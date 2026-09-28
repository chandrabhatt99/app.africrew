<?php

namespace App\Http\Controllers;

use App\Models\StaffingRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ClientPortalController extends Controller
{
    public function dashboard(): View
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

        Auth::login($user);

        return redirect()->route('client.dashboard')->with('success', 'Welcome to AfriCrew Client Portal! Your client account has been created successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
