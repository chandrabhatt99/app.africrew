@php
    $sidebarProfId = session('professional_id');
    $sidebarProf = $sidebarProfId ? \App\Models\Professional::find($sidebarProfId) : null;

    $sidebarIsOnboarded = true;
    $onboardProgress = 100;
    if ($sidebarProf) {
        $filledCount = 0;
        if (!empty($sidebarProf->full_name) && !empty($sidebarProf->phone) && !empty($sidebarProf->category) && !empty($sidebarProf->gender)) $filledCount++;
        if (!empty($sidebarProf->education) || !empty($sidebarProf->experience_records)) $filledCount++;
        if (!empty($sidebarProf->profile_photo) || !empty($sidebarProf->gallery_photos)) $filledCount++;
        if (!empty($sidebarProf->booking_policy) || !empty($sidebarProf->availability)) $filledCount++;
        if (!empty($sidebarProf->one_day_rate) || !empty($sidebarProf->hourly_rate)) $filledCount++;
        $sidebarIsOnboarded = ($filledCount >= 5) && (bool)$sidebarProf->is_onboarded;
        $onboardProgress = min(100, round(($filledCount / 5) * 100));
    }

    $isClientView = Auth::check() && (Auth::user()->role === 'client' || request()->routeIs('client.*'));
    $userName = $isClientView 
        ? (Auth::user()->name ?? 'Client User')
        : ($sidebarProf ? ($sidebarProf->full_name ?? $sidebarProf->username ?? 'Crew Member') : 'Crew Member');
    $userAvatar = $sidebarProf ? $sidebarProf->profile_photo_url : null;

    $sidebarUnreadCount = 0;
    if (Auth::check()) {
        $sidebarUnreadCount = \App\Models\Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
    } elseif ($sidebarProf) {
        $sidebarUser = \App\Models\User::where('email', $sidebarProf->email)->first();
        if ($sidebarUser) {
            $sidebarUnreadCount = \App\Models\Message::where('receiver_id', $sidebarUser->id)
                ->where('is_read', false)
                ->count();
        }
    }
@endphp

<!-- Desktop & Tablet SideNav (Pinned Full Height - Crisp Light Theme) -->
<aside id="main-sidebar" class="w-64 lg:w-60 shrink-0 bg-white text-slate-800 border-r border-slate-200/90 flex flex-col justify-between h-screen sticky top-0 z-30 transition-all duration-300 hidden md:flex shadow-xs select-none">
    
    <!-- Top Branding & Portal Badge -->
    <div class="p-3 border-b border-slate-200/80 space-y-4 shrink-0">
        <div class="flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center text-decoration-none group">
                <img src="{{ asset('images/africrew_logo.jpg') }}" alt="AfriCrew" class="h-9 sm:h-10 w-auto object-contain group-hover:scale-105 transition-transform">
            </a>

            <button onclick="toggleSidebar()" type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors focus:outline-none" title="Collapse Navigation">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
            </button>
        </div>


        <!-- Portal Type Indicator Badge -->
        <!-- <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-amber-50 border border-amber-200/80">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                 <span class="text-[10px] font-black uppercase tracking-wider text-amber-900">
                    {{ $isClientView ? '🏢 Client Portal' : '⚡ Crew Portal' }}
                </span> 
            </div>
            <span class="text-[9px] font-extrabold text-amber-800 bg-amber-200/60 px-2 py-0.5 rounded-md">Live</span>
        </div> -->
    </div>

    <!-- Scrollable SideNav Navigation Links -->
    <nav class="flex-1 overflow-y-auto custom-scrollbar px-3 py-4 space-y-6 text-xs font-bold">
        
        @if($isClientView)
            <!-- CLIENT PORTAL NAVIGATION -->
            <div>
                <span class="px-3 text-[10px] uppercase font-black tracking-widest text-slate-400 block mb-2">
                    Client Navigation
                </span>
                
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('client.dashboard') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-decoration-none transition-all {{ request()->routeIs('client.dashboard') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span>Client Dashboard</span>
                        </div>
                    </a>

                    <!-- Subtabs under Client Dashboard -->
                    <div class="ml-4 pl-3 border-l border-slate-200 space-y-1 my-1.5">
                        <a href="{{ route('client.dashboard') }}?tab=all-bookings" onclick="if(window.location.pathname.includes('/client/dashboard')){ event.preventDefault(); switchClientTab('all-bookings'); }" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-decoration-none text-[11px] font-semibold text-slate-600 hover:bg-amber-50 hover:text-amber-900 transition-all">
                            <span>📋 All Bookings</span>
                        </a>
                        <a href="{{ route('client.dashboard') }}?tab=new-bookings" onclick="if(window.location.pathname.includes('/client/dashboard')){ event.preventDefault(); switchClientTab('new-bookings'); }" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-decoration-none text-[11px] font-semibold text-slate-600 hover:bg-amber-50 hover:text-amber-900 transition-all">
                            <span>⚡ Active Shifts</span>
                        </a>
                        <a href="{{ route('client.dashboard') }}?tab=payments" onclick="if(window.location.pathname.includes('/client/dashboard')){ event.preventDefault(); switchClientTab('payments'); }" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-decoration-none text-[11px] font-semibold text-slate-600 hover:bg-amber-50 hover:text-amber-900 transition-all">
                            <span>💳 Invoices & Budget</span>
                        </a>
                        <a href="{{ route('client.dashboard') }}?tab=favorites" onclick="if(window.location.pathname.includes('/client/dashboard')){ event.preventDefault(); switchClientTab('favorites'); }" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-decoration-none text-[11px] font-semibold text-slate-600 hover:bg-amber-50 hover:text-amber-900 transition-all">
                            <span>❤️ Saved Crew</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- COMMUNICATION & HIRING -->
            <div>
                <span class="px-3 text-[10px] uppercase font-black tracking-widest text-slate-400 block mb-2">
                    Operations & Staffing
                </span>

                <div class="space-y-1">
                    <!-- Live Messages -->
                    <a href="{{ route('messages') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-decoration-none transition-all {{ request()->routeIs('messages') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>Messages & Negotiation</span>
                        </div>
                        @if($sidebarUnreadCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-[#22c55e] text-white font-black text-[10px] min-w-[20px] text-center shadow-2xs">
                                {{ $sidebarUnreadCount }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[9px]">Live</span>
                        @endif
                    </a>

                    <!-- Hire Crew -->
                    <a href="{{ route('crew.index') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-decoration-none transition-all {{ request()->routeIs('crew.*') ? 'bg-amber-100 text-amber-900 font-bold border border-amber-300' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span>+ Hire Event Crew</span>
                        </div>
                    </a>
                </div>
            </div>

        @else
            <!-- CREW PORTAL NAVIGATION -->

            @if(!$sidebarIsOnboarded)
                <div class="mx-1 p-3 bg-amber-50 border border-amber-200 rounded-2xl mb-3 space-y-1 text-center">
                    <span class="text-[10px] font-black uppercase text-amber-900 block">🔒 Profile Incomplete</span>
                    <p class="text-[10px] text-amber-800 font-medium leading-tight">Complete all 5 profile steps to unlock shift bookings.</p>
                </div>
            @endif

            <!-- Section 1: MAIN MENU -->
            <div>
                <span class="px-3 text-[10px] uppercase font-black tracking-widest text-slate-400 block mb-2">
                    Main Workspace
                </span>

                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ $sidebarIsOnboarded ? route('professional.dashboard') : route('professional.profile') }}"
                        @if(!$sidebarIsOnboarded) onclick="alertIncompleteProfile(event)" @endif
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-decoration-none transition-all {{ request()->routeIs('professional.dashboard') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : ($sidebarIsOnboarded ? 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' : 'text-slate-400 bg-slate-100/50 hover:bg-amber-50/60 cursor-not-allowed') }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span>Dashboard</span>
                        </div>
                        @if(!$sidebarIsOnboarded)<span class="text-[10px]">🔒</span>@endif
                    </a>

                    <!-- Profile & Resume -->
                    <a href="{{ route('professional.profile') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-decoration-none transition-all {{ request()->routeIs('professional.profile') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span>My Profile & Resume</span>
                        </div>
                        @if(!$sidebarIsOnboarded)
                            <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[9px] animate-pulse">Action</span>
                        @else
                            <span class="text-[10px] text-emerald-600 font-bold">✓</span>
                        @endif
                    </a>

                    <!-- Shifts & Schedule -->
                    <a href="{{ $sidebarIsOnboarded ? route('professional.shifts') : route('professional.profile') }}"
                        @if(!$sidebarIsOnboarded) onclick="alertIncompleteProfile(event)" @endif
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-decoration-none transition-all {{ request()->routeIs('professional.shifts') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : ($sidebarIsOnboarded ? 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' : 'text-slate-400 bg-slate-100/50 hover:bg-amber-50/60 cursor-not-allowed') }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Shifts & Schedule</span>
                        </div>
                        @if(!$sidebarIsOnboarded)<span class="text-[10px]">🔒</span>@endif
                    </a>

                    <!-- Wallet & Escrow -->
                    <a href="{{ $sidebarIsOnboarded ? route('professional.wallet') : route('professional.profile') }}"
                        @if(!$sidebarIsOnboarded) onclick="alertIncompleteProfile(event)" @endif
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-decoration-none transition-all {{ request()->routeIs('professional.wallet') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : ($sidebarIsOnboarded ? 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' : 'text-slate-400 bg-slate-100/50 hover:bg-amber-50/60 cursor-not-allowed') }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>Wallet & Withdrawals</span>
                        </div>
                        @if(!$sidebarIsOnboarded)<span class="text-[10px]">🔒</span>@endif
                    </a>
                </div>
            </div>

            <!-- Section 2: COMMUNICATION & HELP -->
            <div>
                <span class="px-3 text-[10px] uppercase font-black tracking-widest text-slate-400 block mb-2">
                    Communication & Help
                </span>

                <div class="space-y-1">
                    <!-- Messages -->
                    <a href="{{ $sidebarIsOnboarded ? route('professional.message') : route('professional.profile') }}"
                        @if(!$sidebarIsOnboarded) onclick="alertIncompleteProfile(event)" @endif
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-decoration-none transition-all {{ request()->routeIs('professional.messages') || request()->routeIs('professional.message') || request()->routeIs('messages') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : ($sidebarIsOnboarded ? 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' : 'text-slate-400 bg-slate-100/50 hover:bg-amber-50/60 cursor-not-allowed') }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>Direct Messages</span>
                        </div>
                        @if(!$sidebarIsOnboarded)
                            <span class="text-[10px]">🔒</span>
                        @elseif($sidebarUnreadCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-[#22c55e] text-white font-black text-[10px] min-w-[20px] text-center shadow-2xs">
                                {{ $sidebarUnreadCount }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[9px]">Live</span>
                        @endif
                    </a>

                    <!-- Notifications -->
                    <a href="{{ $sidebarIsOnboarded ? route('professional.notifications') : route('professional.profile') }}"
                        @if(!$sidebarIsOnboarded) onclick="alertIncompleteProfile(event)" @endif
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-decoration-none transition-all {{ request()->routeIs('professional.notifications') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : ($sidebarIsOnboarded ? 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' : 'text-slate-400 bg-slate-100/50 hover:bg-amber-50/60 cursor-not-allowed') }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span>Notifications</span>
                        </div>
                        @if(!$sidebarIsOnboarded)<span class="text-[10px]">🔒</span>@endif
                    </a>

                    <!-- Support Desk -->
                    <a href="{{ $sidebarIsOnboarded ? route('professional.support') : route('professional.profile') }}"
                        @if(!$sidebarIsOnboarded) onclick="alertIncompleteProfile(event)" @endif
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-decoration-none transition-all {{ request()->routeIs('professional.support') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : ($sidebarIsOnboarded ? 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' : 'text-slate-400 bg-slate-100/50 hover:bg-amber-50/60 cursor-not-allowed') }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span>Support Desk</span>
                        </div>
                        @if(!$sidebarIsOnboarded)<span class="text-[10px]">🔒</span>@endif
                    </a>
                </div>
            </div>
        @endif

    </nav>

    <!-- Bottom User Profile Footer Card -->
    <div class="p-4 border-t border-slate-200/80 bg-slate-50/90 shrink-0 space-y-3">
        
        @if(!$isClientView && $sidebarProf && !$sidebarIsOnboarded)
            <!-- Profile Onboarding Progress Bar -->
            <div class="space-y-1">
                <div class="flex items-center justify-between text-[10px] font-extrabold text-amber-700">
                    <span>Profile Setup</span>
                    <span>{{ $onboardProgress }}%</span>
                </div>
                <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-full rounded-full transition-all duration-500" style="width: {{ $onboardProgress }}%"></div>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="relative shrink-0">
                    @if($userAvatar)
                        <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="w-9 h-9 rounded-xl object-cover border border-amber-400 shadow-xs">
                    @else
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black flex items-center justify-center text-xs shadow-xs">
                            {{ strtoupper(substr($userName, 0, 1)) }}
                        </div>
                    @endif
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white absolute -bottom-0.5 -right-0.5" title="Online"></span>
                </div>

                <div class="flex flex-col min-w-0">
                    <span class="text-xs font-black text-slate-950 truncate leading-tight">{{ $userName }}</span>
                    <span class="text-[10px] font-bold text-slate-500 truncate">
                        {{ $isClientView ? 'Client Account' : ($sidebarProf->category ?? 'Event Crew') }}
                    </span>
                </div>
            </div>

            <!-- Sign Out Form -->
            <form method="POST" action="{{ $isClientView ? route('client.logout') : route('staff.logout') }}" class="m-0">
                @csrf
                <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors border-0 bg-transparent" title="Sign Out">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>

    </div>
</aside>

<!-- Mobile Offcanvas Drawer (< md screens) -->
<div id="mobile-sidebar-drawer" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden transition-all duration-300 flex" onclick="closeSidebarDrawer()">
    <div class="w-72 bg-white text-slate-800 h-full shadow-2xl p-5 flex flex-col justify-between border-r border-slate-200" onclick="event.stopPropagation()">
        
        <div class="space-y-5">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <a href="{{ route('home') }}" class="flex items-center text-decoration-none">
                    <img src="{{ asset('images/africrew_logo.jpg') }}" alt="AfriCrew" class="h-8 w-auto object-contain">
                </a>
                <button onclick="closeSidebarDrawer()" class="text-slate-400 hover:text-slate-900 p-1 focus:outline-none" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Links -->
            <nav class="space-y-2 text-xs font-bold overflow-y-auto max-h-[calc(100vh-160px)] custom-scrollbar pr-1">
                @if($isClientView)
                    <div class="px-2 py-1 text-[10px] font-black uppercase tracking-wider text-amber-700">🏢 Client Portal</div>
                    <a href="{{ route('client.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-decoration-none text-slate-800 hover:bg-amber-50">
                        <span>📊 Client Dashboard</span>
                    </a>
                    <a href="{{ route('messages') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-decoration-none text-slate-800 hover:bg-amber-50">
                        <span>💬 Direct Messages</span>
                    </a>
                    <a href="{{ route('crew.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-decoration-none text-slate-800 hover:bg-amber-50">
                        <span>👥 Hire Event Crew</span>
                    </a>
                @else
                    <div class="px-2 py-1 text-[10px] font-black uppercase tracking-wider text-amber-700">⚡ Crew Portal</div>
                    <a href="{{ $sidebarIsOnboarded ? route('professional.dashboard') : route('professional.profile') }}" @if(!$sidebarIsOnboarded) onclick="alertIncompleteProfile(event)" @endif class="flex items-center justify-between px-3 py-2.5 rounded-xl text-decoration-none text-slate-800 hover:bg-slate-100">
                        <span>📊 Dashboard</span>
                        @if(!$sidebarIsOnboarded)<span>🔒</span>@endif
                    </a>
                    <a href="{{ route('professional.profile') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-decoration-none text-slate-800 hover:bg-slate-100">
                        <span>👤 My Profile & Resume</span>
                        @if(!$sidebarIsOnboarded)<span class="text-[9px] bg-amber-500 text-slate-950 font-black px-2 py-0.5 rounded-full">Action Needed</span>@endif
                    </a>
                    <a href="{{ $sidebarIsOnboarded ? route('professional.shifts') : route('professional.profile') }}" @if(!$sidebarIsOnboarded) onclick="alertIncompleteProfile(event)" @endif class="flex items-center justify-between px-3 py-2.5 rounded-xl text-decoration-none text-slate-800 hover:bg-slate-100">
                        <span>📅 Shifts & Schedule</span>
                        @if(!$sidebarIsOnboarded)<span>🔒</span>@endif
                    </a>
                    <a href="{{ $sidebarIsOnboarded ? route('professional.wallet') : route('professional.profile') }}" @if(!$sidebarIsOnboarded) onclick="alertIncompleteProfile(event)" @endif class="flex items-center justify-between px-3 py-2.5 rounded-xl text-decoration-none text-slate-800 hover:bg-slate-100">
                        <span>💳 Wallet & Escrow</span>
                        @if(!$sidebarIsOnboarded)<span>🔒</span>@endif
                    </a>
                    <a href="{{ $sidebarIsOnboarded ? route('messages') : route('professional.profile') }}" @if(!$sidebarIsOnboarded) onclick="alertIncompleteProfile(event)" @endif class="flex items-center justify-between px-3 py-2.5 rounded-xl text-decoration-none text-slate-800 hover:bg-slate-100">
                        <span>💬 Direct Messages</span>
                        @if(!$sidebarIsOnboarded)<span>🔒</span>@endif
                    </a>
                    <a href="{{ $sidebarIsOnboarded ? route('professional.notifications') : route('professional.profile') }}" @if(!$sidebarIsOnboarded) onclick="alertIncompleteProfile(event)" @endif class="flex items-center justify-between px-3 py-2.5 rounded-xl text-decoration-none text-slate-800 hover:bg-slate-100">
                        <span>🔔 Notifications</span>
                        @if(!$sidebarIsOnboarded)<span>🔒</span>@endif
                    </a>
                @endif
            </nav>
        </div>

        <div class="border-t border-slate-200 pt-4">
            <form method="POST" action="{{ $isClientView ? route('client.logout') : route('staff.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-rose-50 text-rose-600 font-black text-xs hover:bg-rose-100 transition-all border border-rose-200">
                    <span>Sign Out</span>
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('main-sidebar');
        const drawer = document.getElementById('mobile-sidebar-drawer');
        if (window.innerWidth >= 768) {
            if (sidebar) {
                const isHidden = sidebar.classList.contains('hidden') || sidebar.style.display === 'none';
                if (isHidden) {
                    sidebar.classList.remove('hidden');
                    sidebar.style.display = 'flex';
                    localStorage.setItem('sidebar_collapsed', 'false');
                } else {
                    sidebar.classList.add('hidden');
                    sidebar.style.display = 'none';
                    localStorage.setItem('sidebar_collapsed', 'true');
                }
            }
        } else {
            if (drawer) {
                drawer.classList.toggle('hidden');
            }
        }
    }

    function closeSidebarDrawer() {
        const drawer = document.getElementById('mobile-sidebar-drawer');
        if (drawer) {
            drawer.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.innerWidth >= 768) {
            const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            const sidebar = document.getElementById('main-sidebar');
            if (sidebar && isCollapsed) {
                sidebar.classList.add('hidden');
                sidebar.style.display = 'none';
            }
        }
    });

    function alertIncompleteProfile(e) {
        e.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Profile Setup Required 🔒',
                text: 'You must complete all 5 profile onboarding sections (Personal, Experience, Photos, Availability, Labour Charges) to 100% before unlocking other portal tabs.',
                confirmButtonColor: '#F59E0B',
                confirmButtonText: 'Complete Profile Now 👤'
            }).then(() => {
                window.location.href = "{{ route('professional.profile') }}";
            });
        } else {
            alert('Please complete all 5 profile onboarding sections before accessing other portal tabs.');
            window.location.href = "{{ route('professional.profile') }}";
        }
    }
</script>


