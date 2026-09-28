<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffingRequest;
use App\Models\WithdrawalRequest;
use App\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AdminPaymentController extends Controller
{
    /**
     * Display financial overview, client payments, escrows, and crew withdrawal requests.
     */
    public function index(): View
    {
        $requests = StaffingRequest::with(['user', 'assignments.professional'])
            ->latest()
            ->paginate(15, ['*'], 'requests_page');

        $withdrawalRequests = WithdrawalRequest::with('professional')
            ->latest()
            ->paginate(15, ['*'], 'withdrawals_page');

        $totalCollected = StaffingRequest::whereIn('payment_status', ['paid_to_admin', 'released_to_crew'])->sum('payment_amount');
        $totalEscrowHeld = Professional::sum('wallet_pending');
        $totalAvailableWallets = Professional::sum('wallet_balance');
        $pendingWithdrawalsCount = WithdrawalRequest::where('status', 'pending')->count();
        $pendingWithdrawalsSum = WithdrawalRequest::where('status', 'pending')->sum('amount');

        return view('admin.payments.index', compact(
            'requests',
            'withdrawalRequests',
            'totalCollected',
            'totalEscrowHeld',
            'totalAvailableWallets',
            'pendingWithdrawalsCount',
            'pendingWithdrawalsSum'
        ));
    }

    /**
     * Record client payment to Admin.
     */
    public function markPaid(Request $request, StaffingRequest $staffingRequest): RedirectResponse
    {
        $data = $request->validate([
            'payment_amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($staffingRequest, $data) {
            $staffingRequest->update([
                'payment_status' => 'paid_to_admin',
                'payment_amount' => $data['payment_amount'],
                'payment_method' => $data['payment_method'],
                'paid_at' => now(),
            ]);

            // Allocate escrow to assigned professionals if assigned
            $assignments = $staffingRequest->assignments()->with('professional')->get();
            if ($assignments->isNotEmpty()) {
                $assignedCount = $assignments->count();
                $perCrewPayout = round($data['payment_amount'] / $assignedCount, 2);

                foreach ($assignments as $assignment) {
                    $prof = $assignment->professional;
                    if ($prof) {
                        $prof->increment('wallet_pending', $perCrewPayout);
                    }
                }
            }
        });

        return back()->with('success', 'Client payment recorded successfully! Funds are held in Escrow pending project completion.');
    }

    /**
     * Process or approve/reject crew withdrawal request.
     */
    public function updateWithdrawal(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,processed,rejected'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($withdrawal, $data) {
            $prof = $withdrawal->professional;

            if ($withdrawal->status === 'pending') {
                if (in_array($data['status'], ['approved', 'processed'])) {
                    // Check balance safety
                    if ($prof->wallet_balance >= $withdrawal->amount) {
                        $prof->decrement('wallet_balance', $withdrawal->amount);
                        $prof->increment('wallet_withdrawn', $withdrawal->amount);

                        $withdrawal->update([
                            'status' => $data['status'],
                            'admin_notes' => $data['admin_notes'] ?? null,
                            'processed_at' => now(),
                        ]);
                    } else {
                        throw new \Exception('Insufficient available wallet balance for this professional.');
                    }
                } elseif ($data['status'] === 'rejected') {
                    $withdrawal->update([
                        'status' => 'rejected',
                        'admin_notes' => $data['admin_notes'] ?? 'Withdrawal request rejected by Admin.',
                        'processed_at' => now(),
                    ]);
                }
            }
        });

        return back()->with('success', 'Withdrawal request updated successfully.');
    }
}
