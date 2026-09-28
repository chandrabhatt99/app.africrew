@extends('layouts.staff')

@section('content')
<div class="space-y-6 sm:space-y-8 pb-16">

    <!-- Top Premium Crew Hero Banner -->
    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden border border-slate-800">
        <!-- Glowing Amber Radial Blur Background -->
        <div class="absolute -top-16 -right-16 w-72 h-72 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <!-- Left User Profile Details -->
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="relative shrink-0">
                    @if(!empty($professional->profile_photo))
                        <img src="{{ get_storage_url($professional->profile_photo) }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border-2 border-amber-400 shadow-md">
                    @else
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-amber-400 to-yellow-600 text-slate-950 font-black text-2xl sm:text-3xl flex items-center justify-center border-2 border-amber-300 shadow-md">
                            {{ strtoupper(substr($professional->full_name ?? 'C', 0, 1)) }}
                        </div>
                    @endif
                    <span class="w-4 h-4 rounded-full bg-emerald-500 border-2 border-slate-950 absolute -bottom-1 -right-1 shadow-sm" title="Active Online"></span>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">{{ $professional->full_name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-black uppercase tracking-wider">
                            ✓ Verified Crew
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-300 font-medium flex items-center gap-3 flex-wrap">
                        <span class="text-amber-400 font-bold">⭐ {{ $professional->category ?: 'Event Usher & Host' }}</span>
                        <span>•</span>
                        <span>📍 {{ $professional->city ?: 'Nairobi' }}, {{ $professional->country ?: 'Kenya' }}</span>
                        <span>•</span>
                        <span class="text-yellow-400 font-bold">Rating: ⭐ {{ number_format($professional->average_rating ?: 4.9, 1) }}</span>
                    </p>

                    <div class="flex items-center gap-2 pt-1">
                        <span class="text-[11px] text-slate-400 font-semibold">Account Status:</span>
                        <span class="px-2 py-0.5 rounded-md bg-amber-400/10 text-amber-300 text-[10px] font-bold border border-amber-400/20">
                            100% Onboarded & Active
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Quick Hero Actions -->
            <div class="flex items-center gap-2.5 flex-wrap w-full md:w-auto shrink-0">
                <a href="{{ route('messages') }}" class="flex-1 md:flex-initial px-4 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-lg transition-all flex items-center justify-center gap-2 text-decoration-none">
                    <span>💬 Live Messenger</span>
                    <span class="w-2 h-2 rounded-full bg-slate-950 animate-pulse"></span>
                </a>
                <a href="{{ route('professional.shifts') }}" class="flex-1 md:flex-initial px-4 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs shadow-md border border-slate-700/80 transition-all flex items-center justify-center gap-1.5 text-decoration-none">
                    <span>📅 My Shifts</span>
                </a>
                <a href="{{ route('professional.profile') }}" class="px-3.5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700/80 transition-all text-decoration-none" title="Edit Profile">
                    <span>⚙️</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Metrics & Financial Overview Grid (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Available Wallet Balance -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-md transition-all space-y-3 group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Available Wallet</span>
                <div class="w-9 h-9 rounded-2xl bg-emerald-500/10 text-emerald-600 font-black flex items-center justify-center text-sm group-hover:scale-110 transition-transform">
                    💳
                </div>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 block tracking-tight">KES {{ number_format($professional->wallet_balance, 2) }}</span>
                <span class="text-[11px] text-emerald-600 font-bold mt-1 block">Ready for instant payout ⚡</span>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('professional.wallet') }}" class="text-xs font-black text-amber-600 hover:text-amber-700 text-decoration-none flex items-center gap-1">
                    <span>Manage Wallet</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        <!-- Card 2: Pending Escrow Balance -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-md transition-all space-y-3 group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Pending Shift Escrow</span>
                <div class="w-9 h-9 rounded-2xl bg-amber-500/10 text-amber-600 font-black flex items-center justify-center text-sm group-hover:scale-110 transition-transform">
                    🔒
                </div>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 block tracking-tight">KES {{ number_format($professional->wallet_pending, 2) }}</span>
                <span class="text-[11px] text-slate-500 font-semibold mt-1 block">Held safely in escrow</span>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400 font-medium">Released on shift completion</span>
            </div>
        </div>

        <!-- Card 3: Total Shift Assignments -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-md transition-all space-y-3 group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total Shift Gigs</span>
                <div class="w-9 h-9 rounded-2xl bg-blue-500/10 text-blue-600 font-black flex items-center justify-center text-sm group-hover:scale-110 transition-transform">
                    📋
                </div>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 block tracking-tight">{{ $stats['total'] ?? 0 }}</span>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[11px] text-emerald-600 font-bold">{{ $stats['accepted'] ?? 0 }} Confirmed</span>
                    <span class="text-[11px] text-slate-300">•</span>
                    <span class="text-[11px] text-amber-600 font-bold">{{ $stats['pending'] ?? 0 }} Pending</span>
                </div>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('professional.shifts') }}" class="text-xs font-black text-slate-700 hover:text-slate-900 text-decoration-none">
                    View Shift Calendar →
                </a>
            </div>
        </div>

        <!-- Card 4: Standard Daily Rate -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-md transition-all space-y-3 group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Base Shift Rate</span>
                <div class="w-9 h-9 rounded-2xl bg-yellow-500/10 text-yellow-600 font-black flex items-center justify-center text-sm group-hover:scale-110 transition-transform">
                    💰
                </div>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black text-amber-600 block tracking-tight">KES {{ number_format($professional->one_day_rate ?: 5000, 2) }}</span>
                <span class="text-[11px] text-slate-500 font-semibold mt-1 block">Hourly: KES {{ number_format($professional->hourly_rate ?: 750, 2) }}</span>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('professional.profile') }}#step-5" class="text-xs font-black text-amber-600 hover:text-amber-700 text-decoration-none">
                    Update Labour Charges →
                </a>
            </div>
        </div>
    </div>

    <!-- Interactive Crew Booking & Availability Calendar Component -->
    @include('partials.crew-calendar')

    <!-- Full-Width Clean Shift Assignments & Gigs Section -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-800 text-[10px] font-black uppercase mb-1">
                    📋 Shift Assignments
                </div>
                <h3 class="text-xl font-black text-slate-900">Shift Assignments & Gigs</h3>
                <p class="text-xs text-slate-500 mt-0.5">Filter upcoming event shifts assigned to your profile and respond to client booking requests.</p>
            </div>

            <!-- Gig Filter Tabs -->
            <div class="flex items-center gap-1.5 bg-slate-100 p-1.5 rounded-2xl text-xs font-extrabold overflow-x-auto">
                <button onclick="filterGigs('all')" id="gig-tab-all" class="px-4 py-2 rounded-xl transition-all bg-amber-500 text-slate-950 font-black shadow-sm">
                    Upcoming
                </button>
                <button onclick="filterGigs('accepted')" id="gig-tab-accepted" class="px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-200">
                    Accepted
                </button>
                <button onclick="filterGigs('week')" id="gig-tab-week" class="px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-200">
                    This Week
                </button>
                <button onclick="filterGigs('pending')" id="gig-tab-pending" class="px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-200">
                    Pending Review
                </button>
            </div>
        </div>

        <!-- Full-Width Clean Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 text-[10px] font-black uppercase tracking-wider text-slate-400 bg-slate-50/50">
                        <th class="py-3.5 px-4 rounded-l-xl">Event Title</th>
                        <th class="py-3.5 px-4">Crew Role</th>
                        <th class="py-3.5 px-4">Location</th>
                        <th class="py-3.5 px-4">Shift Date</th>
                        <th class="py-3.5 px-4">Pay Escrow</th>
                        <th class="py-3.5 px-4 text-right rounded-r-xl">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-800">
                    @if($assignments->isEmpty())
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 space-y-2">
                                <span class="text-3xl block">📋</span>
                                <p class="font-bold text-slate-700 text-xs">No Shift Assignments Found</p>
                                <p class="text-[11px] text-slate-500 max-w-sm mx-auto">When clients book you or assign event shifts, your active assignments will appear here.</p>
                            </td>
                        </tr>
                    @else
                        @foreach($assignments as $job)
                            @php
                                $req = $job->staffingRequest;
                                $proposal = $req?->proposals?->where('professional_id', $professional->id)->first() ?? $req?->proposals?->first();
                                $isNegotiable = ($req?->rate_type === 'negotiable') || ($proposal !== null);
                                $reqId = $req?->id;
                                $totalBudget = $req?->budget ?: ($professional->one_day_rate ?: 5000);
                            @endphp
                            <tr class="gig-row hover:bg-slate-50/80 transition-colors" data-status="{{ strtolower($job->status) }}">
                                <td class="py-4 px-4 font-black text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        <span>{{ $req->event_name ?? 'Corporate Event Shift' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-800 font-bold uppercase text-[10px]">
                                        {{ $req->category ?? $professional->category }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-600 font-medium">
                                    📍 {{ $req->location ?? 'Nairobi, Kenya' }}
                                </td>
                                <td class="py-4 px-4 text-slate-600 font-medium">
                                    📅 {{ date('M j, Y', strtotime($req->event_date ?? now())) }}
                                </td>
                                <td class="py-4 px-4 font-bold text-emerald-700">
                                    KES {{ number_format($totalBudget, 2) }}
                                </td>
                                <td class="py-4 px-4 text-right">
                                    @if($job->status === 'assigned' || $isNegotiable)
                                        <div class="inline-flex items-center gap-1.5 flex-wrap justify-end">
                                            @if($proposal)
                                                <form method="POST" action="{{ route('messages.acceptPrice') }}" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">
                                                    <button type="submit" onclick="return confirm('Accept quote and confirm booking?');" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-black text-[11px] hover:bg-emerald-700 shadow-2xs transition-all border-0">Accept ✓</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('professional.jobs.status', $job) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-black text-[11px] hover:bg-emerald-700 shadow-2xs transition-all border-0">Accept ✓</button>
                                                </form>
                                            @endif

                                            <a href="{{ route('messages') }}?request_id={{ $reqId }}" class="px-3 py-1.5 rounded-xl bg-slate-900 text-amber-400 font-black text-[11px] hover:bg-slate-800 shadow-2xs transition-all text-decoration-none">
                                                💬 Chat
                                            </a>

                                         </div>
                                    @else
                                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-extrabold text-[11px] capitalize">
                                            {{ $job->status }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

    </div>

    <!-- Payout Method Status Widget Card -->
    @php
        $dashPayment = is_array($professional->payment_details) ? $professional->payment_details : [];
        $dashMobile = $dashPayment['mobile_money'] ?? [];
        $dashBank = $dashPayment['bank_account'] ?? [];
        $hasDashPayment = !empty($dashMobile['phone_number']) || !empty($dashBank['account_number']);
    @endphp
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-600 flex items-center justify-center text-xl shrink-0">
                💳
            </div>
            <div>
                <h4 class="text-base font-black text-slate-900">Payout Account Status</h4>
                @if($hasDashPayment)
                    @if(!empty($dashMobile['phone_number']))
                        <p class="text-xs text-emerald-600 font-bold mt-0.5">✓ Mobile Money Connected: {{ strtoupper($dashMobile['provider'] ?? 'M-Pesa') }} ({{ $dashMobile['phone_number'] }})</p>
                    @else
                        <p class="text-xs text-emerald-600 font-bold mt-0.5">✓ Bank Connected: {{ $dashBank['bank_name'] ?? 'Bank Account' }} (Acc: {{ $dashBank['account_number'] }})</p>
                    @endif
                @else
                    <p class="text-xs text-amber-600 font-bold mt-0.5">⚠️ No payment method set yet. Connect your Airtel Money, M-Pesa or Bank details for shift payouts.</p>
                @endif
            </div>
        </div>

        <button onclick="openPaymentModal()" class="px-5 py-3 rounded-2xl bg-slate-900 text-amber-400 hover:bg-slate-800 font-black text-xs shadow-md transition-all shrink-0">
            {{ $hasDashPayment ? 'Manage Payout Account →' : 'Add payment details →' }}
        </button>
    </div>

</div>

<!-- Payment Details Modal -->
<div id="payment-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-5 text-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-lg font-black tracking-tight">Connect Payout Account</h3>
                <p class="text-xs text-slate-500">Provide your Airtel Money, M-Pesa, or Bank details for shift wage transfers.</p>
            </div>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-900 p-1">✕</button>
        </div>
        
        <form method="POST" action="{{ route('professional.payment-details.update') }}" class="space-y-4">
            @csrf
            
            <!-- Account Type Selector Tabs -->
            <div>
                <label class="block text-xs font-extrabold uppercase text-slate-500 mb-2">Account Type</label>
                <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 rounded-2xl text-xs font-black">
                    <button type="button" id="dash-tab-mobile" onclick="switchDashPayoutTab('mobile_money')" 
                        class="py-2 rounded-xl transition-all bg-amber-500 text-slate-950 shadow-xs">
                        📱 Mobile Money
                    </button>
                    <button type="button" id="dash-tab-bank" onclick="switchDashPayoutTab('bank_account')" 
                        class="py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-200">
                        🏦 Bank Account
                    </button>
                </div>
                <input type="hidden" name="account_type" id="dash_input_account_type" value="mobile_money">
            </div>

            <!-- Mobile Money Inputs Container -->
            <div id="dash-box-mobile" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Money Provider</label>
                    <select name="mobile_provider" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                        <option value="airtel" @selected(($dashMobile['provider'] ?? '') === 'airtel')>🔴 Airtel Money / Airtel Payment</option>
                        <option value="mpesa" @selected(($dashMobile['provider'] ?? '') === 'mpesa' || empty($dashMobile['provider']))>🟢 M-Pesa Mobile Money</option>
                        <option value="tigo" @selected(($dashMobile['provider'] ?? '') === 'tigo')>🔵 Tigo Cash / Pesa</option>
                        <option value="mtn" @selected(($dashMobile['provider'] ?? '') === 'mtn')>🟡 MTN Mobile Money</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Connected Phone Number *</label>
                    <input type="text" name="mobile_number" value="{{ $dashMobile['phone_number'] ?? $professional->phone }}" required placeholder="e.g. 0712345678" 
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Account Holder Full Name</label>
                    <input type="text" name="mobile_name" value="{{ $dashMobile['account_name'] ?? $professional->full_name }}" placeholder="e.g. John Doe" 
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                </div>
            </div>

            <!-- Bank Account Inputs Container -->
            <div id="dash-box-bank" class="space-y-3 hidden">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Bank Name *</label>
                    <input type="text" name="bank_name" value="{{ $dashBank['bank_name'] ?? '' }}" placeholder="e.g. KCB Bank, Equity Bank, Absa" 
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Bank Account Number *</label>
                    <input type="text" name="account_number" value="{{ $dashBank['account_number'] ?? '' }}" placeholder="e.g. 1234567890" 
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Account Holder Name *</label>
                    <input type="text" name="bank_account_name" value="{{ $dashBank['account_name'] ?? $professional->full_name }}" placeholder="Exact name on bank account" 
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Branch Code</label>
                        <input type="text" name="branch_code" value="{{ $dashBank['branch_code'] ?? '' }}" placeholder="e.g. Main / 0100" 
                            class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">IFSC / SWIFT</label>
                        <input type="text" name="swift_ifsc" value="{{ $dashBank['swift_ifsc'] ?? '' }}" placeholder="e.g. KCBLKENX" 
                            class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none focus:border-amber-500">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closePaymentModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-md">Save Payout Details →</button>
            </div>
        </form>
    </div>
</div>

<script>
    function filterGigs(filter) {
        ['all', 'accepted', 'week', 'pending'].forEach(f => {
            const btn = document.getElementById(`gig-tab-${f}`);
            if (btn) btn.className = 'px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-200';
        });
        const activeBtn = document.getElementById(`gig-tab-${filter}`);
        if (activeBtn) activeBtn.className = 'px-4 py-2 rounded-xl transition-all bg-amber-500 text-slate-950 font-black shadow-sm';

        const rows = document.querySelectorAll('.gig-row');
        rows.forEach(row => {
            const status = row.getAttribute('data-status') || '';
            if (filter === 'all') {
                row.style.display = '';
            } else if (filter === 'accepted' && status === 'accepted') {
                row.style.display = '';
            } else if (filter === 'pending' && status === 'assigned') {
                row.style.display = '';
            } else if (filter === 'week') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function switchDashPayoutTab(type) {
        const inputType = document.getElementById('dash_input_account_type');
        const btnMobile = document.getElementById('dash-tab-mobile');
        const btnBank = document.getElementById('dash-tab-bank');
        const boxMobile = document.getElementById('dash-box-mobile');
        const boxBank = document.getElementById('dash-box-bank');

        if (inputType) inputType.value = type;

        if (type === 'mobile_money') {
            btnMobile.className = 'py-2 rounded-xl transition-all bg-amber-500 text-slate-950 shadow-xs';
            btnBank.className = 'py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-200';
            boxMobile.classList.remove('hidden');
            boxBank.classList.add('hidden');
        } else {
            btnBank.className = 'py-2 rounded-xl transition-all bg-amber-500 text-slate-950 shadow-xs';
            btnMobile.className = 'py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-200';
            boxBank.classList.remove('hidden');
            boxMobile.classList.add('hidden');
        }
    }

    function openPaymentModal() { document.getElementById('payment-modal').classList.replace('hidden', 'flex'); }
    function closePaymentModal() { document.getElementById('payment-modal').classList.replace('flex', 'hidden'); }
</script>
@endsection
