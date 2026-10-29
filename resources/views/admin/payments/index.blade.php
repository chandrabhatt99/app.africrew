@extends('admin.layout')

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Payments & Escrow Wallet Operations</h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage client payments to admin, escrow locking, and crew payout withdrawal requests.</p>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Collected from Clients</span>
        <div class="text-2xl font-black text-slate-900 mt-2">KES {{ number_format($totalCollected, 2) }}</div>
        <div class="text-[10px] text-emerald-600 font-semibold mt-0.5">Paid directly to Admin account</div>
    </div>

    <div class="bg-emerald-900 text-white rounded-2xl border border-emerald-800 p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-emerald-300 tracking-wider">📱 M-Pesa Daraja Processed</span>
        <div class="text-2xl font-black text-white mt-2">KES {{ number_format($totalMpesaProcessed, 2) }}</div>
        <div class="text-[10px] text-emerald-300 font-semibold mt-0.5">Automatic STK &amp; C2B volume</div>
    </div>

    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-amber-800 tracking-wider">🔒 Escrow Held (Active)</span>
        <div class="text-2xl font-black text-amber-950 mt-2">KES {{ number_format($totalEscrowHeld, 2) }}</div>
        <div class="text-[10px] text-amber-700 font-semibold mt-0.5">Locked until shift completion</div>
    </div>

    <div class="bg-emerald-50 rounded-2xl border border-emerald-200 p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-emerald-800 tracking-wider">Available Crew Wallets</span>
        <div class="text-2xl font-black text-emerald-950 mt-2">KES {{ number_format($totalAvailableWallets, 2) }}</div>
        <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">Eligible for payout withdrawal</div>
    </div>

    <div class="bg-rose-50 rounded-2xl border border-rose-200 p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-rose-800 tracking-wider">Pending Withdrawals</span>
        <div class="text-2xl font-black text-rose-950 mt-2">{{ number_format($pendingWithdrawalsCount) }} Requests</div>
        <div class="text-[10px] text-rose-700 font-semibold mt-0.5">Total KES {{ number_format($pendingWithdrawalsSum, 2) }}</div>
    </div>
</div>

<!-- Section 1: Crew Withdrawal Requests Manager -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden mb-10">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="font-extrabold text-slate-900 text-lg">Crew Payout Withdrawal Requests</h2>
            <p class="text-xs text-slate-500 mt-0.5">Crew can only request withdrawal for funds unlocked after project completion ("Done")</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 font-extrabold text-xs">
            {{ $pendingWithdrawalsCount }} Action Required
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[750px]">
            <thead>
                <tr class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <th class="py-3.5 px-6">Crew Professional</th>
                    <th class="py-3.5 px-6">Amount Requested</th>
                    <th class="py-3.5 px-6">Payout Method & Details</th>
                    <th class="py-3.5 px-6">Request Date</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6 text-right">Admin Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($withdrawalRequests as $w)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-6 font-extrabold text-slate-900">
                        <div>{{ $w->professional->full_name }}</div>
                        <div class="text-xs font-normal text-slate-400">{{ $w->professional->email }} · {{ $w->professional->phone }}</div>
                    </td>
                    <td class="py-3.5 px-6 font-black text-slate-900 text-base">
                        KES {{ number_format($w->amount, 2) }}
                    </td>
                    <td class="py-3.5 px-6 text-xs text-slate-700">
                        <span class="inline-block font-bold uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-800 text-[10px]">
                            {{ $w->payout_method }}
                        </span>
                        <div class="font-extrabold text-slate-900 mt-1">{{ $w->account_details }}</div>
                    </td>
                    <td class="py-3.5 px-6 text-xs text-slate-500">
                        {{ $w->created_at ? $w->created_at->format('d M Y, h:i A') : 'N/A' }}
                    </td>
                    <td class="py-3.5 px-6">
                        @php
                            $wStatus = [
                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'processed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                            ][$w->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                        @endphp
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border capitalize {{ $wStatus }}">
                            {{ $w->status }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-right">
                        @if($w->status === 'pending')
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.payments.withdrawal', $w) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="processed">
                                    <button type="submit" onclick="return confirm('Approve and mark payout as PROCESSED? KES {{ number_format($w->amount, 2) }} will be deducted from crew available balance.')"
                                        class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition-all">
                                        ✓ Approve & Pay
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.payments.withdrawal', $w) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" onclick="return confirm('Reject this withdrawal request?')"
                                        class="px-3 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-800 font-extrabold text-xs transition-all">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-xs text-slate-400 font-medium">Processed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-500 text-xs italic">No withdrawal requests submitted yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($withdrawalRequests->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $withdrawalRequests->links() }}
        </div>
    @endif
</div>

<!-- Section 2: Client Bookings & Admin Payment Collection Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="font-extrabold text-slate-900 text-lg">Client Booking Payments & Escrow Allocations</h2>
            <p class="text-xs text-slate-500 mt-0.5">Record payments received from clients & monitor project completion escrow releases</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[800px]">
            <thead>
                <tr class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <th class="py-3.5 px-6">Client Name</th>
                    <th class="py-3.5 px-6">Event Name & Category</th>
                    <th class="py-3.5 px-6">Event Date</th>
                    <th class="py-3.5 px-6">Payment Status</th>
                    <th class="py-3.5 px-6">Project Status</th>
                    <th class="py-3.5 px-6 text-right">Record Payment / Release</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($requests as $r)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-6 font-extrabold text-slate-900">
                        <div>{{ $r->full_name }}</div>
                        <div class="text-xs font-normal text-slate-400">{{ $r->email }}</div>
                    </td>
                    <td class="py-3.5 px-6 text-slate-800 font-bold">
                        <div>{{ $r->event_name }}</div>
                        <div class="text-xs text-amber-700 font-semibold">{{ $r->category }} ({{ $r->staff_count }} Crew)</div>
                    </td>
                    <td class="py-3.5 px-6 text-xs text-slate-600 font-medium">
                        {{ $r->event_date ? $r->event_date->format('d M Y') : 'N/A' }}
                    </td>
                    <td class="py-3.5 px-6 text-xs">
                        @if($r->payment_status === 'paid_to_admin')
                            <span class="inline-flex px-3 py-1 rounded-full bg-amber-100 text-amber-900 font-bold border border-amber-200">
                                🔒 Paid to Admin (Escrow) — KES {{ number_format($r->payment_amount, 2) }}
                            </span>
                        @elseif($r->payment_status === 'released_to_crew')
                            <span class="inline-flex px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 font-bold border border-emerald-200">
                                ✓ Released to Crew Wallet
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1 rounded-full bg-rose-100 text-rose-800 font-bold border border-rose-200">
                                ⏳ Unpaid
                            </span>
                        @endif
                    </td>
                    <td class="py-3.5 px-6">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border capitalize {{ $r->status === 'completed' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-800' }}">
                            {{ str_replace('_', ' ', $r->status) }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-right">
                        @if($r->payment_status === 'unpaid')
                            <!-- Quick Payment Record Form -->
                            <form method="POST" action="{{ route('admin.payments.markPaid', $r) }}" class="flex items-center justify-end gap-2">
                                @csrf
                                <input type="number" step="0.01" name="payment_amount" value="{{ $r->budget ?: 5000 }}" placeholder="Amount" class="w-24 text-xs bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 focus:outline-none focus:border-amber-500">
                                <select name="payment_method" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-2 py-1.5 focus:outline-none">
                                    <option value="mpesa">M-Pesa</option>
                                    <option value="bank_transfer">Bank</option>
                                    <option value="card">Card</option>
                                    <option value="cash_admin">Cash</option>
                                </select>
                                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-xs px-3 py-1.5 rounded-xl transition-all shadow-xs">
                                    Mark Paid
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.requests.show', $r) }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
                                Inspect Request →
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-500 text-xs italic">No client requests recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $requests->links() }}
        </div>
    @endif
</div>

<!-- Section 3: Safaricom M-Pesa Daraja Live Transactions Log & Sandbox Tester -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden mb-10">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                M
            </div>
            <div>
                <h2 class="font-extrabold text-slate-900 text-lg">Safaricom M-Pesa Daraja Gateway Transactions</h2>
                <p class="text-xs text-slate-500 mt-0.5">Real-time log of STK Push, C2B receipts, and automated webhook callbacks.</p>
            </div>
        </div>

        <!-- STK Push Sandbox Tester Button -->
        <div class="flex items-center gap-2">
            <button type="button" onclick="openAdminMpesaTester()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm transition-all flex items-center gap-1.5 cursor-pointer">
                <span>📲 Test STK Push Prompt</span>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[750px]">
            <thead>
                <tr class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <th class="py-3.5 px-6">Receipt / Ref</th>
                    <th class="py-3.5 px-6">Phone Number</th>
                    <th class="py-3.5 px-6">Amount</th>
                    <th class="py-3.5 px-6">Type & Booking</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6">Timestamp</th>
                    <th class="py-3.5 px-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($mpesaTransactions as $t)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-6">
                        @if($t->mpesa_receipt_number)
                            <span class="font-mono font-black text-slate-900 text-xs px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-950 inline-block">
                                {{ $t->mpesa_receipt_number }}
                            </span>
                        @else
                            <span class="font-mono text-xs text-slate-400">
                                {{ $t->checkout_request_id ? substr($t->checkout_request_id, 0, 15) . '...' : 'Pending' }}
                            </span>
                        @endif
                    </td>
                    <td class="py-3.5 px-6 font-bold text-slate-800 text-xs">
                        {{ $t->phone_number }}
                    </td>
                    <td class="py-3.5 px-6 font-black text-slate-900 text-sm">
                        KES {{ number_format($t->amount, 2) }}
                    </td>
                    <td class="py-3.5 px-6 text-xs text-slate-600">
                        <div class="font-semibold text-slate-800">{{ $t->transaction_type }}</div>
                        @if($t->staffing_request_id)
                            <a href="{{ route('admin.requests.show', $t->staffing_request_id) }}" class="text-amber-700 font-bold hover:underline">
                                Order #{{ $t->staffing_request_id }}
                            </a>
                        @else
                            <span class="text-slate-400">{{ $t->account_reference ?: 'Direct' }}</span>
                        @endif
                    </td>
                    <td class="py-3.5 px-6">
                        @php
                            $badge = [
                                'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'failed' => 'bg-rose-100 text-rose-800 border-rose-200',
                                'cancelled' => 'bg-slate-100 text-slate-700 border-slate-200',
                            ][$t->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                        @endphp
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold border capitalize {{ $badge }}">
                            {{ $t->status }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-xs text-slate-500">
                        {{ $t->created_at ? $t->created_at->format('d M Y, h:i A') : 'N/A' }}
                    </td>
                    <td class="py-3.5 px-6 text-right">
                        @if($t->status === 'pending')
                            <button type="button" onclick="adminSimulateApprove('{{ $t->checkout_request_id }}')" class="px-2.5 py-1 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-[11px] transition-colors">
                                Simulate Approval ⚡
                            </button>
                        @else
                            <span class="text-slate-400 text-xs font-medium">✓ Logged</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-500 text-xs italic">
                        No M-Pesa transactions recorded yet. Use the "Test STK Push Prompt" button above or initiate an escrow payment from the client portal.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mpesaTransactions->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $mpesaTransactions->links() }}
        </div>
    @endif
</div>

<!-- Admin M-Pesa STK Tester Script -->
<script>
function openAdminMpesaTester() {
    Swal.fire({
        title: 'M-Pesa STK Push Sandbox Tester',
        html: `
            <div class="text-left space-y-3 text-xs">
                <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-950 font-medium">
                    Test the live Safaricom Daraja API connectivity. Entering a registered sandbox test phone number will prompt the handset directly.
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Safaricom Phone Number:</label>
                    <input type="tel" id="admin-mpesa-phone" value="254708374149" placeholder="2547XXXXXXXX" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Amount (KES):</label>
                    <input type="number" id="admin-mpesa-amount" value="10" min="1" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Account Reference:</label>
                    <input type="text" id="admin-mpesa-ref" value="AfriCrewAdmin" maxlength="12" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Send STK Push 📲',
        confirmButtonColor: '#059669',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const phone = document.getElementById('admin-mpesa-phone').value;
            const amount = document.getElementById('admin-mpesa-amount').value;
            const ref = document.getElementById('admin-mpesa-ref').value;

            return fetch('/payments/mpesa/stk-push', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    phone: phone,
                    amount: amount,
                    account_reference: ref,
                    transaction_desc: 'Admin Test'
                })
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(e => { throw new Error(e.message || 'STK Push failed'); });
                }
                return res.json();
            })
            .catch(err => {
                Swal.showValidationMessage(err.message || 'Error triggering STK Push');
            });
        }
    }).then(result => {
        if (result.isConfirmed && result.value && result.value.success) {
            Swal.fire({
                icon: 'success',
                title: 'STK Push Sent!',
                text: result.value.message || 'Check your phone and enter your PIN.',
                confirmButtonColor: '#059669'
            }).then(() => {
                window.location.reload();
            });
        }
    });
}

function adminSimulateApprove(checkoutRequestId) {
    Swal.fire({
        title: 'Simulate M-Pesa Payment Approval',
        text: 'This will simulate a successful Daraja callback, mark the transaction completed, and allocate escrow funds.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Simulate Approval',
        confirmButtonColor: '#059669'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('/payments/mpesa/simulate-success', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ checkout_request_id: checkoutRequestId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Confirmed!',
                        text: `M-Pesa Receipt: ${data.receipt}`,
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Simulation Failed', text: data.message });
                }
            });
        }
    });
}
</script>
@endsection
