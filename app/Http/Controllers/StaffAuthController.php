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

        $professional = Professional::where('email', $credentials['email'])->first();

        if (!$professional || !Hash::check($credentials['password'], $professional->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our crew records.',
            ])->onlyInput('email');
        }

        if ($professional->status === 'rejected' || $professional->status === 'suspended') {
            return back()->withErrors([
                'email' => 'Your crew account is currently suspended or not approved.',
            ])->onlyInput('email');
        }

        if (is_null($professional->email_verified_at)) {
            return redirect()->route('staff.verify.notice')->with([
                'pending_email' => $professional->email,
                'verification_token' => $professional->verification_token,
                'error' => 'Please click the verification link sent to your email before logging in.'
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('professional_id', $professional->id);

        if (!$professional->is_onboarded) {
            return redirect()->route('professional.onboarding')->with('info', 'Please complete your crew resume wizard to access the portal.');
        }

        return redirect()->route('professional.dashboard')->with('success', "Welcome back, {$professional->full_name}!");
    }

    /**
     * Display email verification pending notice screen.
     */
    public function showVerifyNotice(): View
    {
        return view('staff.verify-notice');
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

        return redirect()->route('professional.onboarding')->with('success', 'Email verified successfully! Please complete your crew profile & resume wizard to proceed.');
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
