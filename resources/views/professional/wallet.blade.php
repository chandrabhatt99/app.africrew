@extends('layouts.staff')

@section('content')
<div class="space-y-8 pb-16">

    @php
        $paymentDetails = is_array($professional->payment_details) ? $professional->payment_details : [];
        $mobileDetails = $paymentDetails['mobile_money'] ?? [];
        $bankDetails = $paymentDetails['bank_account'] ?? [];
        
        $hasSavedMobile = !empty($mobileDetails['phone_number']);
        $hasSavedBank = !empty($bankDetails['account_number']);
        
        $savedMobileProvider = strtolower($mobileDetails['provider'] ?? 'mpesa');
        $mobileProviderLabel = str_contains($savedMobileProvider, 'airtel') ? 'Airtel Payment' : 'M-Pesa Mobile Money';
        $savedMobilePhone = $mobileDetails['phone_number'] ?? $professional->phone;
        $savedMobileName = $mobileDetails['account_name'] ?? $professional->full_name;

        $savedBankName = $bankDetails['bank_name'] ?? '';
        $savedBankAcc = $bankDetails['account_number'] ?? '';
        $savedBankHolder = $bankDetails['account_name'] ?? $professional->full_name;
    @endphp

    <!-- Top Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 text-xs font-black uppercase mb-2">
                💳 Crew Wallet & Payout Escrow
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">Your Crew Earnings & Wallet</h1>
            <p class="text-xs text-slate-500 mt-1">Track shift escrow payouts, saved payout account settings, and request withdrawals.</p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="openPaymentSettingsModal()" class="px-4 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-md transition-all flex items-center gap-2">
                <span>⚙️ Manage Payout Accounts</span>
            </button>
            <a href="{{ route('professional.shifts') }}" class="px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-800 font-extrabold text-xs shadow-xs hover:bg-slate-50 transition-all">
                📅 View Assigned Shifts
            </a>
        </div>
    </div>

    <!-- Notification Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-extrabold flex items-center gap-3 shadow-xs">
            <span>✓ {{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-bold shadow-xs space-y-1">
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    <!-- Saved Payout Accounts Quick Status Banner -->
    <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                    🏦 System Payout Accounts
                </span>
                <h3 class="text-lg font-black text-slate-950 mt-1">Saved Payout Methods</h3>
                <p class="text-xs text-slate-500">Your connected Airtel Money, M-Pesa, or Bank account for quick withdrawal selection.</p>
            </div>
            <button onclick="openPaymentSettingsModal()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 text-xs font-black shadow-xs transition-all">
                + Add / Update Saved Payout Details
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <!-- Mobile Money Card -->
            <div class="p-4 rounded-2xl border {{ $hasSavedMobile ? 'bg-emerald-50/50 border-emerald-200/90' : 'bg-slate-50 border-slate-200' }} flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📱</span>
                        <span class="font-black text-slate-900 text-sm">Mobile Money</span>
                        @if(str_contains($savedMobileProvider, 'airtel'))
                            <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-extrabold text-[10px]">Airtel Money</span>
                        @else
                            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">M-Pesa</span>
                        @endif
                    </div>
                    @if($hasSavedMobile)
                        <div class="font-bold text-slate-800">Phone: {{ $savedMobilePhone }}</div>
                        <div class="text-[11px] text-slate-500 font-medium">Account Name: {{ $savedMobileName }}</div>
                    @else
                        <div class="text-slate-400 italic font-medium">No mobile money details saved yet.</div>
                    @endif
                </div>
                <button onclick="openPaymentSettingsModal('mobile_money')" class="text-xs font-extrabold text-amber-700 hover:underline">
                    {{ $hasSavedMobile ? 'Edit' : 'Connect' }}
                </button>
            </div>

            <!-- Bank Account Card -->
            <div class="p-4 rounded-2xl border {{ $hasSavedBank ? 'bg-blue-50/50 border-blue-200/90' : 'bg-slate-50 border-slate-200' }} flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🏦</span>
                        <span class="font-black text-slate-900 text-sm">Bank Account</span>
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-extrabold text-[10px]">Wire Transfer</span>
                    </div>
                    @if($hasSavedBank)
                        <div class="font-bold text-slate-800">{{ $savedBankName }} — Acc: {{ $savedBankAcc }}</div>
                        <div class="text-[11px] text-slate-500 font-medium">Account Name: {{ $savedBankHolder }}</div>
                    @else
                        <div class="text-slate-400 italic font-medium">No bank account details saved yet.</div>
                    @endif
                </div>
                <button onclick="openPaymentSettingsModal('bank_account')" class="text-xs font-extrabold text-amber-700 hover:underline">
                    {{ $hasSavedBank ? 'Edit' : 'Connect' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Wallet Metric Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Pending Escrow Card -->
        <div class="bg-amber-500/10 border-2 border-amber-400/80 rounded-3xl p-6 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-black uppercase tracking-wider text-amber-800">🔒 Pending Escrow Balance</span>
                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-900 text-[10px] font-black uppercase">Locked</span>
            </div>
            <div class="text-3xl font-black text-slate-950 mt-3">KES {{ number_format($professional->wallet_pending, 2) }}</div>
            <p class="text-[11px] text-amber-900 font-semibold mt-2 leading-relaxed">
                Held in Escrow for active client projects. Unlocks into Available Balance once the project is marked <strong>COMPLETED ("Done")</strong>.
            </p>
        </div>

        <!-- Available Balance Card -->
        <div class="bg-emerald-600 text-white rounded-3xl p-6 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-200">✓ Available Wallet Balance</span>
                <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-black uppercase">Ready to Withdraw</span>
            </div>
            <div class="text-3xl font-black text-amber-300 mt-3">KES {{ number_format($professional->wallet_balance, 2) }}</div>
            <p class="text-[11px] text-emerald-100 font-medium mt-2 leading-relaxed">
                Funds from completed projects. Eligible for instant Airtel Payment, M-Pesa or Bank payout requests.
            </p>
        </div>

        <!-- Total Withdrawn Card -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Total Withdrawn</span>
                <span class="text-lg">🏦</span>
            </div>
            <div class="text-3xl font-black text-slate-900 mt-3">KES {{ number_format($professional->wallet_withdrawn, 2) }}</div>
            <p class="text-[11px] text-slate-500 font-medium mt-2 leading-relaxed">
                Total payouts successfully processed by Admin into your mobile phone or bank account.
            </p>
        </div>
    </div>

    <!-- Escrow Policy Banner -->
    <div class="p-5 rounded-3xl bg-slate-900 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-md">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🛡️</span>
            <div>
                <h3 class="text-sm font-extrabold text-amber-400">Safe Escrow Payment System</h3>
                <p class="text-xs text-slate-300 mt-0.5">
                    Client pays Admin upfront. Money is locked safely in Escrow and automatically released to your Available Balance when the shift project is completed ("Done"). Direct cash from clients is not permitted.
                </p>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">

        <!-- Left: Request Payout Withdrawal Form -->
        <div class="bg-white rounded-3xl border border-slate-200/90 p-7 shadow-xl h-fit space-y-6">
            <div>
                <h2 class="text-lg font-black text-slate-950 mb-1">Request Payout Withdrawal</h2>
                <p class="text-xs text-slate-500">Select your saved payout account or enter new account details.</p>
            </div>

            <form method="POST" action="{{ route('professional.wallet.withdraw') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Withdrawal Amount (KES)
                    </label>
                    <input type="number" step="10" name="amount" required max="{{ $professional->wallet_balance }}" placeholder="e.g. 5000"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-900 text-sm font-black focus:border-amber-500 focus:bg-white transition-all">
                    <span class="text-[10px] text-slate-400 mt-1 block">Maximum available: KES {{ number_format($professional->wallet_balance, 2) }}</span>
                </div>

                <!-- Select Payout Account Source -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Select Payout Account
                    </label>

                    <div class="space-y-2 text-xs font-bold">
                        @if($hasSavedMobile)
                            <label class="flex items-center gap-3 p-3.5 rounded-2xl border bg-slate-50 hover:bg-amber-50/60 cursor-pointer transition-all border-slate-200">
                                <input type="radio" name="payout_source" value="saved_mobile" checked onclick="toggleWithdrawalSource('saved_mobile')" class="accent-amber-500">
                                <div>
                                    <div class="text-slate-900 font-extrabold">📱 Saved Mobile Money ({{ $mobileProviderLabel }})</div>
                                    <div class="text-[11px] text-slate-500 font-medium">{{ $savedMobilePhone }} — {{ $savedMobileName }}</div>
                                </div>
                            </label>
                        @endif

                        @if($hasSavedBank)
                            <label class="flex items-center gap-3 p-3.5 rounded-2xl border bg-slate-50 hover:bg-amber-50/60 cursor-pointer transition-all border-slate-200">
                                <input type="radio" name="payout_source" value="saved_bank" @if(!$hasSavedMobile) checked @endif onclick="toggleWithdrawalSource('saved_bank')" class="accent-amber-500">
                                <div>
                                    <div class="text-slate-900 font-extrabold">🏦 Saved Bank Account ({{ $savedBankName }})</div>
                                    <div class="text-[11px] text-slate-500 font-medium">Acc: {{ $savedBankAcc }} — {{ $savedBankHolder }}</div>
                                </div>
                            </label>
                        @endif

                        <label class="flex items-center gap-3 p-3.5 rounded-2xl border bg-slate-50 hover:bg-amber-50/60 cursor-pointer transition-all border-slate-200">
                            <input type="radio" name="payout_source" value="custom" @if(!$hasSavedMobile && !$hasSavedBank) checked @endif onclick="toggleWithdrawalSource('custom')" class="accent-amber-500">
                            <div>
                                <div class="text-slate-900 font-extrabold">✏️ Enter Custom Payout Details</div>
                                <div class="text-[11px] text-slate-500 font-medium">Enter custom Airtel/M-Pesa or Bank account</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Custom Payout Details Fields (Hidden unless 'custom' is selected) -->
                <div id="custom-payout-fields" class="{{ ($hasSavedMobile || $hasSavedBank) ? 'hidden' : '' }} space-y-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Payout Method
                        </label>
                        <select name="payout_method" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-900 text-xs font-bold focus:border-amber-500 focus:bg-white transition-all">
                            <option value="mpesa">M-Pesa Mobile Money</option>
                            <option value="airtel_money">Airtel Money</option>
                            <option value="bank_transfer">Bank Wire Transfer</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Account / Phone Details
                        </label>
                        <input type="text" name="account_details" value="{{ $professional->phone }}" placeholder="Phone (e.g. 0712345678) or Bank details"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-900 text-xs font-semibold focus:border-amber-500 focus:bg-white transition-all">
                    </div>
                </div>

                <button type="submit" @if($professional->wallet_balance <= 0) disabled @endif
                    class="w-full py-4 rounded-2xl bg-slate-900 hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed text-amber-400 font-black text-xs shadow-lg transition-all">
                    Submit Withdrawal Request →
                </button>
            </form>
        </div>

        <!-- Right: Active Pending Escrow Shifts & Past Withdrawal History -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Pending Escrow Shifts -->
            <div class="bg-white rounded-3xl border border-slate-200/90 p-7 shadow-sm">
                <h2 class="text-lg font-black text-slate-950 mb-1">Active Shifts Pending Project Completion ("Done")</h2>
                <p class="text-xs text-slate-500 mb-4">Funds in escrow for these active shifts will move to Available Balance once the shift is completed.</p>

                @if($pendingEscrowJobs->isEmpty())
                    <div class="py-6 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 p-4">
                        No active shift payouts currently locked in pending escrow.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($pendingEscrowJobs as $job)
                            <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 flex items-center justify-between gap-3">
                                <div>
                                    <div class="font-extrabold text-slate-900 text-sm">{{ $job->staffingRequest->event_name }}</div>
                                    <div class="text-xs text-slate-600 mt-0.5">📅 Date: {{ $job->staffingRequest->event_date ? $job->staffingRequest->event_date->format('d M Y') : 'N/A' }} · 📍 {{ $job->staffingRequest->location }}</div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-3 py-1 rounded-full bg-amber-200 text-amber-900 font-black text-xs">
                                        🔒 In Escrow
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Past Withdrawal Requests Table -->
            <div class="bg-white rounded-3xl border border-slate-200/90 p-7 shadow-sm overflow-hidden">
                <h2 class="text-lg font-black text-slate-950 mb-1">Withdrawal Request History</h2>
                <p class="text-xs text-slate-500 mb-4">Status of your submitted payout withdrawal requests</p>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left min-w-[500px]">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Amount</th>
                                <th class="py-3 px-4">Method & Account</th>
                                <th class="py-3 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($withdrawals as $w)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        {{ $w->created_at ? $w->created_at->format('d M Y, h:i A') : 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 font-black text-slate-900 text-sm">
                                        KES {{ number_format($w->amount, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-700">
                                        <span class="uppercase font-extrabold text-[10px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-800">{{ str_replace('_', ' ', $w->payout_method) }}</span>
                                        <div class="font-bold mt-0.5">{{ $w->account_details }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        @php
                                            $wBadge = [
                                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'processed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                                            ][$w->status] ?? 'bg-slate-100 text-slate-800';
                                        @endphp
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full font-extrabold border capitalize {{ $wBadge }}">
                                            {{ $w->status }}
                                        </span>
                                        @if($w->admin_notes)
                                            <div class="text-[10px] text-slate-400 mt-0.5 italic">{{ $w->admin_notes }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400 italic">No withdrawal requests submitted yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Manage / Connect Payout Account Details Modal -->
<div id="payment-settings-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-5 text-slate-900">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-lg font-black tracking-tight">Save Payout Account Details</h3>
                <p class="text-xs text-slate-500">Choose account type and enter details to save in system.</p>
            </div>
            <button onclick="closePaymentSettingsModal()" class="text-slate-400 hover:text-slate-900 p-1">
                ✕
            </button>
        </div>

        <form method="POST" action="{{ route('professional.payment-details.update') }}" class="space-y-5">
            @csrf

            <!-- Account Type Selector Tabs -->
            <div>
                <label class="block text-xs font-extrabold uppercase text-slate-500 mb-2">Account Type</label>
                <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 rounded-2xl text-xs font-black">
                    <button type="button" id="tab-btn-mobile" onclick="switchPayoutTypeTab('mobile_money')" 
                        class="py-2.5 rounded-xl transition-all bg-amber-500 text-slate-950 shadow-xs flex items-center justify-center gap-2">
                        <span>📱 Mobile Money</span>
                    </button>
                    <button type="button" id="tab-btn-bank" onclick="switchPayoutTypeTab('bank_account')" 
                        class="py-2.5 rounded-xl transition-all text-slate-600 hover:bg-slate-200 flex items-center justify-center gap-2">
                        <span>🏦 Bank Account</span>
                    </button>
                </div>
                <input type="hidden" name="account_type" id="input_account_type" value="mobile_money">
            </div>

            <!-- Mobile Money Inputs Container -->
            <div id="container-mobile-money" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Money Provider</label>
                    <select name="mobile_provider" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                        <option value="airtel" @selected(($mobileDetails['provider'] ?? '') === 'airtel')>🔴 Airtel Money / Airtel Payment</option>
                        <option value="mpesa" @selected(($mobileDetails['provider'] ?? '') === 'mpesa' || empty($mobileDetails['provider']))>🟢 M-Pesa Mobile Money</option>
                        <option value="tigo" @selected(($mobileDetails['provider'] ?? '') === 'tigo')>🔵 Tigo Cash / Pesa</option>
                        <option value="mtn" @selected(($mobileDetails['provider'] ?? '') === 'mtn')>🟡 MTN Mobile Money</option>
                        <option value="other" @selected(($mobileDetails['provider'] ?? '') === 'other')>📱 Other Mobile Wallet</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Connected Phone Number *</label>
                    <input type="text" name="mobile_number" value="{{ $mobileDetails['phone_number'] ?? $professional->phone }}" required placeholder="e.g. 0712345678 or +254712345678" 
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                    <span class="text-[10px] text-slate-400 mt-1 block">The phone number registered with your mobile money account.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Account Holder Full Name</label>
                    <input type="text" name="mobile_name" value="{{ $mobileDetails['account_name'] ?? $professional->full_name }}" placeholder="e.g. John Doe" 
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                </div>
            </div>

            <!-- Bank Account Inputs Container -->
            <div id="container-bank-account" class="space-y-4 hidden">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Bank Name *</label>
                    <input type="text" name="bank_name" value="{{ $bankDetails['bank_name'] ?? '' }}" placeholder="e.g. KCB Bank, Equity Bank, Absa, Stanbic" 
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Bank Account Number *</label>
                    <input type="text" name="account_number" value="{{ $bankDetails['account_number'] ?? '' }}" placeholder="e.g. 1234567890" 
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Account Holder Name *</label>
                    <input type="text" name="bank_account_name" value="{{ $bankDetails['account_name'] ?? $professional->full_name }}" placeholder="Exact name on bank account" 
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Branch Code / Name</label>
                        <input type="text" name="branch_code" value="{{ $bankDetails['branch_code'] ?? '' }}" placeholder="e.g. Main Branch / 0100" 
                            class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">IFSC / SWIFT Code</label>
                        <input type="text" name="swift_ifsc" value="{{ $bankDetails['swift_ifsc'] ?? '' }}" placeholder="e.g. KCBLKENX" 
                            class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none focus:border-amber-500">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closePaymentSettingsModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                <button type="submit" class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-md transition-all">Save Account Details →</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleWithdrawalSource(source) {
        const customFields = document.getElementById('custom-payout-fields');
        if (customFields) {
            if (source === 'custom') {
                customFields.classList.remove('hidden');
            } else {
                customFields.classList.add('hidden');
            }
        }
    }

    function switchPayoutTypeTab(type) {
        const inputType = document.getElementById('input_account_type');
        const btnMobile = document.getElementById('tab-btn-mobile');
        const btnBank = document.getElementById('tab-btn-bank');
        const boxMobile = document.getElementById('container-mobile-money');
        const boxBank = document.getElementById('container-bank-account');

        if (inputType) inputType.value = type;

        if (type === 'mobile_money') {
            btnMobile.className = 'py-2.5 rounded-xl transition-all bg-amber-500 text-slate-950 shadow-xs flex items-center justify-center gap-2';
            btnBank.className = 'py-2.5 rounded-xl transition-all text-slate-600 hover:bg-slate-200 flex items-center justify-center gap-2';
            boxMobile.classList.remove('hidden');
            boxBank.classList.add('hidden');
        } else {
            btnBank.className = 'py-2.5 rounded-xl transition-all bg-amber-500 text-slate-950 shadow-xs flex items-center justify-center gap-2';
            btnMobile.className = 'py-2.5 rounded-xl transition-all text-slate-600 hover:bg-slate-200 flex items-center justify-center gap-2';
            boxBank.classList.remove('hidden');
            boxMobile.classList.add('hidden');
        }
    }

    function openPaymentSettingsModal(defaultTab = 'mobile_money') {
        switchPayoutTypeTab(defaultTab);
        document.getElementById('payment-settings-modal').classList.replace('hidden', 'flex');
    }

    function closePaymentSettingsModal() {
        document.getElementById('payment-settings-modal').classList.replace('flex', 'hidden');
    }
</script>
@endsection
