<div class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-6 hover:shadow-xl hover:border-amber-400/80 transition-all duration-300 space-y-5 shadow-sm relative overflow-hidden group">
    
    <!-- Top Header & Actions -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
        <div class="space-y-1.5">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="w-2.5 h-2.5 rounded-full {{ $req->status === 'assigned' ? 'bg-emerald-500 animate-pulse' : ($req->status === 'new' ? 'bg-amber-500 animate-pulse' : 'bg-slate-400') }}"></span>
                <h3 class="text-lg sm:text-xl font-black text-slate-950 tracking-tight group-hover:text-amber-700 transition-colors">{{ $req->event_name }}</h3>
                
                <!-- Status Badge -->
                <span class="px-3 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider
                    {{ $req->status === 'assigned' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : ($req->status === 'new' ? 'bg-amber-50 text-amber-900 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                    {{ strtoupper($req->status) }}
                </span>

                <!-- Rate Type Badge -->
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-900 text-amber-400">
                    ⚡ {{ ucfirst($req->rate_type ?? 'fixed') }} Rate
                </span>
            </div>

            <!-- Event Details Info Bar -->
            <div class="text-xs text-slate-500 font-semibold flex flex-wrap items-center gap-x-4 gap-y-1 pt-0.5">
                <span>📍 <strong class="text-slate-800">{{ $req->location }}</strong></span>
                <span>📅 <strong class="text-slate-800">{{ $req->event_date ? $req->event_date->format('D, M j, Y') : 'TBD' }}</strong></span>
                <span>🎭 Category: <strong class="text-slate-800">{{ $req->category }}</strong></span>
                @if($req->event_type)
                    <span>🎪 Type: <strong class="text-slate-800">{{ $req->event_type }}</strong></span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap shrink-0 self-end md:self-center">
            <span class="text-xs font-black text-amber-900 bg-amber-500/10 px-3.5 py-1.5 rounded-2xl border border-amber-500/20">
                Order #{{ $req->id }}
            </span>

            <!-- Digital Agreement -->
            <a href="{{ route('contracts.forRequest', $req->id) }}" class="text-xs text-slate-900 font-black bg-slate-100 hover:bg-slate-900 hover:text-amber-400 px-3.5 py-1.5 rounded-2xl border border-slate-200 transition-all flex items-center gap-1.5 shadow-2xs">
                📜 Agreement
            </a>

            <!-- Proposal / Counter Negotiation -->
            <button type="button" onclick="openNegotiationModal({{ $req->id }}, {{ $req->budget ?: 250 }})" class="text-xs text-indigo-900 font-black bg-indigo-50 hover:bg-indigo-600 hover:text-white px-3.5 py-1.5 rounded-2xl border border-indigo-200 transition-all flex items-center gap-1.5 shadow-2xs">
                💬 Negotiate Rate
            </button>
        </div>
    </div>

    <!-- Shift Specifications Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80">
        <div>
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">📅 Event Date & Duration</span>
            <span class="font-black text-slate-900 text-xs sm:text-sm mt-0.5 block">{{ $req->event_date ? $req->event_date->format('D, M j, Y') : 'TBD' }}</span>
            <span class="text-[10px] text-amber-700 font-bold block">{{ str_replace('_', ' ', ucfirst($req->shift_duration ?? '1 Day')) }}</span>
        </div>

        <div>
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">👥 Talent Headcount</span>
            <span class="font-black text-slate-900 text-xs sm:text-sm mt-0.5 block">{{ $req->staff_count }} Member(s)</span>
            <span class="text-[10px] text-slate-500 font-bold block">{{ $req->category }}</span>
        </div>

        <div>
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">💰 Total Budget</span>
            <span class="font-black text-amber-700 text-xs sm:text-sm mt-0.5 block">
                {{ ($req->currency ?? 'USD') === 'KES' ? 'KSh ' . number_format($req->budget ?: 3500) : '$' . number_format($req->budget ?: 250, 2) }}
            </span>
            <span class="text-[10px] text-slate-500 font-bold block">AfriCrew Escrow</span>
        </div>

        <div>
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">🛡️ Payment Status</span>
            <span class="font-black text-xs sm:text-sm mt-0.5 block {{ $req->payment_status === 'paid' ? 'text-emerald-700' : 'text-amber-800' }}">
                {{ $req->payment_status === 'paid' ? '✓ Paid in Full' : ($req->payment_status === 'deposit_paid' ? '✓ 50% Escrow Paid' : 'Pending Escrow') }}
            </span>
            <span class="text-[10px] text-slate-500 font-bold block">Escrow Protected</span>
        </div>
    </div>

    <!-- Escrow Terms & Action Bar -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 text-white border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-md">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 font-black text-lg flex items-center justify-center shrink-0 border border-amber-500/30">
                💳
            </div>
            <div>
                <span class="font-black text-xs sm:text-sm text-white block">Escrow Protection Policy (50% Deposit / 50% Balance)</span>
                <span class="text-[11px] text-slate-300 font-medium">50% is safely reserved in Escrow upon confirmation; 50% balance after shift completion.</span>
            </div>
        </div>

        @if($req->payment_status !== 'paid')
            <button type="button" onclick="openPaymentModal({{ $req->id }}, {{ $req->budget ?: 250 }}, '{{ $req->currency ?? 'USD' }}')" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg transition-all shrink-0 flex items-center gap-1.5 border border-emerald-400">
                <span>Pay Escrow Amount ⚡</span>
            </button>
        @else
            <span class="px-4 py-2 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 font-black text-xs shrink-0 flex items-center gap-1">
                ✓ Escrow Fully Paid
            </span>
        @endif
    </div>

    <!-- Active Quotations & Proposals from Crew -->
    @if($req->proposals && $req->proposals->count() > 0)
        <div class="p-4.5 bg-indigo-50/70 rounded-2xl border border-indigo-200 space-y-3 text-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-black uppercase text-indigo-950 tracking-wider">💬 Crew Quotations & Travel Proposals</span>
                <span class="text-[10px] font-bold text-indigo-800 bg-indigo-100 px-2 py-0.5 rounded-md">Direct Proposals</span>
            </div>

            <div class="space-y-2.5">
                @foreach($req->proposals as $prop)
                    <div class="p-3.5 bg-white rounded-2xl border border-indigo-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xs">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-black text-slate-950 text-xs sm:text-sm">{{ $prop->professional->full_name ?? 'Usher Candidate' }}</span>
                                <span class="px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-900 font-black text-[9px] uppercase">
                                    {{ strtoupper($prop->status) }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-700 font-medium flex flex-wrap items-center gap-2">
                                <span>Proposed Rate: <strong class="text-slate-950 font-black">KES {{ number_format($prop->custom_quote_amount) }}</strong></span>
                                @if($prop->travel_fee > 0)
                                    <span>• Transport: <strong class="text-slate-950">KES {{ number_format($prop->travel_fee) }}</strong></span>
                                @endif
                                @if($prop->accommodation_fee > 0)
                                    <span>• Food/Accomm: <strong class="text-slate-950">KES {{ number_format($prop->accommodation_fee) }}</strong></span>
                                @endif
                            </div>
                            @if($prop->notes)
                                <p class="text-[11px] text-slate-600 italic bg-slate-50 p-2 rounded-xl border border-slate-200 mt-1 font-normal">
                                    "{{ $prop->notes }}"
                                </p>
                            @endif
                        </div>

                        @if($prop->status === 'pending' || $prop->status === 'countered')
                            <div class="flex items-center gap-2 flex-wrap shrink-0">
                                <!-- Accept Quote -->
                                <form method="POST" action="{{ route('proposals.respond', $prop) }}" class="m-0">
                                    @csrf
                                    <input type="hidden" name="action" value="accept">
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-2xs border-0">
                                        ✓ Accept Quote
                                    </button>
                                </form>

                                <!-- Client Counter Rate Button -->
                                <button type="button" onclick="openProposalCounterModal({{ $prop->id }}, {{ $prop->custom_quote_amount ?: 250 }})" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-2xs border-0">
                                    💬 Counter Rate
                                </button>

                                <!-- Live Chat Link -->
                                <a href="{{ route('messages') }}" class="px-3 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-bold text-xs text-decoration-none">
                                    💬 Chat
                                </a>

                                <!-- Reject Quote -->
                                <form method="POST" action="{{ route('proposals.respond', $prop) }}" class="m-0">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-xs border border-rose-200">
                                        Decline
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="px-3.5 py-1.5 rounded-xl bg-emerald-100 text-emerald-900 font-black text-xs border border-emerald-200">
                                ✓ Agreed Proposal
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Assigned Vetted Crew Members Section & Direct Messaging -->
    <div class="pt-1">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-black uppercase text-slate-400 tracking-wider">Assigned Vetted Crew Members</span>
            <span class="text-[11px] font-black text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                💬 Live Messaging Active
            </span>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($req->assignments as $assign)
                @php $p = $assign->professional; @endphp
                @if($p)
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-3.5 flex items-center justify-between gap-3 shadow-2xs hover:border-amber-400 transition-colors">
                        <div class="flex items-center gap-3">
                            @if($p->profile_photo)
                                @php
                                    $photoUrl = get_storage_url($p->profile_photo);
                                @endphp
                                <img src="{{ $photoUrl }}" alt="{{ $p->full_name }}" class="w-11 h-11 rounded-xl object-cover border-2 border-amber-400 shrink-0">
                            @else
                                <div class="w-11 h-11 rounded-xl bg-slate-900 text-amber-400 font-black text-sm flex items-center justify-center shrink-0 border border-amber-400/40">
                                    {{ strtoupper(substr($p->full_name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('crew.show', $p) }}" class="text-xs font-black text-slate-950 hover:text-amber-600 truncate block">
                                    {{ $p->full_name }}
                                </a>
                                <span class="text-[10px] text-amber-800 font-black block uppercase tracking-tight">{{ $p->category }}</span>
                                <span class="text-[10px] text-slate-500 font-semibold block">📞 {{ $p->phone ?: 'Assigned' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($p->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->phone) }}?text={{ urlencode('Hello ' . $p->full_name . ', regarding event order #' . $req->id) }}" target="_blank" class="w-8.5 h-8.5 rounded-xl bg-emerald-50 hover:bg-emerald-500 hover:text-white text-emerald-700 font-bold flex items-center justify-center text-sm transition-all border border-emerald-200" title="Contact via WhatsApp">
                                    💬
                                </a>
                            @endif
                            <button type="button" onclick="openDirectChatModal('{{ $p->full_name }}', {{ $p->id }})" class="w-8.5 h-8.5 rounded-xl bg-slate-100 hover:bg-slate-900 hover:text-amber-400 text-slate-800 font-bold flex items-center justify-center text-xs transition-all border border-slate-200" title="Direct In-App Chat">
                                ✉️
                            </button>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-span-full text-xs text-slate-500 font-semibold italic p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                    ⚡ Our operations team is currently matching and assigning the best vetted talent for this event request.
                </div>
            @endforelse
        </div>
    </div>

</div>
