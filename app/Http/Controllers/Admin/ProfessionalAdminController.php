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

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $professionals = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'total' => Professional::count(),
            'approved' => Professional::where('status', 'approved')->count(),
            'pending' => Professional::where('status', 'pending')->count(),
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

    public function updateStatus(Request $request, Professional $professional): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,suspended,deactivated'],
            'interview_date' => ['nullable', 'date'],
            'interview_time' => ['nullable', 'string', 'max:50'],
            'interviewer_name' => ['nullable', 'string', 'max:150'],
            'interview_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['status'] === 'approved') {
            if (empty($data['interview_date']) || empty($data['interview_time']) || empty($data['interviewer_name'])) {
                return back()->withErrors([
                    'interview' => 'Before approving a crew member, you must specify the Interview Date, Interview Time, and Interviewer Name.'
                ])->withInput();
            }
            $data['interview_status'] = 'completed';
        }

        $professional->update($data);

        $statusMsg = match ($data['status']) {
            'approved' => "Professional APPROVED! Interview recorded for {$professional->interview_date?->format('M d, Y')} at {$professional->interview_time} by {$professional->interviewer_name}.",
            'deactivated', 'rejected', 'suspended' => 'Professional account DEACTIVATED / FLAGGED as fraud/inactive.',
            default => 'Professional status updated to Pending review.'
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
}
