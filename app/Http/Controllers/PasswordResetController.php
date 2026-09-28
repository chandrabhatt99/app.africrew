<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        if ($user) {
            $token = Str::random(60);
            session(['password_reset_token_' . $user->email => $token]);
            $msg = "Password reset link token generated for {$user->email}. (Demo Token: {$token})";
        } else {
            $msg = "If an account exists with that email, a password reset link has been dispatched.";
        }

        return back()->with('success', $msg);
    }
}
