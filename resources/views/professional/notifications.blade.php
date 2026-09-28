@extends('layouts.staff')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-16">

    <!-- Header & Action Controls -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Notification Center</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Stay updated on shift booking requests, payout updates, and system alerts.</p>
        </div>

        <button onclick="markAllRead()" class="px-4 py-2.5 rounded-2xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-extrabold text-xs transition-all flex items-center gap-1.5 shrink-0">
            <span>✓ Mark all as read</span>
        </button>
    </div>

    <!-- Notifications List Container -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
        
        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 border-b border-slate-100 pb-4 text-xs font-extrabold overflow-x-auto">
            <button onclick="filterNotifications('all')" id="notif-tab-all" class="px-4 py-2 rounded-xl transition-all bg-amber-500 text-slate-950 font-black shadow-sm">
                All (3)
            </button>
            <button onclick="filterNotifications('unread')" id="notif-tab-unread" class="px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-100">
                Unread (2)
            </button>
            <button onclick="filterNotifications('shifts')" id="notif-tab-shifts" class="px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-100">
                Shift Requests
            </button>
        </div>

        <!-- Notification Items List -->
        <div class="space-y-3 pt-2">
            
            <!-- Item 1: Unread Shift Request -->
            <div class="p-4 sm:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-start gap-4 transition-all hover:bg-amber-500/20">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-lg shrink-0 shadow-sm">
                    📅
                </div>
                <div class="flex-grow min-w-0 space-y-1">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-xs font-black text-slate-900 truncate">New Shift Booking Request: Reggae Cultural Festival</h3>
                        <span class="text-[10px] text-slate-500 font-bold shrink-0">10 mins ago</span>
                    </div>
                    <p class="text-xs text-slate-700 leading-relaxed font-medium">
                        Marcus Vance requested to book you for 1 Day shift at Uhuru Gardens on <strong>KES 5,000.00</strong> rate schedule.
                    </p>
                    <div class="pt-2 flex items-center gap-3">
                        <a href="{{ route('professional.dashboard') }}" class="px-4 py-1.5 rounded-xl bg-amber-500 text-slate-950 font-black text-[11px] hover:bg-amber-400 transition-all shadow-sm">
                            View Shift Details →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Item 2: Unread System Verification -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-4 transition-all hover:bg-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white font-black flex items-center justify-center text-lg shrink-0 shadow-sm">
                    🛡️
                </div>
                <div class="flex-grow min-w-0 space-y-1">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-xs font-black text-slate-900 truncate">Crew Resume Verified</h3>
                        <span class="text-[10px] text-slate-500 font-bold shrink-0">2 hours ago</span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed font-medium">
                        Your government ID and ushering skills profile have been verified by AfriCrew Admin. Your public profile is live!
                    </p>
                </div>
            </div>

            <!-- Item 3: Read System Alert -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 opacity-80 flex items-start gap-4 transition-all hover:opacity-100">
                <div class="w-10 h-10 rounded-2xl bg-blue-500 text-white font-black flex items-center justify-center text-lg shrink-0 shadow-sm">
                    💡
                </div>
                <div class="flex-grow min-w-0 space-y-1">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-xs font-bold text-slate-800 truncate">Welcome to AfriCrew Crew Portal</h3>
                        <span class="text-[10px] text-slate-400 font-medium shrink-0">Yesterday</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Complete your 5-step onboarding wizard to set your calendar availability and daily KES labour charges.
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    function filterNotifications(filter) {
        ['all', 'unread', 'shifts'].forEach(f => {
            const btn = document.getElementById(`notif-tab-${f}`);
            if (btn) btn.className = 'px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-100';
        });
        const activeBtn = document.getElementById(`notif-tab-${filter}`);
        if (activeBtn) activeBtn.className = 'px-4 py-2 rounded-xl transition-all bg-amber-500 text-slate-950 font-black shadow-sm';
    }

    function markAllRead() {
        alert('All notifications marked as read.');
    }
</script>
@endsection
