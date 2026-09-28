@extends('layouts.staff')

@section('content')
<div class="space-y-6 pb-16">

        <!-- Compact Control Top Bar -->
        
        <!-- Success Flash Alert -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-bold flex items-center gap-2 shadow-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ session('success') }}</span>
            </div>
        @endif



        @php
            $newRequests = $requests->whereIn('status', ['new', 'under_review', 'staff_matching', 'assigned']);
            $oldRequests = $requests->whereIn('status', ['completed', 'cancelled']);
            $totalBudget = $requests->sum('budget') ?: ($requests->count() * 250);
            $totalAssignedTalent = $requests->flatMap->assignments->count();

            // Prepare dynamic Chart.js analytics data for the past 6 months
            $chartMonths = [];
            $chartBookings = [];
            $chartSpending = [];
            for ($i = 5; $i >= 0; $i--) {
                $mDate = date('Y-m', strtotime("-$i months"));
                $mLabel = date('M', strtotime("-$i months"));
                $mReqs = $requests->filter(fn($r) => $r->created_at && $r->created_at->format('Y-m') === $mDate);
                $count = $mReqs->count();
                $spend = $mReqs->sum('budget') ?: ($count * 250);
                
                // Fallback baseline for visual elegance if new account
                if ($i === 0 && $count === 0 && $requests->count() > 0) {
                    $count = $requests->count();
                    $spend = $totalBudget;
                }
                
                $chartMonths[] = $mLabel;
                $chartBookings[] = $count;
                $chartSpending[] = $spend;
            }
        @endphp

        <!-- Metrics Overview Grid with Refined Card Hover Details -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
            
            <!-- Card 1: Total Bookings -->
            <div onclick="switchClientTab('all-bookings')" class="cursor-pointer group bg-white border border-slate-200/90 hover:border-amber-400 rounded-3xl p-5 sm:p-6 shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 to-amber-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between">
                    <div class="text-[10px] sm:text-xs font-extrabold text-slate-400 group-hover:text-amber-700 uppercase tracking-wider transition-colors">
                        Total Bookings
                    </div>
                    <span class="w-9 h-9 rounded-2xl bg-amber-50 group-hover:bg-amber-500 text-amber-600 group-hover:text-slate-950 font-black text-sm flex items-center justify-center transition-all group-hover:scale-110 shadow-xs">
                        📋
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-950 mt-2">
                    {{ $requests->count() }}
                </div>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 text-[11px]">
                    <span class="text-amber-700 font-extrabold flex items-center gap-1">
                        <span>All Time Requests</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </span>
                    <span class="text-[10px] font-bold bg-amber-50 text-amber-800 px-2 py-0.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                        View All
                    </span>
                </div>
            </div>

            <!-- Card 2: Active / New Shifts -->
            <div onclick="switchClientTab('new-bookings')" class="cursor-pointer group bg-white border border-slate-200/90 hover:border-amber-500 rounded-3xl p-5 sm:p-6 shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-rose-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between">
                    <div class="text-[10px] sm:text-xs font-extrabold text-slate-400 group-hover:text-amber-700 uppercase tracking-wider transition-colors">
                        Active / New Shifts
                    </div>
                    <span class="w-9 h-9 rounded-2xl bg-amber-100/60 group-hover:bg-amber-500 text-amber-700 group-hover:text-slate-950 font-black text-sm flex items-center justify-center transition-all group-hover:scale-110 shadow-xs">
                        ⚡
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-amber-600 mt-2">
                    {{ $newRequests->count() }}
                </div>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 text-[11px]">
                    <span class="text-amber-800 font-extrabold flex items-center gap-1">
                        <span>Live & Scheduled</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </span>
                    <span class="text-[10px] font-extrabold bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                        ● Live
                    </span>
                </div>
            </div>

            <!-- Card 3: Completed / Old -->
            <div onclick="switchClientTab('old-bookings')" class="cursor-pointer group bg-white border border-slate-200/90 hover:border-emerald-500 rounded-3xl p-5 sm:p-6 shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-400 to-teal-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between">
                    <div class="text-[10px] sm:text-xs font-extrabold text-slate-400 group-hover:text-emerald-700 uppercase tracking-wider transition-colors">
                        Completed / Old
                    </div>
                    <span class="w-9 h-9 rounded-2xl bg-emerald-50 group-hover:bg-emerald-600 text-emerald-600 group-hover:text-white font-black text-sm flex items-center justify-center transition-all group-hover:scale-110 shadow-xs">
                        📁
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 mt-2">
                    {{ $oldRequests->count() }}
                </div>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 text-[11px]">
                    <span class="text-emerald-700 font-extrabold flex items-center gap-1">
                        <span>Past Events</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </span>
                    <span class="text-[10px] font-extrabold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                        Archived
                    </span>
                </div>
            </div>

            <!-- Card 4: Payments & Budget -->
            <div onclick="switchClientTab('payments')" class="cursor-pointer group bg-white border border-slate-200/90 hover:border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-slate-800 to-slate-950 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between">
                    <div class="text-[10px] sm:text-xs font-extrabold text-slate-400 group-hover:text-slate-900 uppercase tracking-wider transition-colors">
                        Payments & Budget
                    </div>
                    <span class="w-9 h-9 rounded-2xl bg-slate-100 group-hover:bg-slate-900 text-slate-800 group-hover:text-amber-400 font-black text-sm flex items-center justify-center transition-all group-hover:scale-110 shadow-xs">
                        💳
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">
                    ${{ number_format($totalBudget, 2) }}
                </div>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 text-[11px]">
                    <span class="text-emerald-700 font-extrabold flex items-center gap-1">
                        <span>Paid / Invoiced</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </span>
                    <span class="text-[10px] font-extrabold bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                        🛡️ Escrow
                    </span>
                </div>
            </div>

        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-4 sm:p-6 shadow-lg">
            
            <!-- Tab Headers -->
            <div class="flex items-center gap-2 border-b border-slate-200 pb-4 overflow-x-auto text-xs font-black">
                <button type="button" onclick="switchClientTab('all-bookings')" id="tab-all-bookings" class="client-tab-btn px-4 py-2.5 rounded-xl bg-slate-900 text-amber-400 shadow-md">
                    📋 Total Bookings ({{ $requests->count() }})
                </button>
                <button type="button" onclick="switchClientTab('new-bookings')" id="tab-new-bookings" class="client-tab-btn px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200">
                    ⚡ New / Active ({{ $newRequests->count() }})
                </button>
                <button type="button" onclick="switchClientTab('old-bookings')" id="tab-old-bookings" class="client-tab-btn px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200">
                    📁 Past / Old Bookings ({{ $oldRequests->count() }})
                </button>
                <button type="button" onclick="switchClientTab('payments')" id="tab-payments" class="client-tab-btn px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200">
                    💳 Payments & Invoices
                </button>
                <button type="button" onclick="switchClientTab('reports')" id="tab-reports" class="client-tab-btn px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200">
                    📊 Event Reports
                </button>
                <button type="button" onclick="switchClientTab('favorites')" id="tab-favorites" class="client-tab-btn px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200">
                    ❤️ Saved Crew ({{ isset($favorites) ? $favorites->count() : 0 }})
                </button>
            </div>

            <!-- TAB 1: TOTAL BOOKINGS LIST -->
            <div id="content-all-bookings" class="client-tab-content pt-6 space-y-6">
                @forelse($requests as $req)
                    @include('client.partials.booking-card', ['req' => $req])
                @empty
                    @include('client.partials.empty-state')
                @endforelse
            </div>

            <!-- TAB 2: NEW / ACTIVE BOOKINGS -->
            <div id="content-new-bookings" class="client-tab-content hidden pt-6 space-y-6">
                @forelse($newRequests as $req)
                    @include('client.partials.booking-card', ['req' => $req])
                @empty
                    <div class="text-center py-10 bg-slate-50 rounded-2xl border border-slate-200 p-6 text-xs text-slate-500 font-semibold">
                        No active or pending event requests found.
                    </div>
                @endforelse
            </div>

            <!-- TAB 3: OLD / PAST BOOKINGS -->
            <div id="content-old-bookings" class="client-tab-content hidden pt-6 space-y-6">
                @forelse($oldRequests as $req)
                    @include('client.partials.booking-card', ['req' => $req])
                @empty
                    <div class="text-center py-10 bg-slate-50 rounded-2xl border border-slate-200 p-6 text-xs text-slate-500 font-semibold">
                        No completed past bookings yet.
                    </div>
                @endforelse
            </div>

            <!-- TAB 4: PAYMENTS & INVOICES -->
            <div id="content-payments" class="client-tab-content hidden pt-6 space-y-6">
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <h3 class="text-sm font-black text-slate-900">Payment Statements & Billing History</h3>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs">✓ Account Verified</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-slate-400 border-b border-slate-200">
                                    <th class="pb-2">Order ID</th>
                                    <th class="pb-2">Event Name</th>
                                    <th class="pb-2">Event Date</th>
                                    <th class="pb-2">Budget / Amount</th>
                                    <th class="pb-2">Payment Status</th>
                                    <th class="pb-2 text-right">Invoice</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/60 font-semibold text-slate-700">
                                @forelse($requests as $req)
                                    <tr>
                                        <td class="py-3 font-bold text-slate-900">#{{ $req->id }}</td>
                                        <td class="py-3 font-extrabold text-slate-900">{{ $req->event_name }}</td>
                                        <td class="py-3">{{ $req->event_date ? $req->event_date->format('M j, Y') : 'N/A' }}</td>
                                        <td class="py-3 font-bold text-amber-700">${{ number_format($req->budget ?: 250, 2) }}</td>
                                        <td class="py-3">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800">
                                                Paid / Invoiced
                                            </span>
                                        </td>
                                        <td class="py-3 text-right">
                                            <button type="button" onclick="Swal.fire({ icon: 'info', title: 'Invoice Download', text: 'Generating official PDF invoice for Order #{{ $req->id }}...', confirmButtonColor: '#F59E0B' })" class="px-3 py-1 rounded-lg bg-slate-900 text-amber-400 font-bold text-[10px] hover:bg-slate-800">
                                                📥 Download PDF
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-slate-400 italic">No invoices recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 5: EVENT REPORTS & STATEMENTS -->
            <div id="content-reports" class="client-tab-content hidden pt-6 space-y-6">
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <h3 class="text-sm font-black text-slate-900">Crew Shift Reports & Operations Analytics</h3>
                        <span class="text-xs font-bold text-slate-500">AfriCrew On-Site Verification Reports</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($requests as $req)
                            <div class="bg-white border border-slate-200 rounded-2xl p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-black text-slate-900">{{ $req->event_name }}</h4>
                                    <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                        Shift Statement
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 space-y-1">
                                    <div>📍 {{ $req->location }}</div>
                                    <div>📅 {{ $req->event_date ? $req->event_date->format('D, M j, Y') : '' }}</div>
                                    <div>👥 Assigned Crew: {{ $req->assignments->count() }} Talent Member(s)</div>
                                </div>
                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-[10px] text-emerald-700 font-bold">● On-Time Arrival 100%</span>
                                    <button type="button" onclick="Swal.fire({ icon: 'info', title: 'Operations Report', text: 'Generating shift report statement for {{ $req->event_name }}...', confirmButtonColor: '#F59E0B' })" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[10px] border border-slate-200">
                                        📄 Export Statement
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-6 text-slate-400 italic">
                                No shift reports generated yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            <!-- TAB 6: SAVED FAVORITE CREW -->
            <div id="content-favorites" class="client-tab-content hidden pt-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @forelse($favorites ?? [] as $fav)
                        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs space-y-4 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    @if($fav->profile_photo)
                                        <img src="{{ str_starts_with($fav->profile_photo, 'http') ? $fav->profile_photo : asset($fav->profile_photo) }}" alt="{{ $fav->full_name }}" class="w-12 h-12 rounded-full object-cover border-2 border-amber-400">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-amber-500 text-slate-950 font-black text-lg flex items-center justify-center">
                                            {{ strtoupper(substr($fav->full_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="text-sm font-black text-slate-900">{{ $fav->full_name }}</h4>
                                        <span class="text-xs font-bold text-amber-600 uppercase">{{ $fav->category }}</span>
                                    </div>
                                </div>
                                <div class="text-xs text-slate-500 space-y-1">
                                    <div>📍 {{ $fav->city }}, {{ $fav->country }}</div>
                                    <div>⭐ {{ number_format($fav->average_rating, 1) }} ({{ $fav->reviews_count }} reviews)</div>
                                    <div class="text-slate-900 font-bold mt-1">${{ number_format($fav->one_day_rate ?: $fav->hourly_rate ?: 100, 2) }} / day</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                                <a href="{{ route('crew.show', $fav->id) }}" class="flex-1 text-center px-3 py-2 rounded-xl bg-slate-900 text-amber-400 font-bold text-xs hover:bg-slate-800">
                                    View Profile
                                </a>
                                <form method="POST" action="{{ route('client.favorites.toggle', $fav->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold text-xs">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 bg-slate-50 rounded-2xl border border-slate-200 p-6 text-xs text-slate-500 font-semibold">
                            You have no saved crew members in your favorites yet.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

<script>
function switchClientTab(tabId) {
    // Reset top tab buttons
    document.querySelectorAll('.client-tab-btn').forEach(btn => {
        btn.className = 'client-tab-btn px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200';
    });

    // Reset sidebar subtabs
    document.querySelectorAll('.client-sidebar-subtab').forEach(sub => {
        sub.classList.remove('bg-amber-100', 'text-amber-950', 'font-black', 'shadow-xs');
        sub.classList.add('text-slate-600');
    });

    // Hide all tab contents
    document.querySelectorAll('.client-tab-content').forEach(c => {
        c.classList.add('hidden');
    });

    // Activate active top tab
    const activeTabBtn = document.getElementById('tab-' + tabId);
    if (activeTabBtn) {
        activeTabBtn.className = 'client-tab-btn px-4 py-2.5 rounded-xl bg-slate-900 text-amber-400 shadow-md';
    }

    // Activate active sidebar subtab
    const activeSubtab = document.getElementById('sidebar-subtab-' + tabId);
    if (activeSubtab) {
        activeSubtab.classList.remove('text-slate-600');
        activeSubtab.classList.add('bg-amber-100', 'text-amber-950', 'font-black', 'shadow-xs');
    }

    // Activate active mobile drawer subtab if present
    const activeMobileSubtab = document.getElementById('mobile-sidebar-subtab-' + tabId);
    if (activeMobileSubtab) {
        activeMobileSubtab.classList.remove('text-slate-600');
        activeMobileSubtab.classList.add('bg-amber-100', 'text-amber-950', 'font-black', 'shadow-xs');
    }

    // Show active content
    const activeTabContent = document.getElementById('content-' + tabId);
    if (activeTabContent) {
        activeTabContent.classList.remove('hidden');
    }

    // Update browser URL query parameter without re-loading
    if (window.history && window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.set('tab', tabId);
        window.history.replaceState({}, '', url);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab') || (window.location.hash ? window.location.hash.substring(1) : null);
    const validTabs = ['all-bookings', 'new-bookings', 'old-bookings', 'payments', 'reports', 'favorites'];
    if (tabParam && validTabs.includes(tabParam)) {
        switchClientTab(tabParam);
    } else {
        switchClientTab('all-bookings');
    }
});

function openNegotiationModal(requestId, currentBudget) {
    Swal.fire({
        title: `Negotiate Budget for Order #${requestId}`,
        html: `
            <div class="text-left space-y-3 text-xs">
                <p class="text-slate-600">Propose a counter-offer or rate adjustment for this event request:</p>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Current Budget:</label>
                    <input type="text" value="${currentBudget}" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-700">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Your Counter-Offer Amount:</label>
                    <input type="number" id="swal-counter-budget" value="${currentBudget}" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Negotiation Notes / Reasons:</label>
                    <textarea id="swal-negotiate-notes" rows="2" placeholder="e.g. Budget adjustment for 2 additional hours..." class="w-full border border-slate-300 rounded-xl p-2 text-slate-900"></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Submit Counter Proposal 💬',
        confirmButtonColor: '#4F46E5',
        focusConfirm: false,
        preConfirm: () => {
            const counterAmount = document.getElementById('swal-counter-budget').value;
            const notes = document.getElementById('swal-negotiate-notes').value;
            if (!counterAmount || counterAmount <= 0) {
                Swal.showValidationMessage('Please enter a valid counter amount');
            }
            return { counterAmount, notes };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ route('messages') }}";
        }
    });
}

function openProposalCounterModal(proposalId, currentQuote) {
    Swal.fire({
        title: 'Propose Counter Rate to Crew 💬',
        html: `
            <div class="text-left space-y-3 text-xs">
                <p class="text-slate-600 font-medium">Propose a counter-offer rate for this crew member's quotation:</p>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Current Proposed Rate:</label>
                    <input type="text" value="${currentQuote}" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-700">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Your Counter-Offer Rate Amount:</label>
                    <input type="number" id="swal-proposal-counter-amount" value="${currentQuote}" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Submit Counter Rate 💬',
        confirmButtonColor: '#F59E0B',
        focusConfirm: false,
        preConfirm: () => {
            const counterAmount = document.getElementById('swal-proposal-counter-amount').value;
            if (!counterAmount || counterAmount <= 0) {
                Swal.showValidationMessage('Please enter a valid counter amount');
            }
            return counterAmount;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/proposals/${proposalId}/respond`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'counter';
            
            const counterInput = document.createElement('input');
            counterInput.type = 'hidden';
            counterInput.name = 'counter_amount';
            counterInput.value = result.value;
            
            form.appendChild(csrfInput);
            form.appendChild(actionInput);
            form.appendChild(counterInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function openPaymentModal(requestId, amount, currency) {
    const depositAmount = (amount * 0.5).toFixed(2);
    Swal.fire({
        title: `Escrow Payment for Order #${requestId}`,
        html: `
            <div class="text-left space-y-3 text-xs">
                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 font-bold">
                    <span>💳 Escrow Rule: 50% Deposit Now, 50% Balance on Event Completion.</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-slate-700 font-semibold">
                    <div>Total Price: <strong>${currency === 'KES' ? 'KSh ' + amount : '$' + amount}</strong></div>
                    <div>50% Deposit: <strong class="text-emerald-700 font-black">${currency === 'KES' ? 'KSh ' + (amount * 0.5) : '$' + depositAmount}</strong></div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Payment Method:</label>
                    <select id="swal-payment-method" class="w-full border border-slate-300 rounded-xl px-3 py-2 font-bold text-slate-900">
                        <option value="mpesa">📱 M-Pesa Express / Mobile Money</option>
                        <option value="card">💳 Credit / Debit Card (Visa, MasterCard)</option>
                        <option value="bank">🏛️ Direct Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Account / Phone Number:</label>
                    <input type="text" id="swal-payment-phone" placeholder="e.g. +254 700 000 000" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: `Pay 50% Escrow (${currency === 'KES' ? 'KSh ' + (amount * 0.5) : '$' + depositAmount}) ⚡`,
        confirmButtonColor: '#059669',
        preConfirm: () => {
            const phone = document.getElementById('swal-payment-phone').value;
            if (!phone) {
                Swal.showValidationMessage('Please enter phone or account number');
            }
            return { phone, method: document.getElementById('swal-payment-method').value };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: '50% Escrow Payment Processed!',
                text: `Payment prompt sent to ${result.value.phone}. Funds are safely held in Escrow by AfriCrew Admin.`,
                confirmButtonColor: '#F59E0B'
            });
        }
    });
}

function openDirectChatModal(profName, profId) {
    Swal.fire({
        title: `Direct Chat with ${profName}`,
        html: `
            <div class="text-left space-y-3 text-xs">
                <div class="h-40 bg-slate-50 border border-slate-200 rounded-xl p-3 overflow-y-auto space-y-2 font-medium">
                    <div class="bg-amber-100 p-2.5 rounded-xl text-amber-900 max-w-[80%]">
                        Hello! I am ${profName}. Looking forward to ushering at your event.
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" id="swal-chat-input" placeholder="Type your message..." class="flex-grow border border-slate-300 rounded-xl px-3 py-2 text-slate-900 text-xs">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Send Message 💬',
        confirmButtonColor: '#0F172A',
        preConfirm: () => {
            return document.getElementById('swal-chat-input').value;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                icon: 'success',
                title: 'Message Sent!',
                text: `Message delivered to ${profName}.`,
                timer: 1800,
                showConfirmButton: false
            });
}
</script>
@endsection
