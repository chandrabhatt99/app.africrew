@extends('layouts.staff')

@section('content')
<div class="space-y-8 pb-16">

    <!-- Page Header & Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Shifts & Schedule Tracker</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Track client hire requests, accept/decline offers, check your event schedule & monitor payouts.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('professional.wallet') }}" class="px-4 py-2 rounded-2xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs shadow-md transition-all flex items-center gap-1.5">
                <span>💳 Crew Wallet & Withdrawals</span>
            </a>
            <span class="px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 font-extrabold text-xs">
                ✓ Live Earnings & Schedule
            </span>
        </div>
    </div>

    <!-- Earnings & Payout Summary Widgets -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total Realized Payouts</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 font-black flex items-center justify-center text-sm">💵</span>
            </div>
            <span class="text-2xl font-black text-emerald-600 block">KES {{ number_format($earnings['realized'] ?? 0, 2) }}</span>
            <span class="text-[11px] text-slate-400 font-medium block">Completed past event shifts paid out</span>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Expected Upcoming Payouts</span>
                <span class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 font-black flex items-center justify-center text-sm">📅</span>
            </div>
            <span class="text-2xl font-black text-slate-900 block">KES {{ number_format($earnings['upcoming'] ?? 0, 2) }}</span>
            <span class="text-[11px] text-slate-400 font-medium block">Accepted upcoming confirmed shifts</span>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Pending Offers Potential</span>
                <span class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 font-black flex items-center justify-center text-sm">⏳</span>
            </div>
            <span class="text-2xl font-black text-amber-600 block">KES {{ number_format($earnings['pending'] ?? 0, 2) }}</span>
            <span class="text-[11px] text-slate-400 font-medium block">Action required to accept/decline</span>
        </div>

    </div>

    <!-- Interactive Crew Booking & Availability Calendar Component -->
    @include('partials.crew-calendar')

    <!-- Shift Offers & Schedule Management Container -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        
        <!-- Filter Tabs -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 flex-wrap gap-4">
            <h3 class="text-base font-black text-slate-900">Shift Assignments Timeline</h3>

            <div class="flex items-center gap-1.5 bg-slate-100 p-1.5 rounded-2xl text-xs font-extrabold overflow-x-auto">
                <button onclick="filterShifts('all')" id="shift-tab-all" class="px-4 py-2 rounded-xl transition-all bg-amber-500 text-slate-950 font-black shadow-sm">
                    All Shifts
                </button>
                <button onclick="filterShifts('assigned')" id="shift-tab-assigned" class="px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-200">
                    Pending Offers (Action Required)
                </button>
                <button onclick="filterShifts('accepted')" id="shift-tab-accepted" class="px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-200">
                    Upcoming Confirmed
                </button>
                <button onclick="filterShifts('completed')" id="shift-tab-completed" class="px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-200">
                    Past Completed
                </button>
            </div>
        </div>

        <!-- Shift Cards Container -->
        @if($assignments->isEmpty())
            <div class="p-12 text-center border-2 border-dashed border-slate-200 rounded-3xl space-y-2">
                <span class="text-4xl block mb-2">📋</span>
                <h4 class="text-sm font-black text-slate-900">No Shift Offers Yet</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto">When clients book you directly or organizers assign you to a shift, your hire offers will appear here for you to accept or reject.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($assignments as $job)
                    @php
                        $req = $job->staffingRequest;
                        $status = strtolower($job->status);
                        $proposal = $req?->proposals?->where('professional_id', $professional->id)->first() ?? $req?->proposals?->first();
                        $isNegotiable = ($req?->rate_type === 'negotiable') || ($proposal !== null);
                        $propId = $proposal?->id;
                        $reqId = $req?->id;
                        $estPay = $proposal?->counter_amount ?? ($req?->budget ?? ($professional->one_day_rate ?? 5000.00));
                    @endphp
                    <div class="shift-item-card p-6 rounded-3xl border transition-all space-y-4 {{ $status === 'assigned' ? 'bg-amber-500/10 border-amber-500/40 shadow-sm' : 'bg-slate-50/60 border-slate-200/90' }}" data-status="{{ $status }}">
                        
                        <!-- Top Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/60 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl {{ $status === 'assigned' ? 'bg-amber-500 text-slate-950' : ($status === 'accepted' ? 'bg-emerald-500 text-white' : 'bg-slate-900 text-white') }} font-black flex items-center justify-center text-sm shrink-0">
                                    {{ $status === 'assigned' ? '⚡' : ($status === 'accepted' ? '✓' : '🏛️') }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-black text-slate-900">{{ $req->event_name ?? 'Event Shift Request' }}</h4>
                                        @if($isNegotiable)
                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[9px] uppercase tracking-wider">
                                                🤝 Negotiable Rate
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-medium">Client: <strong>{{ $req->full_name ?? 'Marcus Vance' }}</strong> {{ $req->company_name ? '('.$req->company_name.')' : '' }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-xs font-black text-slate-900 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-xs">
                                    Est. Pay: <span class="text-amber-600 font-black">KES {{ number_format($estPay, 2) }}</span>
                                </span>

                                @if($status === 'assigned')
                                    <span class="px-3 py-1 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] uppercase tracking-wider animate-pulse">
                                        Action Required
                                    </span>
                                @elseif($status === 'accepted')
                                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-700 border border-emerald-500/30 font-black text-[10px] uppercase">
                                        Confirmed Shift
                                    </span>
                                @elseif($status === 'declined')
                                    <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-700 border border-rose-500/30 font-black text-[10px] uppercase">
                                        Declined
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-slate-200 text-slate-700 font-black text-[10px] uppercase">
                                        {{ $status }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Shift Specs Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div class="p-3 rounded-2xl bg-white border border-slate-200/80">
                                <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block mb-1">Shift Date & Duration</span>
                                <span class="font-black text-slate-900 block">📅 {{ date('D, M j, Y', strtotime($req->event_date ?? now())) }}</span>
                                <span class="text-[11px] text-amber-700 font-extrabold mt-0.5 block">⚡ Duration: {{ str_replace('_', ' ', ucfirst($req->shift_duration ?? '1 Day')) }}</span>
                            </div>

                            <div class="p-3 rounded-2xl bg-white border border-slate-200/80">
                                <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block mb-1">Venue Location</span>
                                <span class="font-black text-slate-900 block">📍 {{ $req->location ?? 'Nairobi Venue' }}</span>
                                <span class="text-[11px] text-slate-500 font-medium mt-0.5 block">Role: {{ $req->category ?? $professional->category }}</span>
                            </div>

                            <div class="p-3 rounded-2xl bg-white border border-slate-200/80">
                                <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block mb-1">Client Contact</span>
                                <span class="font-black text-slate-900 block">📞 {{ $req->phone ?? '+254 700 000 000' }}</span>
                                <span class="text-[11px] text-slate-500 font-medium mt-0.5 block truncate">✉️ {{ $req->email ?? 'client@company.com' }}</span>
                            </div>
                        </div>

                        @if(!empty($req->requirements))
                            <div class="p-3 rounded-2xl bg-white border border-slate-200/80 text-xs text-slate-600 font-medium">
                                📌 <strong>Special Requirements / Dress Code:</strong> {{ $req->requirements }}
                            </div>
                        @endif

                        <!-- Action Buttons for Offers & Negotiation -->
                        @if($status === 'assigned' || $isNegotiable)
                            <div class="pt-3 border-t border-amber-500/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-xs text-amber-900 font-bold">
                                    @if($isNegotiable)
                                        ⚡ <strong>Negotiable Rate Offer:</strong> Client proposal: KES {{ number_format($req->budget ?? 0, 2) }}.
                                        @if($proposal && $proposal->counter_amount > 0)
                                            (Latest Counter: <strong class="text-emerald-700">KES {{ number_format($proposal->counter_amount, 2) }}</strong>)
                                        @endif
                                    @else
                                        ⚡ Acknowledge this shift request to confirm your availability to the client.
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto">
                                    <!-- 1. Accept Quote Button -->
                                    @if($proposal)
                                        <form method="POST" action="{{ route('messages.acceptPrice') }}" class="flex-1 sm:flex-none">
                                            @csrf
                                            <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">
                                            <button type="submit" onclick="return confirm('Accept rate KES {{ number_format($estPay, 2) }} and confirm booking?')"
                                                class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:brightness-110 text-white font-black text-xs shadow-md transition-all flex items-center justify-center gap-1 border-0">
                                                <span>✓ Accept Quote</span>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('professional.jobs.status', $job) }}" class="flex-1 sm:flex-none">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit"
                                                class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:brightness-110 text-white font-black text-xs shadow-md transition-all flex items-center justify-center gap-1 border-0">
                                                <span>✓ Accept Quote</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- 2. Counter Rate Button -->
                                    <button type="button" 
                                        onclick="openShiftCounterModal({{ $propId ? $propId : 'null' }}, {{ $reqId }}, {{ $proposal?->custom_quote_amount ?? ($req->budget ?? 3500) }}, {{ $proposal?->travel_fee ?? 0 }})"
                                        class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-sm transition-all flex items-center justify-center gap-1 border-0">
                                        <span>💬 Counter Rate</span>
                                    </button>

                                    <!-- 3. Chat Live Button -->
                                    <a href="{{ route('professional.messages') }}?request_id={{ $reqId }}" 
                                        class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs shadow-sm transition-all flex items-center justify-center gap-1">
                                        <span>💬 Chat</span>
                                </div>
                            </div>
                        @elseif($status === 'accepted')
                            <div class="pt-2 flex items-center justify-between text-xs text-emerald-700 font-bold">
                                <span>✅ You accepted this shift. Make sure to arrive 15 minutes before start time.</span>
                                
                                <form method="POST" action="{{ route('professional.jobs.status', $job) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-amber-400 font-black text-xs hover:bg-slate-800 transition-all border-0">
                                        Mark Shift Completed ✓
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif

    </div>

</div>

<!-- Shift Counter-Offer Modal -->
<div id="shift-counter-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-black text-slate-900">💼 Propose Counter-Offer Price</h3>
            <button type="button" onclick="closeShiftCounterModal()" class="text-slate-400 hover:text-slate-700 text-sm font-bold">✕</button>
        </div>

        <form method="POST" action="{{ route('messages.proposePrice') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="proposal_id" id="shift-modal-proposal-id">
            <input type="hidden" name="staffing_request_id" id="shift-modal-request-id">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Base Rate Quote (KES)</label>
                <input type="number" step="50" name="custom_quote_amount" id="shift-modal-quote-amount" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-bold outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Travel & Accommodation Allowance (KES)</label>
                <input type="number" step="50" name="travel_fee" id="shift-modal-travel-fee" value="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-bold outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Notes / Negotiated Conditions</label>
                <textarea name="notes" rows="2" placeholder="e.g. Includes full day shift + travel allowance..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-medium outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeShiftCounterModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-md">Submit Counter-Offer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openShiftCounterModal(propId, reqId, currentQuote, currentTravel) {
        document.getElementById('shift-modal-proposal-id').value = propId || '';
        document.getElementById('shift-modal-request-id').value = reqId || '';
        document.getElementById('shift-modal-quote-amount').value = currentQuote || 3500;
        document.getElementById('shift-modal-travel-fee').value = currentTravel || 0;
        document.getElementById('shift-counter-modal').classList.replace('hidden', 'flex');
    }

    function closeShiftCounterModal() {
        document.getElementById('shift-counter-modal').classList.replace('flex', 'hidden');
    }

    function filterShifts(filter) {
        ['all', 'assigned', 'accepted', 'completed'].forEach(f => {
            const btn = document.getElementById(`shift-tab-${f}`);
            if (btn) btn.className = 'px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-200';
        });

        const activeBtn = document.getElementById(`shift-tab-${filter}`);
        if (activeBtn) activeBtn.className = 'px-4 py-2 rounded-xl transition-all bg-amber-500 text-slate-950 font-black shadow-sm';

        const cards = document.querySelectorAll('.shift-item-card');
        cards.forEach(card => {
            const st = card.getAttribute('data-status');
            if (filter === 'all' || st === filter) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }
</script>
@endsection
