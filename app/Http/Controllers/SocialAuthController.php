<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

        $type = $request->query('type', 'client'); // client, staff, or hire
        session(['social_auth_type' => $type]);

        // Check if OAuth client ID is configured for this provider in config/services.php
        $clientId = config("services.{$provider}.client_id");
        $clientSecret = config("services.{$provider}.client_secret");

        if (empty($clientId) || empty($clientSecret)) {
            // Fallback for development / demo mode when live OAuth keys are not set in .env yet
            return $this->handleMockSocialAuth($provider, $type);
        }

        try {
            if ($provider === 'google') {
                return Socialite::driver('google')
                    ->with(['prompt' => 'select_account'])
                    ->redirect();
            }
            return Socialite::driver($provider)->redirect();
        } catch (\Throwable $e) {
            // If redirect fails (e.g. invalid credentials or network issue), use dev mock fallback
            return $this->handleMockSocialAuth($provider, $type);
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

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Throwable $e) {
            return redirect()->route('client.login')->with('error', 'Unable to authenticate with ' . ucfirst($provider) . '. Please try again or log in with email.');
        }

        return $this->authenticateSocialUser($provider, $socialUser, $type);
    }

    /**
     * Process social user authentication or account creation.
     */
    protected function authenticateSocialUser(string $provider, $socialUser, string $type): RedirectResponse
    {
        $idColumn = $provider . '_id';
        $email = (method_exists($socialUser, 'getEmail') ? $socialUser->getEmail() : null) 
                 ?? ((method_exists($socialUser, 'getId') ? $socialUser->getId() : rand(10000, 99999)) . "@{$provider}.com");
        $name = (method_exists($socialUser, 'getName') ? $socialUser->getName() : null) 
                ?? (ucfirst($provider) . ' Member');
        $avatar = method_exists($socialUser, 'getAvatar') ? $socialUser->getAvatar() : null;
        $socialId = method_exists($socialUser, 'getId') ? $socialUser->getId() : ('soc_' . rand(10000, 99999));

        if ($type === 'staff') {
            // Authenticate Crew / Professional
            $professional = Professional::where($idColumn, $socialId)
                ->orWhere('email', $email)
                ->first();

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
                    'phone' => '+254 700 000 000',
                    'password' => Hash::make(Str::random(24)),
                    'category' => 'Event Ushers',
                    $idColumn => $socialId,
                    'auth_provider' => $provider,
                    'profile_photo' => $avatar,
                    'status' => 'approved',
                    'is_onboarded' => false,
                    'email_verified_at' => now(),
                ]);
            } else {
                $professional->update([
                    $idColumn => $socialId,
                    'auth_provider' => $provider,
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
            } else {
                $authUser->update([
                    $idColumn => $socialId,
                    'auth_provider' => $provider,
                    'avatar' => $avatar ?? $authUser->avatar,
                ]);
            }
            Auth::login($authUser, true);

            if (!$professional->is_onboarded) {
                return redirect()->route('professional.onboarding')
                    ->with('success', 'Logged in via ' . ucfirst($provider) . '! Please complete your crew resume profile.');
            }

            return redirect()->route('professional.dashboard')
                ->with('success', 'Successfully logged in with ' . ucfirst($provider) . '!');
        }

        // Authenticate Client User / Hiring Request
        $user = User::where($idColumn, $socialId)
            ->orWhere('email', $email)
            ->first();

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
                $idColumn => $socialId,
                'auth_provider' => $provider,
                'avatar' => $avatar ?? $user->avatar,
            ]);
        }

        Auth::login($user, true);

        if ($type === 'hire') {
            return redirect()->route('hire.create')
                ->with('success', 'Authenticated as ' . $user->name . ' via ' . ucfirst($provider) . '.');
        }

        return redirect()->route('client.dashboard')
            ->with('success', 'Welcome back, ' . $user->name . '! Logged in with ' . ucfirst($provider) . '.');
    }

    /**
     * Fallback mock social authentication for dev testing when API keys are not in .env.
     */
    protected function handleMockSocialAuth(string $provider, string $type): RedirectResponse
    {
        $mockId = 'demo_' . $provider . '_' . rand(100, 999);
        $mockEmail = 'demo.' . $provider . '@africrew.com';
        $mockName = ucfirst($provider) . ' Account User';

        $mockUser = new class($mockId, $mockEmail, $mockName) {
            private $id;
            private $email;
            private $name;

            public function __construct($id, $email, $name) {
                $this->id = $id;
                $this->email = $email;
                $this->name = $name;
            }
            public function getId() { return $this->id; }
            public function getEmail() { return $this->email; }
            public function getName() { return $this->name; }
            public function getAvatar() { return null; }
        };

        return $this->authenticateSocialUser($provider, $mockUser, $type);
    }
}
