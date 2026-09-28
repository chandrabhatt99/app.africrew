<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminSubAdminController extends Controller
{
    public function index(): View
    {
        $subadmins = User::where('is_admin', true)->latest()->get();
        $availablePermissions = [
            'manage_requests' => 'Manage Staffing Requests',
            'manage_professionals' => 'Manage Vetted Crew Pool',
            'manage_payments' => 'Payments & Escrow Ops',
            'manage_tickets' => 'Support Desk & Disputes',
            'manage_settings' => 'Platform Settings',
        ];

        return view('admin.subadmins.index', compact('subadmins', 'availablePermissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'permissions' => ['nullable', 'array'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => true,
            'role' => 'admin',
            'permissions' => $data['permissions'] ?? [],
        ]);

        return back()->with('success', "Sub-admin account {$data['name']} created successfully!");
    }

    public function destroy(User $subadmin): RedirectResponse
    {
        if ($subadmin->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        $subadmin->delete();
        return back()->with('success', 'Sub-admin account removed.');
    }
}
