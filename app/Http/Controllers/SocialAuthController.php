<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the OAuth Provider authentication page.
     */
    public function redirect(string $provider, Request $request): RedirectResponse
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return redirect()->route('home')->with('error', 'Unsupported authentication provider.');
        }

        $type = $request->query('type', 'client');
        if (!in_array($type, ['client', 'staff', 'hire'], true)) {
            return redirect()->route('home')->with('error', 'Unsupported account type for social sign-in.');
        }

        session(['social_auth_type' => $type]);

        // Check if OAuth client ID is configured for this provider in config/services.php
        $clientId = config("services.{$provider}.client_id");
        $clientSecret = config("services.{$provider}.client_secret");

        if (empty($clientId) || empty($clientSecret)) {
            Log::error("{$provider} social sign-in is unavailable because its OAuth credentials are not configured.");

            return back()->with('error', ucfirst($provider) . ' sign-in is not configured. Please use email and password or contact support.');
        }

        try {
            if ($provider === 'google') {
                return Socialite::driver('google')
                    ->with(['prompt' => 'select_account'])
                    ->redirect();
            }

            if ($provider === 'facebook') {
                return Socialite::driver('facebook')
                    ->scopes(['email', 'public_profile'])
                    ->redirect();
            }

            return Socialite::driver($provider)->redirect();
        } catch (\Throwable $e) {
            Log::error("Unable to start {$provider} social sign-in.", [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', ucfirst($provider) . ' sign-in could not be started. Please try again or use email and password.');
        }
    }

    /**
     * Handle provider callback.
     */
    public function callback(string $provider, Request $request): RedirectResponse
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return redirect()->route('home')->with('error', 'Unsupported authentication provider.');
        }

        $type = session('social_auth_type', 'client');
        if (!in_array($type, ['client', 'staff', 'hire'], true)) {
            $type = 'client';
        }
        $errorRoute = match ($type) {
            'staff' => 'staff.login',
            'hire' => 'hire.create',
            default => 'client.login',
        };

        // If the user cancelled or denied the authentication request
        if ($request->has('error')) {
            $errorDesc = $request->get('error_description', $request->get('error_message', 'Authentication request was cancelled.'));
            Log::info("Social sign-in cancelled or returned error for {$provider}.", [
                'error' => $request->get('error'),
                'description' => $errorDesc,
            ]);

            return redirect()->route($errorRoute)
                ->with('error', ucfirst($provider) . ' sign-in was cancelled.');
        }

        try {
            if ($provider === 'facebook') {
                $socialUser = Socialite::driver('facebook')
                    ->fields(['name', 'first_name', 'last_name', 'email', 'picture.width(400)'])
                    ->user();
            } else {
                $socialUser = Socialite::driver($provider)->user();
            }
        } catch (\Throwable $e) {
            Log::warning("Unable to complete {$provider} social sign-in.", [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route($errorRoute)
                ->with('error', 'Unable to authenticate with ' . ucfirst($provider) . '. Please try again or log in with email.');
        }

        $socialId = trim((string) $socialUser->getId());
        $socialEmail = strtolower(trim((string) $socialUser->getEmail()));
        $socialName = trim((string) ($socialUser->getName() ?: $socialUser->getNickname()));

        if ($socialId === '') {
            return redirect()->route($errorRoute)
                ->with('error', ucfirst($provider) . ' did not return a valid user identity.');
        }

        if ($socialName === '') {
            $socialName = ucfirst($provider) . ' User';
        }

        // If email was not returned (e.g. phone-only Facebook accounts), attempt lookup or assign fallback
        $idColumn = $provider . '_id';
        if ($socialEmail === '' || !filter_var($socialEmail, FILTER_VALIDATE_EMAIL)) {
            $existing = ($type === 'staff')
                ? Professional::where($idColumn, $socialId)->first()
                : User::where($idColumn, $socialId)->first();

            if ($existing && !empty($existing->email)) {
                $socialEmail = strtolower(trim($existing->email));
            } else {
                $socialEmail = "{$provider}_{$socialId}@africrew.ke";
            }
        }

        return $this->authenticateSocialUser($provider, $socialUser, $type, $socialEmail, $socialName);
    }

    /**
     * Process social user authentication or account creation.
     */
    protected function authenticateSocialUser(
        string $provider,
        SocialiteUser $socialUser,
        string $type,
        ?string $emailOverride = null,
        ?string $nameOverride = null
    ): RedirectResponse {
        $idColumn = $provider . '_id';
        $socialId = (string) $socialUser->getId();
        $email = $emailOverride ?: strtolower(trim((string) $socialUser->getEmail()));
        $name = $nameOverride ?: trim((string) ($socialUser->getName() ?: $socialUser->getNickname()));
        if ($name === '') {
            $name = ucfirst($provider) . ' User';
        }

        // Ensure avatar string does not exceed database VARCHAR(255) limits
        $avatar = $socialUser->getAvatar();
        if ($avatar && strlen($avatar) > 255) {
            $avatar = ($provider === 'facebook')
                ? "https://graph.facebook.com/{$socialId}/picture?type=large"
                : substr($avatar, 0, 255);
        }

        if ($type === 'staff') {
            // Authenticate Crew / Professional
            $professional = Professional::where($idColumn, $socialId)
                ->orWhere('email', $email)
                ->first();

            if ($professional && in_array($professional->status, ['rejected', 'suspended', 'deactivated'], true)) {
                return redirect()->route('staff.login')
                    ->with('error', 'This crew account is not currently allowed to sign in. Please contact AfriCrew support.');
            }

            if (!$professional) {
                $baseUsername = Str::slug($name) ?: 'user' . rand(100, 999);
                $username = $baseUsername;
                $counter = 1;
                while (Professional::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter++;
                }

                $professional = Professional::create([
                    'full_name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'phone' => null,
                    'password' => Hash::make(Str::random(24)),
                    'category' => 'Event Ushers',
                    $idColumn => $socialId,
                    'auth_provider' => $provider,
                    'profile_photo' => $avatar,
                    'status' => 'pending',
                    'is_onboarded' => false,
                    'email_verified_at' => now(),
                ]);
            } else {
                $professional->update([
                    'full_name' => $name,
                    $idColumn => $socialId,
                    'auth_provider' => $provider,
                    'profile_photo' => $avatar ?? $professional->profile_photo,
                    'email_verified_at' => $professional->email_verified_at ?? now(),
                ]);
            }

            session()->regenerate();
            session(['professional_id' => $professional->id]);

            // Sync linked User model for Auth::user() header compatibility
            $authUser = User::where('email', $email)->first();
            if (!$authUser) {
                $authUser = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'professional',
                    $idColumn => $socialId,
                    'avatar' => $avatar,
                    'auth_provider' => $provider,
                    'email_verified_at' => now(),
                ]);
            } elseif ($authUser->role === 'professional') {
                $authUser->update([
                    'name' => $name,
                    $idColumn => $socialId,
                    'auth_provider' => $provider,
                    'avatar' => $avatar ?? $authUser->avatar,
                ]);
            } else {
                $authUser->update([
                    $idColumn => $socialId,
                    'auth_provider' => $provider,
                    'avatar' => $avatar ?? $authUser->avatar,
                ]);
            }

            if ($authUser->role === 'professional') {
                Auth::login($authUser, true);
            } elseif (Auth::check()) {
                Auth::logout();
            }

            \App\Services\EmailNotificationService::sendLoginNotification(
                $professional->email,
                $professional->full_name,
                'Crew Member (' . ucfirst($provider) . ')',
                request()?->ip(),
                request()?->userAgent()
            );

            if (!$professional->is_onboarded) {
                return redirect()->route('professional.profile')
                    ->with('success', 'Logged in via ' . ucfirst($provider) . '! Please complete your crew resume profile.');
            }

            return redirect()->route('professional.dashboard')
                ->with('success', 'Successfully logged in with ' . ucfirst($provider) . '!');
        }

        // Authenticate Client User / Hiring Request
        $user = User::where($idColumn, $socialId)
            ->orWhere('email', $email)
            ->first();

        if ($user?->is_admin) {
            return redirect()->route('client.login')
                ->with('error', 'This account is reserved for administration and cannot be used for client sign-in.');
        }

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(24)),
                'role' => 'client',
                $idColumn => $socialId,
                'avatar' => $avatar,
                'auth_provider' => $provider,
                'email_verified_at' => now(),
            ]);
        } else {
            $user->update([
                'name' => $name,
                $idColumn => $socialId,
                'auth_provider' => $provider,
                'avatar' => $avatar ?? $user->avatar,
            ]);
        }

        Auth::login($user, true);

        \App\Services\EmailNotificationService::sendLoginNotification(
            $user->email,
            $user->name,
            'Client (' . ucfirst($provider) . ')',
            request()?->ip(),
            request()?->userAgent()
        );

        if ($type === 'hire') {
            return redirect()->route('hire.create')
                ->with('success', 'Authenticated as ' . $user->name . ' via ' . ucfirst($provider) . '.');
        }

        return redirect()->route('client.dashboard')
            ->with('success', 'Welcome back, ' . $user->name . '! Logged in with ' . ucfirst($provider) . '.');
    }
}
