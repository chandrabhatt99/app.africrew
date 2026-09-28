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
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Collected from Clients</span>
        <div class="text-2xl font-black text-slate-900 mt-2">KES {{ number_format($totalCollected, 2) }}</div>
        <div class="text-[10px] text-emerald-600 font-semibold mt-0.5">Paid directly to Admin account</div>
    </div>

    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-amber-800 tracking-wider">🔒 Escrow Held (Active Projects)</span>
        <div class="text-2xl font-black text-amber-950 mt-2">KES {{ number_format($totalEscrowHeld, 2) }}</div>
        <div class="text-[10px] text-amber-700 font-semibold mt-0.5">Locked until projects are completed ("Done")</div>
    </div>

    <div class="bg-emerald-50 rounded-2xl border border-emerald-200 p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-emerald-800 tracking-wider">Available Crew Wallets</span>
        <div class="text-2xl font-black text-emerald-950 mt-2">KES {{ number_format($totalAvailableWallets, 2) }}</div>
        <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">Eligible for payout withdrawal</div>
    </div>

    <div class="bg-rose-50 rounded-2xl border border-rose-200 p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-rose-800 tracking-wider">Pending Crew Withdrawals</span>
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
@endsection
