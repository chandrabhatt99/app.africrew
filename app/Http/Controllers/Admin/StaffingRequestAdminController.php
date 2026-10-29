<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\StaffingAssignment;
use App\Models\StaffingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffingRequestAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.requests.index', [
            'requests' => StaffingRequest::latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.requests.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'event_name' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:100'],
            'staff_count' => ['required', 'integer', 'min:1', 'max:10000'],
            'event_date' => ['required', 'date'],
            'shift_duration' => ['nullable', 'string'],
            'start_time' => ['nullable', 'string'],
            'end_time' => ['nullable', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:new,under_review,staff_matching,shortlisted,assigned,completed,cancelled'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $data['start_time'] = $data['start_time'] ?? '09:00';
        $data['end_time'] = $data['end_time'] ?? '17:00';

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('staffing-requests', 'public');
        }

        $staffingRequest = StaffingRequest::create($data);

        return redirect()->route('admin.requests.show', $staffingRequest)
            ->with('success', 'Staffing request created successfully.');
    }

    public function show(StaffingRequest $staffingRequest): View
    {
        $professionals = Professional::where('status', 'approved')
            ->where('category', $staffingRequest->category)
            ->orderByDesc('experience_years')
            ->get();

        return view('admin.requests.show', compact('staffingRequest', 'professionals'));
    }

    public function updateStatus(Request $request, StaffingRequest $staffingRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,under_review,staff_matching,shortlisted,assigned,completed,cancelled'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($staffingRequest, $data) {
            $oldStatus = $staffingRequest->status;
            $staffingRequest->update(['status' => $data['status']]);

            // If project is marked COMPLETED ('done') and client payment is in escrow ('paid_to_admin')
            if ($data['status'] === 'completed' && $oldStatus !== 'completed') {
                if ($staffingRequest->payment_status === 'paid_to_admin' && $staffingRequest->payment_amount > 0) {
                    $assignments = $staffingRequest->assignments()->with('professional')->get();
                    if ($assignments->isNotEmpty()) {
                        $perCrewAmount = round($staffingRequest->payment_amount / $assignments->count(), 2);
                        foreach ($assignments as $assignment) {
                            $prof = $assignment->professional;
                            if ($prof) {
                                // Move from pending escrow to available wallet balance
                                $deductPending = min($prof->wallet_pending, $perCrewAmount);
                                $prof->decrement('wallet_pending', $deductPending);
                                $prof->increment('wallet_balance', $perCrewAmount);
                            }
                        }
                    }
                    $staffingRequest->update(['payment_status' => 'released_to_crew']);
                }
            }
        });

        if ($data['status'] === 'completed') {
            \App\Services\EmailNotificationService::sendEventCompleteToClient($staffingRequest);
            \App\Services\EmailNotificationService::sendCrewGigCompletedToAllCrew($staffingRequest);
        }

        if ($data['status'] === 'cancelled') {
            \App\Services\EmailNotificationService::sendBookingCancelledToAllCrew($staffingRequest);
            \App\Services\EmailNotificationService::sendClientBookingCancelled($staffingRequest);
        }

        return back()->with('success', 'Request status updated successfully.');
    }

    public function reschedule(Request $request, StaffingRequest $staffingRequest): RedirectResponse
    {
        $data = $request->validate([
            'event_date' => ['required', 'date'],
        ]);

        $oldDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : 'Original Date';
        $staffingRequest->update(['event_date' => $data['event_date']]);
        $newDate = $staffingRequest->fresh()->event_date->format('M j, Y');

        // Notify client
        $clientEmail = $staffingRequest->email ?: $staffingRequest->user?->email;
        if ($clientEmail) {
            \App\Services\EmailNotificationService::sendBookingRescheduled($clientEmail, $staffingRequest, $oldDate, $newDate);
        }

        // Notify assigned crew members
        $assignments = $staffingRequest->assignments()->with('professional')->get();
        foreach ($assignments as $assignment) {
            if ($assignment->professional) {
                \App\Services\EmailNotificationService::sendBookingRescheduled($assignment->professional, $staffingRequest, $oldDate, $newDate);
            }
        }

        return back()->with('success', "Booking rescheduled from {$oldDate} to {$newDate}. Notifications sent.");
    }

    public function assign(Request $request, StaffingRequest $staffingRequest): RedirectResponse
    {
        $data = $request->validate([
            'professional_id' => ['required', 'exists:professionals,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $assignedProf = null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($staffingRequest, $data, &$assignedProf) {
            $assignment = StaffingAssignment::firstOrCreate(
                [
                    'staffing_request_id' => $staffingRequest->id,
                    'professional_id' => $data['professional_id'],
                ],
                [
                    'status' => 'assigned',
                    'notes' => $data['notes'] ?? null,
                ]
            );

            if ($staffingRequest->status !== 'completed') {
                $staffingRequest->update(['status' => 'assigned']);
            }

            $assignedProf = Professional::find($data['professional_id']);

            // If the client has already paid to Admin, allocate pending escrow to newly assigned crew
            if ($staffingRequest->payment_status === 'paid_to_admin' && $staffingRequest->payment_amount > 0) {
                if ($assignedProf) {
                    $totalAssigned = $staffingRequest->assignments()->count();
                    $perCrewAmount = round($staffingRequest->payment_amount / max($totalAssigned, 1), 2);
                    $assignedProf->increment('wallet_pending', $perCrewAmount);
                }
            }
        });

        if ($assignedProf) {
            // Send email to crew member
            \App\Services\EmailNotificationService::sendGigAssignedToCrew($assignedProf, $staffingRequest);
            \App\Services\EmailNotificationService::sendCrewBooked($assignedProf, $staffingRequest);
            // Send email to client
            \App\Services\EmailNotificationService::sendCrewConfirmedToClient($staffingRequest, $assignedProf->full_name);
        }

        return back()->with('success', 'Professional assigned successfully.');
    }
}
