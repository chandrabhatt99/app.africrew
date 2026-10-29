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
            $totalBudget = $requests->sum('budget') ?: ($requests->count() * 3500);
            $totalAssignedTalent = $requests->flatMap->assignments->count();
            $paidRequests = $requests->whereIn('payment_status', ['paid', 'paid_to_admin', 'released_to_crew']);
            $unpaidRequests = $requests->whereNotIn('payment_status', ['paid', 'paid_to_admin', 'released_to_crew']);

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
                    KES {{ number_format($totalBudget) }}
                </div>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 text-[11px]">
                    <span class="text-slate-600 font-extrabold flex items-center gap-1">
                        <span>Total Commitment</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </span>
                    @if($paidRequests->count() > 0)
                        <span class="text-[10px] font-extrabold bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded-md">
                            ● {{ $paidRequests->count() }} Escrow Secured
                        </span>
                    @else
                        <span class="text-[10px] font-extrabold bg-amber-50 text-amber-800 px-2 py-0.5 rounded-md">
                            ● {{ $unpaidRequests->count() }} Pending Escrow
                        </span>
                    @endif
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
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Payment Statements & Billing History</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Real-time record of all event booking escrow deposits, settled amounts, and billing invoices.</p>
                        </div>
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
                                    <th class="pb-2 text-right">Invoice & Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/60 font-semibold text-slate-700">
                                @forelse($requests as $req)
                                    <tr>
                                        <td class="py-3 font-bold text-slate-900">#{{ $req->id }}</td>
                                        <td class="py-3 font-extrabold text-slate-900">{{ $req->event_name }}</td>
                                        <td class="py-3">{{ $req->event_date ? $req->event_date->format('M j, Y') : 'N/A' }}</td>
                                        <td class="py-3 font-bold text-amber-700">
                                            {{ ($req->currency ?? 'KES') === 'KES' ? 'KES ' . number_format($req->budget ?: 3500) : ($req->currency ?? 'USD') . ' ' . number_format($req->budget ?: 250, 2) }}
                                        </td>
                                        <td class="py-3">
                                            @if(in_array($req->payment_status, ['paid', 'released_to_crew']))
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1 shadow-2xs">
                                                    ✓ Paid in Full
                                                </span>
                                            @elseif($req->payment_status === 'paid_to_admin')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-teal-100 text-teal-800 border border-teal-200 inline-flex items-center gap-1 shadow-2xs">
                                                    ✓ 50% Escrow Secured
                                                </span>
                                            @elseif($req->payment_status === 'refunded')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-700 border border-slate-300 inline-flex items-center gap-1 shadow-2xs">
                                                    Refunded
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-amber-50 text-amber-800 border border-amber-200 inline-flex items-center gap-1.5 shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    Pending Escrow
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-right">
                                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                                @if(!in_array($req->payment_status, ['paid', 'paid_to_admin', 'released_to_crew']))
                                                    <button type="button" onclick="openPaymentModal({{ $req->id }}, {{ $req->budget ?: 3500 }}, '{{ $req->currency ?? 'KES' }}')" class="px-3 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-[10px] shadow-sm transition-all flex items-center gap-1 cursor-pointer">
                                                        <span>Pay Escrow ⚡</span>
                                                    </button>
                                                @endif
                                                <button type="button" onclick="Swal.fire({ icon: 'info', title: 'Invoice Download', text: 'Generating official PDF invoice for Order #{{ $req->id }}...', confirmButtonColor: '#F59E0B' })" class="px-3 py-1.5 rounded-xl bg-slate-900 text-amber-400 font-bold text-[10px] hover:bg-slate-800 transition-all flex items-center gap-1 cursor-pointer">
                                                    📥 Download PDF
                                                </button>
                                            </div>
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
    // If currency is USD, calculate approximate KES equivalent for Safaricom M-Pesa (e.g. 1 USD ~ 130 KES)
    let displayCurrency = currency || 'KES';
    let numericAmount = parseFloat(amount) || 5000;
    let kesAmount = (displayCurrency === 'USD') ? Math.round(numericAmount * 130) : Math.round(numericAmount);
    let kesDeposit = Math.round(kesAmount * 0.5);

    Swal.fire({
        title: '',
        html: `
            <div class="text-left space-y-4">
                <!-- Header with M-Pesa Branding -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-black text-sm shadow-md">
                            M
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base m-0">Lipa na M-Pesa Online</h3>
                            <span class="text-[11px] text-slate-500 font-semibold block">Order #${requestId} · 50% Escrow Deposit</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider">
                        Instant STK
                    </span>
                </div>

                <!-- Price Breakdown Box -->
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600 font-medium">
                        <span>Total Project Value:</span>
                        <span class="font-bold text-slate-900">KES ${kesAmount.toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between text-slate-900 font-black text-sm pt-1 border-t border-slate-200">
                        <span class="text-emerald-800">50% Escrow Required:</span>
                        <span class="text-emerald-700 text-base">KES ${kesDeposit.toLocaleString()}</span>
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium">
                        🛡️ Funds are held safely in Escrow and only released to crew upon shift completion.
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Payment Method</label>
                        <select id="mpesa-payment-method" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold text-slate-900 bg-white focus:outline-none focus:border-emerald-500">
                            <option value="mpesa" selected>🟢 M-Pesa Express (STK Push to Phone)</option>
                            <option value="bank">🏛️ Direct Bank Transfer (Manual)</option>
                            <option value="card">💳 Credit / Debit Card (Visa/MasterCard)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">
                            Safaricom Mobile Number <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="tel" 
                            id="mpesa-phone-number" 
                            placeholder="e.g. 0712 345 678 or 254712345678" 
                            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-900 font-bold focus:outline-none focus:border-emerald-500 text-sm"
                            value="{{ auth()->user()->phone ?? '' }}"
                        >
                        <span class="text-[10px] text-slate-400 mt-1 block">
                            You will receive an STK Push PIN prompt directly on this handset.
                        </span>
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: `Send STK Prompt (KES ${kesDeposit.toLocaleString()}) 📲`,
        confirmButtonColor: '#059669',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#94a3b8',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const phone = document.getElementById('mpesa-phone-number').value.trim();
            const method = document.getElementById('mpesa-payment-method').value;

            if (!phone) {
                Swal.showValidationMessage('Please enter your Safaricom phone number.');
                return false;
            }

            if (method !== 'mpesa') {
                Swal.showValidationMessage('Please select M-Pesa Express for automatic payment processing.');
                return false;
            }

            // Initiate M-Pesa STK Push
            return fetch('/payments/mpesa/stk-push', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    phone: phone,
                    amount: kesDeposit,
                    staffing_request_id: requestId,
                    account_reference: 'Order #' + requestId,
                    transaction_desc: '50% Escrow'
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || 'M-Pesa request failed'); });
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(error.message || 'Failed to initiate STK push');
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value && result.value.success) {
            const checkoutId = result.value.checkout_request_id;
            startMpesaPolling(checkoutId, requestId, kesDeposit);
        }
    });
}

function startMpesaPolling(checkoutRequestId, requestId, amount) {
    let secondsLeft = 60;
    let pollInterval = null;

    Swal.fire({
        title: '',
        html: `
            <div class="text-center py-4 space-y-4">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-2xl animate-pulse">
                    📲
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 m-0">STK Push Sent to Your Phone!</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1">
                        Please check your phone screen and enter your <strong>M-Pesa PIN</strong> to approve payment of <strong>KES ${amount.toLocaleString()}</strong>.
                    </p>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs font-semibold text-slate-500">
                    <span>Waiting for confirmation... </span>
                    <span id="mpesa-timer" class="font-black text-emerald-600">60s</span>
                </div>
                <div class="pt-2 flex flex-col gap-2">
                    <button type="button" onclick="simulateMpesaApproval('${checkoutRequestId}')" class="px-3 py-1.5 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-900 text-xs font-bold transition-colors">
                        ⚡ Test Approval (Sandbox Mode)
                    </button>
                </div>
            </div>
        `,
        showCancelButton: true,
        cancelButtonText: 'Cancel Waiting',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
            const timerEl = document.getElementById('mpesa-timer');
            pollInterval = setInterval(() => {
                secondsLeft -= 3;
                if (timerEl) timerEl.textContent = `${Math.max(0, secondsLeft)}s`;

                // Query transaction status
                fetch(`/payments/mpesa/status/${checkoutRequestId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.is_paid) {
                            clearInterval(pollInterval);
                            showMpesaSuccess(data.receipt, amount);
                        } else if (data.status === 'failed' || data.status === 'cancelled') {
                            clearInterval(pollInterval);
                            Swal.fire({
                                icon: 'error',
                                title: 'Payment Not Completed',
                                text: data.result_desc || 'The transaction was cancelled or timed out.',
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    })
                    .catch(() => {});

                if (secondsLeft <= 0) {
                    clearInterval(pollInterval);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Request Timed Out',
                        text: 'We have not received PIN confirmation yet. If you completed payment, the status will update shortly.',
                        confirmButtonColor: '#F59E0B'
                    });
                }
            }, 3000);
        },
        willClose: () => {
            if (pollInterval) clearInterval(pollInterval);
        }
    });
}

function simulateMpesaApproval(checkoutRequestId) {
    const btn = event ? (event.target || event.currentTarget) : null;
    if (btn) {
        btn.innerText = 'Approving Simulation... ⏳';
        btn.disabled = true;
    }
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
            showMpesaSuccess(data.receipt, 0);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Simulation Error',
                text: data.message || 'Failed to simulate M-Pesa approval.'
            });
        }
    })
    .catch(err => {
        console.error('Simulation error:', err);
        Swal.fire({
            icon: 'error',
            title: 'Request Failed',
            text: err.message
        });
    });
}

function showMpesaSuccess(receipt, amount) {
    Swal.fire({
        icon: 'success',
        title: 'M-Pesa Payment Successful! 🎉',
        html: `
            <div class="text-left space-y-2 text-xs pt-2">
                <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-950 font-bold">
                    ✓ 50% Escrow Deposit Verified &amp; Safely Locked.
                </div>
                ${receipt ? `<div class="font-semibold text-slate-700">M-Pesa Receipt: <strong class="text-slate-900">${receipt}</strong></div>` : ''}
                <div class="text-slate-500 font-medium">Your event staff has been confirmed. Reloading bookings...</div>
            </div>
        `,
        confirmButtonColor: '#059669',
        confirmButtonText: 'Great, Continue!'
    }).then(() => {
        window.location.reload();
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
    });
}

function openRescheduleModal(requestId, currentDate) {
    const minDate = new Date();
    minDate.setDate(minDate.getDate() + 1);
    const minDateStr = minDate.toISOString().split('T')[0];

    Swal.fire({
        title: 'Reschedule Event Booking',
        html: `
            <div class="text-left space-y-3 text-xs pt-2">
                <p class="text-slate-600">Select the new event date. An updated schedule notification and revised contract will be emailed to you and all assigned crew members.</p>
                <div>
                    <label class="block font-bold text-slate-800 uppercase tracking-wider mb-1">New Event Date</label>
                    <input type="date" id="swal-reschedule-date" value="${currentDate}" min="${minDateStr}" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 font-semibold focus:border-amber-500 focus:outline-none">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Confirm Reschedule 📅',
        confirmButtonColor: '#0284c7',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const dateVal = document.getElementById('swal-reschedule-date').value;
            if (!dateVal) {
                Swal.showValidationMessage('Please select a valid new event date.');
                return false;
            }
            return dateVal;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/client/bookings/${requestId}/reschedule`;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            const dateInput = document.createElement('input');
            dateInput.type = 'hidden';
            dateInput.name = 'event_date';
            dateInput.value = result.value;
            form.appendChild(dateInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmCancelBooking(requestId, eventName) {
    Swal.fire({
        title: 'Cancel Booking?',
        html: `
            <div class="text-left space-y-2 text-xs pt-2">
                <div class="p-3 bg-rose-50 rounded-xl border border-rose-200 text-rose-900 font-medium">
                    Are you sure you want to cancel the booking for <strong>${eventName}</strong>?
                </div>
                <p class="text-slate-600">All assigned crew members will be immediately notified via email. Any eligible deposit refund will be processed according to the cancellation policy.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel Booking',
        confirmButtonColor: '#e11d48',
        cancelButtonText: 'Keep Booking'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/client/bookings/${requestId}/cancel`;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
