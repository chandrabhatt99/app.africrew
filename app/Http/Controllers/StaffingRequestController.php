<?php

namespace App\Http\Controllers;

use App\Models\StaffingAssignment;
use App\Models\StaffingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffingRequestController extends Controller
{
    public function create(Request $request)
    {
        $idsParam = $request->query('requested_professional_ids') ?? $request->query('ids') ?? $request->query('staff_id') ?? $request->query('professional_id');
        $requestedProfessionals = collect();

        if ($idsParam) {
            $idsArray = is_array($idsParam) ? $idsParam : explode(',', $idsParam);
            $idsArray = array_filter(array_map('trim', $idsArray));
            
            if (!empty($idsArray)) {
                $requestedProfessionals = \App\Models\Professional::where('status', 'approved')
                    ->whereIn('id', $idsArray)
                    ->get();
            }
        }

        $requestedProfessional = $requestedProfessionals->first();
        if (!$requestedProfessional) {
            $requestedProfessional = \App\Models\Professional::where('status', 'approved')->first();
        }

        $categories = \App\Models\Category::where('is_active', true)->get();

        return view('hire-staff', compact('requestedProfessional', 'requestedProfessionals', 'categories'));
    }

    public function createForProfessional(\App\Models\Professional $professional)
    {
        $loggedProfId = session('professional_id');
        $currentUser = auth()->user();
        if (($loggedProfId && $loggedProfId == $professional->id) || ($currentUser && strtolower($currentUser->email) === strtolower($professional->email))) {
            return redirect()->route('crew.show', $professional)->with('error', 'You cannot hire yourself for a staffing request.');
        }

        $categories = \App\Models\Category::where('is_active', true)->get();
        $requestedProfessionals = collect([$professional]);

        return view('hire-staff', [
            'requestedProfessional' => $professional,
            'requestedProfessionals' => $requestedProfessionals,
            'categories' => $categories
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'event_name' => ['required', 'string', 'max:200'],
            'event_type' => ['nullable', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'staff_count' => ['required', 'integer', 'min:1', 'max:10000'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'dates' => ['nullable', 'array'],
            'shift_duration' => ['nullable', 'string'],
            'start_time' => ['nullable', 'string'],
            'end_time' => ['nullable', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'rate_type' => ['nullable', 'string', 'in:fixed,negotiable'],
            'currency' => ['nullable', 'string', 'in:USD,KES'],
            'attachment' => ['nullable', 'file', 'max:10240'],
            'password' => [Auth::check() ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'requested_professional_id' => ['nullable'],
            'requested_professional_ids' => ['nullable'],
        ]);

        $data['start_time'] = $data['start_time'] ?? '09:00';
        $data['end_time'] = $data['end_time'] ?? '17:00';

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('staffing-requests', 'public');
        }

        // Collect professional IDs (array or single)
        $profIds = [];
        if (!empty($data['requested_professional_ids'])) {
            $profIds = is_array($data['requested_professional_ids']) 
                ? $data['requested_professional_ids'] 
                : explode(',', $data['requested_professional_ids']);
        } elseif (!empty($data['requested_professional_id'])) {
            $profIds = [$data['requested_professional_id']];
        }
        $profIds = array_unique(array_filter(array_map('trim', $profIds)));

        unset($data['requested_professional_id'], $data['requested_professional_ids']);
        $password = $data['password'] ?? null;
        unset($data['password'], $data['password_confirmation']);

        // Client Account registration & login
        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        } else {
            $user = User::where('email', $data['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $data['full_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'company_name' => $data['company_name'] ?? null,
                    'password' => Hash::make($password),
                    'role' => 'client',
                ]);
            }
            Auth::login($user);
            $data['user_id'] = $user->id;
        }

        // If multiple professionals selected, adjust staff_count if less than selection count
        if (count($profIds) > 1 && $data['staff_count'] < count($profIds)) {
            $data['staff_count'] = count($profIds);
        }

        $staffingRequest = StaffingRequest::create($data);

        if (!empty($profIds)) {
            $staffingRequest->update(['status' => 'assigned']);

            foreach ($profIds as $profId) {
                $prof = \App\Models\Professional::find($profId);
                if (!$prof) continue;

                StaffingAssignment::create([
                    'staffing_request_id' => $staffingRequest->id,
                    'professional_id' => $prof->id,
                    'status' => 'assigned',
                    'notes' => 'Direct client booking request submitted via crew profile / multi-team basket.',
                ]);

                $budgetAmount = $staffingRequest->budget ?: (($staffingRequest->currency === 'KES') ? ($prof->full_day_rate ?: 3500) : 100);
                $proposal = \App\Models\Proposal::create([
                    'staffing_request_id' => $staffingRequest->id,
                    'professional_id' => $prof->id,
                    'custom_quote_amount' => $budgetAmount,
                    'travel_fee' => 0,
                    'status' => 'pending',
                    'notes' => "Initial " . ($staffingRequest->rate_type === 'negotiable' ? 'Negotiable' : 'Fixed') . " Price Booking Request from " . $staffingRequest->full_name,
                ]);

                $receiverId = User::where('email', $prof->email)->first()?->id ?? User::where('is_admin', true)->first()?->id;

                $msgObj = \App\Models\Message::create([
                    'sender_id' => $staffingRequest->user_id,
                    'receiver_id' => $receiverId,
                    'staffing_request_id' => $staffingRequest->id,
                    'proposal_id' => $proposal->id,
                    'message' => "👋 Hi {$prof->full_name}! Client {$staffingRequest->full_name} submitted a team booking request for '{$staffingRequest->event_name}' with a " . ($staffingRequest->rate_type === 'negotiable' ? 'Negotiable Price' : 'Fixed Rate') . " of {$staffingRequest->currency} " . number_format($budgetAmount, 2) . ". Let's discuss details and finalize pricing!",
                    'negotiated_price' => $budgetAmount,
                    'negotiation_status' => 'proposed',
                    'price_breakdown' => [
                        'base_rate' => $budgetAmount,
                        'rate_type' => $staffingRequest->rate_type ?? 'fixed',
                        'currency' => $staffingRequest->currency ?? 'USD',
                    ],
                ]);

                try {
                    event(new \App\Events\NewMessageSent($msgObj));
                } catch (\Throwable $e) {}
            }
        }

        return redirect()->route('client.dashboard')->with('success', 'Your staffing request and client portal account have been created successfully!');
    }

    public function success(): View
    {
        return view('hire-success');
    }
}
