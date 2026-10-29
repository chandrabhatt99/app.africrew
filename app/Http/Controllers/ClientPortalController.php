<?php

namespace App\Http\Controllers;

use App\Models\StaffingRequest;
use App\Models\User;
use App\Services\EmailNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ClientPortalController extends Controller
{
    public function dashboard(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('client.login');
        }

        $requests = StaffingRequest::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->with(['assignments.professional'])
            ->latest()
            ->get();

        $favorites = $user->favorites()->with('reviews')->get();

        return view('client.dashboard', compact('user', 'requests', 'favorites'));
    }

    public function toggleFavorite(\App\Models\Professional $professional): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('client.login')->with('error', 'Please log in to save talent to your favorites.');
        }

        if ($user->favorites()->where('professional_id', $professional->id)->exists()) {
            $user->favorites()->detach($professional->id);
            $message = "Removed {$professional->full_name} from your favorites.";
        } else {
            $user->favorites()->attach($professional->id);
            $message = "Added {$professional->full_name} to your saved favorites!";
        }

        return back()->with('success', $message);
    }

    public function showLogin(): View
    {
        if (Auth::check()) {
            return redirect()->route('client.dashboard');
        }
        return view('client.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            EmailNotificationService::sendLoginNotification(
                $user->email,
                $user->name,
                'Client',
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->intended(route('client.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our client records.',
        ])->onlyInput('email');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('client.dashboard');
        }
        return view('client.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'company_name' => $data['company_name'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'client',
        ]);

        EmailNotificationService::sendClientWelcome($user);

        Auth::login($user);

        return redirect()->route('client.dashboard')->with('success', 'Welcome to AfriCrew Client Portal! Your client account has been created successfully.');
    }

    public function cancelBooking(Request $request, StaffingRequest $staffingRequest): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('client.login');
        }

        // Verify authorization
        if ($staffingRequest->user_id !== $user->id && $staffingRequest->email !== $user->email) {
            return back()->withErrors(['error' => 'Unauthorized action for this booking.']);
        }

        if ($staffingRequest->status === 'completed') {
            return back()->withErrors(['error' => 'Completed bookings cannot be cancelled.']);
        }

        $staffingRequest->update(['status' => 'cancelled']);

        // Send booking cancelled email to all assigned crew members
        EmailNotificationService::sendBookingCancelledToAllCrew($staffingRequest);

        // Send booking cancelled & refund notice email to Client
        EmailNotificationService::sendClientBookingCancelled($staffingRequest);

        return back()->with('success', "Booking #{$staffingRequest->id} for '{$staffingRequest->event_name}' has been cancelled. Your assigned crew has been notified.");
    }

    public function rescheduleBooking(Request $request, StaffingRequest $staffingRequest): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('client.login');
        }

        // Verify authorization
        if ($staffingRequest->user_id !== $user->id && $staffingRequest->email !== $user->email) {
            return back()->withErrors(['error' => 'Unauthorized action for this booking.']);
        }

        $data = $request->validate([
            'event_date' => ['required', 'date', 'after:today'],
        ]);

        $oldDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : 'Original Date';
        $staffingRequest->update(['event_date' => $data['event_date']]);
        $newDate = $staffingRequest->fresh()->event_date->format('M j, Y');

        // Send booking rescheduled email to client
        EmailNotificationService::sendBookingRescheduled($user->email, $staffingRequest, $oldDate, $newDate);

        // Send booking rescheduled email to all assigned crew
        $assignments = $staffingRequest->assignments()->with('professional')->get();
        foreach ($assignments as $assignment) {
            if ($assignment->professional) {
                EmailNotificationService::sendBookingRescheduled($assignment->professional, $staffingRequest, $oldDate, $newDate);
            }
        }

        return back()->with('success', "Booking rescheduled from {$oldDate} to {$newDate}. Confirmation emails have been sent to you and your assigned crew.");
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('client.login');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($data);

        // Send profile updated email notification to client
        EmailNotificationService::sendProfileUpdated($user);

        return back()->with('success', 'Your client profile has been updated successfully!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
