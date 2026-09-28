@extends('layouts.staff')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-16">

    <!-- Header & Action Controls -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Notification Center</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Stay updated on shift booking requests, payout updates, and system alerts.</p>
        </div>

        <button onclick="markAllRead()" class="px-4 py-2.5 rounded-2xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-extrabold text-xs transition-all flex items-center gap-1.5 shrink-0 cursor-pointer">
            <span>✓ Mark all as read</span>
        </button>
    </div>

    @php
        $allCount = count($notifications);
        $unreadCount = $notifications->where('is_unread', true)->count();
        $shiftsCount = $notifications->where('type', 'shifts')->count();
    @endphp

    <!-- Notifications List Container -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
        
        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 border-b border-slate-100 pb-4 text-xs font-extrabold overflow-x-auto">
            <button onclick="filterNotifications('all')" id="notif-tab-all" class="px-4 py-2 rounded-xl transition-all bg-amber-500 text-slate-950 font-black shadow-sm cursor-pointer">
                All (<span id="count-all">{{ $allCount }}</span>)
            </button>
            <button onclick="filterNotifications('unread')" id="notif-tab-unread" class="px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-100 cursor-pointer">
                Unread (<span id="count-unread">{{ $unreadCount }}</span>)
            </button>
            <button onclick="filterNotifications('shifts')" id="notif-tab-shifts" class="px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-100 cursor-pointer">
                Shift Requests (<span id="count-shifts">{{ $shiftsCount }}</span>)
            </button>
        </div>

        <!-- Notification Items List -->
        <div class="space-y-3 pt-2" id="notif-items-container">
            @forelse($notifications as $notif)
                @php
                    $bgColor = $notif['is_unread'] ? 'bg-amber-500/10 border-amber-500/30' : 'bg-slate-50 border-slate-200';
                    $iconBg = $notif['color'] === 'amber' ? 'bg-amber-500 text-slate-950' : ($notif['color'] === 'emerald' ? 'bg-emerald-500 text-white' : ($notif['color'] === 'rose' ? 'bg-rose-500 text-white' : 'bg-blue-500 text-white'));
                @endphp
                <div class="notif-item p-4 sm:p-5 rounded-2xl border flex items-start gap-4 transition-all {{ $bgColor }}"
                     data-id="{{ $notif['id'] }}"
                     data-type="{{ $notif['type'] }}"
                     data-unread="{{ $notif['is_unread'] ? 'true' : 'false' }}">
                    <div class="w-10 h-10 rounded-2xl {{ $iconBg }} font-black flex items-center justify-center text-lg shrink-0 shadow-sm">
                        {{ $notif['icon'] }}
                    </div>
                    <div class="flex-grow min-w-0 space-y-1">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-xs font-black text-slate-900 truncate">{{ $notif['title'] }}</h3>
                            <span class="text-[10px] text-slate-500 font-bold shrink-0">{{ $notif['time'] }}</span>
                        </div>
                        <p class="text-xs text-slate-700 leading-relaxed font-medium">
                            {{ $notif['description'] }}
                        </p>
                        @if(!empty($notif['action_url']))
                            <div class="pt-2 flex items-center gap-3">
                                <a href="{{ $notif['action_url'] }}" class="px-4 py-1.5 rounded-xl bg-slate-900 text-amber-400 font-black text-[11px] hover:bg-slate-800 transition-all shadow-xs text-decoration-none">
                                    {{ $notif['action_text'] }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                    <span class="text-3xl block">🔔</span>
                    <h3 class="text-sm font-black text-slate-900">No Notifications</h3>
                    <p class="text-xs text-slate-500">You're all caught up! Shift requests and account alerts will appear here.</p>
                </div>
            @endforelse
        </div>

    </div>

</div>

<script>
    function filterNotifications(filter) {
        ['all', 'unread', 'shifts'].forEach(f => {
            const btn = document.getElementById(`notif-tab-${f}`);
            if (btn) btn.className = 'px-4 py-2 rounded-xl transition-all text-slate-600 hover:bg-slate-100 cursor-pointer';
        });

        const activeBtn = document.getElementById(`notif-tab-${filter}`);
        if (activeBtn) activeBtn.className = 'px-4 py-2 rounded-xl transition-all bg-amber-500 text-slate-950 font-black shadow-sm cursor-pointer';

        const items = document.querySelectorAll('.notif-item');
        items.forEach(item => {
            const type = item.getAttribute('data-type');
            const unread = item.getAttribute('data-unread') === 'true';

            if (filter === 'all') {
                item.style.display = 'flex';
            } else if (filter === 'unread') {
                item.style.display = unread ? 'flex' : 'none';
            } else if (filter === 'shifts') {
                item.style.display = (type === 'shifts') ? 'flex' : 'none';
            }
        });
    }

    function markAllRead() {
        const items = document.querySelectorAll('.notif-item');
        items.forEach(item => {
            item.setAttribute('data-unread', 'false');
            item.classList.remove('bg-amber-500/10', 'border-amber-500/30');
            item.classList.add('bg-slate-50', 'border-slate-200');
        });

        const unreadBadge = document.getElementById('count-unread');
        if (unreadBadge) unreadBadge.textContent = '0';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'All notifications marked as read',
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            alert('All notifications marked as read.');
        }
    }
</script>
@endsection
