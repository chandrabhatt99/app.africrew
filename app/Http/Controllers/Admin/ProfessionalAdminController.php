<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfessionalAdminController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Professional::query();

        if ($status === 'interview_scheduled') {
            $query->where('interview_status', 'scheduled');
        } elseif (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('interviewer_name', 'like', "%{$search}%");
            });
        }

        $professionals = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'total' => Professional::count(),
            'approved' => Professional::where('status', 'approved')->count(),
            'pending' => Professional::where('status', 'pending')->count(),
            'interview_scheduled' => Professional::where('interview_status', 'scheduled')->count(),
            'deactivated' => Professional::whereIn('status', ['rejected', 'suspended', 'deactivated'])->count(),
        ];

        return view('admin.professionals.index', compact('professionals', 'status', 'search', 'counts'));
    }

    public function create(): View
    {
        return view('admin.professionals.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:professionals,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'min:8'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'skills' => ['nullable', 'string', 'max:3000'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:80'],
            'about' => ['nullable', 'string', 'max:5000'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'half_day_rate' => ['nullable', 'numeric', 'min:0'],
            'full_day_rate' => ['nullable', 'numeric', 'min:0'],
            'availability' => ['required', 'string'],
            'status' => ['required', 'in:pending,approved,rejected,suspended,deactivated'],
            'languages' => ['nullable', 'string', 'max:500'],
            'preferred_locations' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'max:5120'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'government_id' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['languages'] = !empty($data['languages'])
            ? array_values(array_filter(array_map('trim', explode(',', $data['languages']))))
            : [];
        $data['preferred_locations'] = !empty($data['preferred_locations'])
            ? array_values(array_filter(array_map('trim', explode(',', $data['preferred_locations']))))
            : [];

        foreach (['profile_photo', 'resume', 'government_id'] as $file) {
            if ($request->hasFile($file)) {
                $data[$file] = $request->file($file)->store("professionals/{$file}", 'public');
            }
        }

        $professional = Professional::create($data);

        return redirect()->route('admin.professionals.show', $professional)
            ->with('success', 'Crew member added and onboarded successfully.');
    }

    public function edit(Professional $professional): View
    {
        $categories = \App\Models\Category::where('is_active', true)->get();
        return view('admin.professionals.edit', compact('professional', 'categories'));
    }

    public function update(Request $request, Professional $professional): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:professionals,email,' . $professional->id],
            'phone' => ['required', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'skills' => ['nullable', 'string', 'max:3000'],
            'services' => ['nullable', 'array'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:80'],
            'about' => ['nullable', 'string', 'max:5000'],
            'my_update' => ['nullable', 'string', 'max:255'],
            'lets_connect' => ['nullable', 'string', 'max:255'],
            'highlight_quote' => ['nullable', 'string', 'max:1000'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'half_day_rate' => ['nullable', 'numeric', 'min:0'],
            'full_day_rate' => ['nullable', 'numeric', 'min:0'],
            'one_day_rate' => ['nullable', 'numeric', 'min:0'],
            'two_day_rate' => ['nullable', 'numeric', 'min:0'],
            'rehearsal_rate' => ['nullable', 'numeric', 'min:0'],
            'availability' => ['required', 'string'],
            'status' => ['required', 'in:pending,approved,rejected,suspended,deactivated'],
            'languages' => ['nullable'],
            'preferred_locations' => ['nullable'],
            'profile_photo' => ['nullable', 'image', 'max:5120'],
            'cover_photo' => ['nullable', 'image', 'max:5120'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'government_id' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        if (!empty($data['languages']) && is_string($data['languages'])) {
            $data['languages'] = array_values(array_filter(array_map('trim', explode(',', $data['languages']))));
        }

        if (!empty($data['preferred_locations']) && is_string($data['preferred_locations'])) {
            $data['preferred_locations'] = array_values(array_filter(array_map('trim', explode(',', $data['preferred_locations']))));
        }

        if (!empty($data['services']) && is_array($data['services'])) {
            $cleanedServices = [];
            foreach ($data['services'] as $item) {
                if (is_string($item)) {
                    $decoded = json_decode($item, true);
                    if (is_array($decoded) && !empty($decoded['title'])) $cleanedServices[] = $decoded;
                } elseif (is_array($item) && !empty($item['title'])) {
                    $cleanedServices[] = $item;
                }
            }
            $data['services'] = $cleanedServices;
        }

        foreach (['profile_photo', 'cover_photo', 'resume', 'government_id'] as $file) {
            if ($request->hasFile($file)) {
                $data[$file] = $request->file($file)->store("professionals/{$file}", 'public');
            }
        }

        $professional->update($data);

        return redirect()->route('admin.professionals.show', $professional)
            ->with('success', 'Crew member details and capabilities updated successfully.');
    }

    public function show(Professional $professional): View
    {
        return view('admin.professionals.show', compact('professional'));
    }

    public function scheduleInterview(Request $request, Professional $professional): RedirectResponse
    {
        $data = $request->validate([
            'interview_date' => ['required', 'date'],
            'interview_time' => ['required', 'string', 'max:100'],
            'interviewer_name' => ['required', 'string', 'max:150'],
            'interview_link' => ['nullable', 'string', 'max:255'],
            'interview_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['interview_status'] = 'scheduled';
        if ($professional->status !== 'approved') {
            $data['status'] = 'pending';
        }

        $professional->update($data);

        // Send interview invitation email to crew member
        \App\Services\EmailNotificationService::sendInterviewScheduledToCrew($professional);

        $dateFormatted = $professional->interview_date ? $professional->interview_date->format('M d, Y') : $data['interview_date'];

        return back()->with('success', "Interview scheduled for {$professional->full_name} on {$dateFormatted} at {$professional->interview_time} with {$professional->interviewer_name}. Email invitation dispatched!");
    }

    public function approve(Request $request, Professional $professional): RedirectResponse
    {
        $data = [
            'status' => 'approved',
            'interview_status' => 'completed',
            'email_verified_at' => $professional->email_verified_at ?? now(),
            'verification_token' => null,
        ];

        if ($request->filled('interview_notes')) {
            $data['interview_notes'] = $request->input('interview_notes');
        }

        $professional->update($data);

        // Send approval emails to crew member
        \App\Services\EmailNotificationService::sendProfileLiveToCrew($professional);
        \App\Services\EmailNotificationService::sendCrewApproved($professional);

        return back()->with('success', "Crew member {$professional->full_name} has been APPROVED! They can now log in to the Crew Portal.");
    }

    public function reject(Request $request, Professional $professional): RedirectResponse
    {
        $reason = $request->input('rejection_reason', $request->input('interview_notes'));

        $data = [
            'status' => 'rejected',
            'interview_status' => 'completed',
        ];

        if (!empty($reason)) {
            $data['interview_notes'] = $reason;
        }

        $professional->update($data);

        // Send rejection email to crew member
        \App\Services\EmailNotificationService::sendCrewRejected($professional, $reason);

        return back()->with('success', "Crew member {$professional->full_name} has been REJECTED. They will see the rejection status upon login and are barred from portal access.");
    }

    public function updateStatus(Request $request, Professional $professional): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,suspended,deactivated'],
            'interview_date' => ['nullable', 'date'],
            'interview_time' => ['nullable', 'string', 'max:50'],
            'interviewer_name' => ['nullable', 'string', 'max:150'],
            'interview_link' => ['nullable', 'string', 'max:255'],
            'interview_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['status'] === 'approved') {
            $data['interview_status'] = 'completed';
            $data['email_verified_at'] = $professional->email_verified_at ?? now();
            $data['verification_token'] = null;
        } elseif ($data['status'] === 'rejected') {
            $data['interview_status'] = 'completed';
        }

        $professional->update($data);

        if ($data['status'] === 'approved') {
            \App\Services\EmailNotificationService::sendProfileLiveToCrew($professional);
            \App\Services\EmailNotificationService::sendCrewApproved($professional);
        } elseif ($data['status'] === 'rejected') {
            $reason = !empty($data['interview_notes']) 
                ? $data['interview_notes'] 
                : 'Application requirements and identity validation standards were not met.';
            \App\Services\EmailNotificationService::sendCrewRejected($professional, $reason);
        }

        $statusMsg = match ($data['status']) {
            'approved' => "Professional {$professional->full_name} APPROVED! Account is now active for gig bookings.",
            'rejected' => "Professional {$professional->full_name} REJECTED. Account is barred from login.",
            'deactivated', 'suspended' => "Professional {$professional->full_name} account flagged as {$data['status']}.",
            default => "Professional status updated to Pending review."
        };

        return back()->with('success', $statusMsg);
    }

    public function destroy(Professional $professional): RedirectResponse
    {
        $name = $professional->full_name;
        $professional->delete();

        return redirect()->route('admin.professionals.index')
            ->with('success', "Staff record for '{$name}' has been permanently deleted from system.");
    }

    public function toggleRefereeVerify(Request $request, Professional $professional, int $index): RedirectResponse
    {
        $referees = is_array($professional->referees) ? $professional->referees : [];
        if (isset($referees[$index])) {
            $item = is_string($referees[$index]) ? json_decode($referees[$index], true) : $referees[$index];
            $currentStatus = strtolower($item['status'] ?? '');
            if ($currentStatus === 'verified' || !empty($item['is_confirmed'])) {
                $item['status'] = 'unverified';
                $item['is_confirmed'] = false;
            } else {
                $item['status'] = 'verified';
                $item['is_confirmed'] = true;
                $item['verified_at'] = now()->toDateTimeString();
                if (!isset($item['show_on_cv'])) {
                    $item['show_on_cv'] = true;
                }
            }
            $referees[$index] = $item;
            $professional->update(['referees' => $referees]);
            return back()->with('success', 'Referee verification status updated.');
        }
        return back()->with('error', 'Referee not found.');
    }

    public function toggleRefereeVisibility(Request $request, Professional $professional, int $index): RedirectResponse
    {
        $referees = is_array($professional->referees) ? $professional->referees : [];
        if (isset($referees[$index])) {
            $item = is_string($referees[$index]) ? json_decode($referees[$index], true) : $referees[$index];
            $item['show_on_cv'] = empty($item['show_on_cv']);
            $referees[$index] = $item;
            $professional->update(['referees' => $referees]);
            return back()->with('success', 'Referee digital CV visibility updated.');
        }
        return back()->with('error', 'Referee not found.');
    }
}
