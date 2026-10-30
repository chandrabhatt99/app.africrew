<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EmailNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showForgot(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();
        $prof = \App\Models\Professional::where('email', $request->email)->first();

        if ($user || $prof) {
            $token = Str::random(60);
            $email = $user ? $user->email : $prof->email;
            $firstName = $user 
                ? explode(' ', trim($user->name ?: 'Member'))[0] 
                : ($prof->first_name ?: explode(' ', trim($prof->full_name))[0]);

            // Store in password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            // Also keep token in session for local testing
            session(['password_reset_token_' . $email => $token]);

            $resetLink = route('password.reset', ['token' => $token, 'email' => $email]);

            // Dispatch Password Reset Email
            EmailNotificationService::sendPasswordReset($email, $firstName, $resetLink);

            $msg = "Password reset instructions have been sent to {$email}. Please check your inbox.";
        } else {
            $msg = "If an account exists with that email, a password reset link has been dispatched.";
        }

        return back()->with('success', $msg);
    }

    public function showReset(Request $request, string $token): View
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        $sessionToken = session('password_reset_token_' . $request->email);

        $isValid = false;
        if ($record && Hash::check($request->token, $record->token)) {
            $isValid = true;
        } elseif ($sessionToken && $sessionToken === $request->token) {
            $isValid = true;
        }

        if (!$isValid) {
            return back()->withErrors(['email' => 'This password reset link is invalid or has expired.'])->withInput();
        }

        $user = User::where('email', $request->email)->first();
        $prof = \App\Models\Professional::where('email', $request->email)->first();

        if (!$user && !$prof) {
            return back()->withErrors(['email' => 'Account not found.'])->withInput();
        }

        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        if ($prof) {
            $prof->update([
                'password' => Hash::make($request->password),
            ]);
        }

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        session()->forget('password_reset_token_' . $request->email);

        $loginRoute = ($prof && !$user) ? route('staff.login') : route('client.login');
        return redirect($loginRoute)->with('success', 'Your password has been updated successfully! Please log in with your new password.');
    }
}
