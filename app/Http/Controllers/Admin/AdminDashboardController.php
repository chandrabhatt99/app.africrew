<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\StaffingRequest;
use App\Models\StaffingAssignment;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'requests' => StaffingRequest::latest()->take(8)->get(),
            'totalRequests' => StaffingRequest::count(),
            'newRequests' => StaffingRequest::where('status', 'new')->count(),
            'totalClients' => User::where('role', 'client')->count(),
            'totalPaymentsCollected' => StaffingRequest::whereIn('payment_status', ['paid_to_admin', 'released_to_crew'])->sum('payment_amount'),
            'totalEscrowHeld' => Professional::sum('wallet_pending'),
            'pendingWithdrawalsCount' => WithdrawalRequest::where('status', 'pending')->count(),
            'totalProfessionals' => Professional::count(),
            'approvedProfessionals' => Professional::where('status', 'approved')->count(),
            'pendingProfessionals' => Professional::where('status', 'pending')->count(),
            'deactivatedProfessionals' => Professional::whereIn('status', ['rejected', 'suspended', 'deactivated'])->count(),
            'recentStaff' => Professional::latest()->take(6)->get(),
            'assignedJobs' => StaffingAssignment::whereIn('status', ['assigned', 'accepted'])->count(),
        ]);
    }
}
