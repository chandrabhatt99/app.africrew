<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffingRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminClientController extends Controller
{
    /**
     * Display a listing of all registered clients and clients with bookings.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'client')
            ->orWhereIn('id', StaffingRequest::whereNotNull('user_id')->pluck('user_id'))
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $clients = $query->paginate(15)->withQueryString();

        // Attach statistics to each client
        foreach ($clients as $client) {
            $client->total_requests_count = StaffingRequest::where('user_id', $client->id)
                ->orWhere('email', $client->email)
                ->count();

            $client->total_spent = StaffingRequest::where(function ($q) use ($client) {
                    $q->where('user_id', $client->id)->orWhere('email', $client->email);
                })
                ->whereIn('payment_status', ['paid_to_admin', 'released_to_crew'])
                ->sum('payment_amount');
        }

        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Display single client profile and full booking timeline/history.
     */
    public function show(User $client): View
    {
        $requests = StaffingRequest::where('user_id', $client->id)
            ->orWhere('email', $client->email)
            ->with(['assignments.professional'])
            ->latest()
            ->get();

        $totalSpent = $requests->whereIn('payment_status', ['paid_to_admin', 'released_to_crew'])->sum('payment_amount');
        $completedProjectsCount = $requests->where('status', 'completed')->count();

        return view('admin.clients.show', compact('client', 'requests', 'totalSpent', 'completedProjectsCount'));
    }
}
