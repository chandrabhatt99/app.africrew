<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffAuthController extends Controller
{
    /**
     * Display the staff login view.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('professional_id')) {
            return redirect()->route('professional.dashboard');
        }

        return view('staff.login');
    }

    /**
     * Handle staff login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $professional = Professional::where('email', strtolower(trim($credentials['email'])))->first();

        if (!$professional || !Hash::check($credentials['password'], $professional->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our crew records.',
            ])->onlyInput('email');
        }

        if ($professional->status === 'rejected') {
            return back()->with([
                'status_rejected' => true,
                'rejection_reason' => $professional->interview_notes,
            ])->withErrors([
                'email' => 'Your profile is rejected. Your crew application was not approved by the AfriCrew vetting team.',
            ])->onlyInput('email');
        }

        if ($professional->status === 'suspended' || $professional->status === 'deactivated') {
            return back()->withErrors([
                'email' => 'Your crew account is currently suspended or deactivated. Please contact administration.',
            ])->onlyInput('email');
        }

        if (is_null($professional->email_verified_at) && $professional->status !== 'approved') {
            return redirect()->route('staff.verify.notice')->with([
                'pending_email' => $professional->email,
                'submitted_name' => $professional->full_name,
                'submitted_email' => $professional->email,
                'submitted_category' => $professional->category,
                'ref_id' => 'AFR-CRW-' . str_pad($professional->id, 5, '0', STR_PAD_LEFT),
                'error' => 'Please verify your email address before continuing to your crew profile.'
            ]);
        }

        // Auto-verify email upon admin approval if not already stamped
        if (is_null($professional->email_verified_at)) {
            $professional->update([
                'email_verified_at' => now(),
                'verification_token' => null,
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('professional_id', $professional->id);

        \App\Services\EmailNotificationService::sendLoginNotification(
            $professional->email,
            $professional->full_name,
            'Crew Member',
            $request->ip(),
            $request->userAgent()
        );

        if ($professional->status === 'pending' || !$professional->is_onboarded) {
            return redirect()->route('professional.profile')->with('info', 'Continue completing your crew profile. You can request review once it is at least 60% complete.');
        }

        return redirect()->route('professional.dashboard')->with('success', "Welcome back, {$professional->full_name}!");
    }

    /**
     * Check crew application & interview status by email
     */
    public function checkStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'status_email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->input('status_email')));
        $professional = Professional::where('email', $email)->first();

        if (!$professional) {
            return back()->with('status_check_error', "No crew application found for '{$email}'. Please ensure you registered via Join AfriCrew.")->withInput();
        }

        if ($professional->status === 'approved') {
            return back()->with([
                'status_approved' => true,
                'status_check_success' => "Great news! Your profile is APPROVED. You can now log in below with your password.",
            ])->withInput();
        }

        if ($professional->status === 'rejected') {
            return back()->with([
                'status_rejected' => true,
                'rejection_reason' => $professional->interview_notes,
                'status_check_error' => 'Your profile is rejected. Your crew application was not approved by the AfriCrew vetting team.',
            ])->withInput();
        }

        if ($professional->interview_status === 'scheduled') {
            return back()->with([
                'status_interview_scheduled' => true,
                'interview_date' => $professional->interview_date ? $professional->interview_date->format('l, F j, Y') : 'Date to be confirmed',
                'interview_time' => $professional->interview_time ?: '10:00 AM',
                'interviewer_name' => $professional->interviewer_name ?: 'AfriCrew Vetting Team',
                'interview_link' => $professional->interview_link,
                'interview_notes' => $professional->interview_notes,
                'candidate_name' => $professional->full_name,
                'status_check_info' => 'Your vetting interview is scheduled! Details are displayed below.',
            ])->withInput();
        }

        return back()->with([
            'status_pending_unscheduled' => true,
            'candidate_name' => $professional->full_name,
            'status_check_info' => "Application Received! Thank you {$professional->full_name}. Your profile is under review and our team will schedule your interview soon.",
        ])->withInput();
    }

    /**
     * Display crew application pending / review notice screen.
     */
    public function showVerifyNotice(Request $request): View
    {
        $email = session('submitted_email') ?? session('pending_email');
        $professional = null;
        if ($email) {
            $professional = Professional::where('email', $email)->first();
        }

        return view('staff.verify-notice', compact('professional'));
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        $email = $request->input('email', session('pending_email'));
        $professional = Professional::where('email', $email)->first();

        if (!$professional) {
            return back()->with('error', 'No crew account found for that email address.');
        }

        if ($professional->email_verified_at) {
            return redirect()->route('staff.login')->with('info', 'Your email is already verified. Please log in.');
        }

        $newToken = \Illuminate\Support\Str::random(40);
        $professional->update(['verification_token' => $newToken]);

        session()->put('pending_email', $professional->email);
        session()->put('verification_token', $newToken);

        if (!\App\Services\EmailNotificationService::sendApplicationReceivedToCrew($professional)) {
            return back()->with('error', 'We could not send the verification email. Please try again or contact AfriCrew support.');
        }

        return back()->with('success', "A fresh verification link has been sent to {$professional->email}.");
    }

    public function updateEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'old_email' => ['required', 'email'],
            'new_email' => ['required', 'email', 'unique:professionals,email'],
        ]);

        $professional = Professional::where('email', $request->old_email)->first();

        if (!$professional) {
            return back()->with('error', 'Original email address record not found.');
        }

        $newToken = \Illuminate\Support\Str::random(40);
        $professional->update([
            'email' => strtolower(trim($request->new_email)),
            'verification_token' => $newToken,
            'email_verified_at' => null,
        ]);

        session()->put('pending_email', $professional->email);
        session()->put('verification_token', $newToken);

        if (!\App\Services\EmailNotificationService::sendApplicationReceivedToCrew($professional)) {
            return back()->with('error', 'Your email was updated, but we could not send the verification message. Please try again.');
        }

        return back()->with('success', "Your email address has been updated to {$professional->email}. A new verification link has been dispatched!");
    }

    /**
     * Verify email address via token link.
     */
    public function verifyEmail($token): RedirectResponse
    {
        $professional = Professional::where('verification_token', $token)->first();

        if (!$professional) {
            return redirect()->route('staff.login')->with('error', 'Invalid or expired email verification link.');
        }

        $professional->update([
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);

        session()->regenerate();
        session()->put('professional_id', $professional->id);

        return redirect()->route('professional.profile')->with('success', 'Email verified successfully! Please complete your Profile & Resume Builder to request account review.');
    }

    /**
     * Log out staff member.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('professional_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login')->with('success', 'You have been logged out of the Crew Portal.');
    }
}
