@php
    $profId = session('professional_id');
    $isLoggedIn = !empty($profId);
    $currentProf = $profId ? \App\Models\Professional::find($profId) : null;
    $username = $currentProf ? ($currentProf->username ?? Str::slug(strtok($currentProf->full_name ?? 'User', ' '))) : null;
    $avatarUrl = $currentProf ? $currentProf->profile_photo_url : null;

    $isAuthRoute = request()->routeIs('client.login') || request()->routeIs('client.register') || request()->routeIs('staff.login') || request()->routeIs('admin.login');
    $isCrmPanel = !$isAuthRoute && (request()->routeIs('client.*') || request()->routeIs('professional.*') || request()->routeIs('messages') || request()->routeIs('admin.*'));

    $pageTitle = 'Dashboard';
    if (request()->routeIs('client.dashboard')) {
        $pageTitle = 'Client Portal Dashboard';
    } elseif (request()->routeIs('professional.dashboard')) {
        $pageTitle = 'Crew Operations Dashboard';
    } elseif (request()->routeIs('messages') || request()->routeIs('professional.messages') || request()->routeIs('professional.message') || request()->routeIs('client.messages')) {
        $pageTitle = 'Messages & Price Negotiation';
    } elseif (request()->routeIs('professional.shifts')) {
        $pageTitle = 'Shifts & Schedule Calendar';
    } elseif (request()->routeIs('professional.wallet')) {
        $pageTitle = 'Wallet & Withdrawal Escrow';
    } elseif (request()->routeIs('professional.profile')) {
        $pageTitle = 'Profile & Resume Builder';
    } elseif (request()->routeIs('professional.notifications')) {
        $pageTitle = 'System Notifications';
    } elseif (request()->routeIs('professional.support')) {
        $pageTitle = 'Support Desk & Help Tickets';
    } elseif (request()->routeIs('crew.index')) {
        $pageTitle = 'Vetted Crew Catalog';
    }
@endphp

@if($isCrmPanel)
    <!-- ========================================== -->
    <!-- 1. CRM WORKSPACE PANEL HEADER (IN-APP PORTAL) -->
    <!-- ========================================== -->
    <header id="crm-header" class="bg-white/95 backdrop-blur-md border-b border-slate-200/90 sticky top-0 z-20 shadow-2xs transition-colors duration-200">
        <div class="w-full px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            
            <!-- Left: SideNav Toggle & Dynamic Page Title Breadcrumb -->
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" type="button" class="p-2 rounded-xl text-slate-700 hover:text-slate-950 hover:bg-slate-100 focus:outline-none transition-colors border border-slate-200/80" title="Toggle Side Navigation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <!-- Dynamic CRM Breadcrumb & Title -->
                <div class="flex items-center gap-2 min-w-0">
                    <span class="hidden sm:inline-block text-xs font-bold text-slate-400 whitespace-nowrap">AfriCrew Workspace</span>
                    <span class="hidden sm:inline-block text-xs text-slate-300">/</span>
                    <h1 class="text-xs sm:text-sm md:text-base font-black text-slate-950 tracking-tight m-0 leading-none whitespace-nowrap truncate">
                        {{ $pageTitle }}
                    </h1>
                </div>
            </div>

            <!-- Right: Notifications & User Profile -->
            <div class="flex items-center gap-3 shrink-0">
                @if(Auth::check() && (!Auth::user()->is_admin || Auth::user()->role === 'client' || request()->routeIs('client.*')))
                    <!-- Client Portal User Badge -->
                    <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2 text-decoration-none group">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-xl object-cover border border-amber-400 shadow-xs group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-8 h-8 rounded-xl bg-slate-900 text-amber-400 font-black flex items-center justify-center text-xs shadow-xs group-hover:scale-105 transition-transform border border-amber-400/40">
                                {{ strtoupper(substr(Auth::user()->name ?? 'C', 0, 1)) }}
                            </div>
                        @endif
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="text-xs font-black text-slate-900 group-hover:text-amber-600 transition-colors">{{ Auth::user()->name }}</span>
                            <span class="text-[9px] font-black uppercase tracking-wider text-amber-700">CLIENT</span>
                        </div>
                    </a>

                    <!-- Client Sign Out Button -->
                    <form method="POST" action="{{ route('client.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-all flex items-center gap-1.5 border border-rose-200/80" title="Sign Out">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span class="hidden sm:inline">Sign Out</span>
                        </button>
                    </form>
                @elseif(Auth::check() && Auth::user()->is_admin)
                    <!-- Admin User Badge -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-decoration-none group">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-amber-400 font-black flex items-center justify-center text-xs shadow-xs group-hover:scale-105 transition-transform border border-amber-400/40">
                            A
                        </div>
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="text-xs font-black text-slate-900 group-hover:text-amber-600 transition-colors">{{ Auth::user()->name }}</span>
                            <span class="text-[9px] font-black uppercase tracking-wider text-rose-600">ADMIN</span>
                        </div>
                    </a>

                    <!-- Admin Sign Out Button -->
                    <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-all flex items-center gap-1.5 border border-rose-200/80" title="Sign Out">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span class="hidden sm:inline">Sign Out</span>
                        </button>
                    </form>
                @elseif($isLoggedIn && $currentProf)
                    <!-- Notifications Bell Icon -->
                    <a href="{{ route('professional.notifications') }}" class="relative p-2 rounded-xl text-slate-700 hover:bg-slate-100 focus:outline-none transition-all border border-slate-200/60" title="Notifications">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="w-4 h-4 rounded-full bg-amber-500 text-slate-950 font-black text-[9px] flex items-center justify-center absolute -top-1 -right-1 border-2 border-white shadow-xs">
                            3
                        </span>
                    </a>

                    <!-- Crew User Profile Badge -->
                    <a href="{{ route('professional.profile') }}" class="flex items-center gap-2 text-decoration-none group">
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $username }}" class="w-8 h-8 rounded-xl object-cover border border-amber-400 shadow-xs group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black flex items-center justify-center text-xs shadow-xs group-hover:scale-105 transition-transform">
                                {{ strtoupper(substr($username ?? 'C', 0, 1)) }}
                            </div>
                        @endif
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="text-xs font-black text-slate-900 group-hover:text-amber-600 transition-colors" id="user-name-text">{{ $username }}</span>
                            <span class="text-[9px] font-black uppercase tracking-wider text-amber-700">CREW</span>
                        </div>
                    </a>

                    <!-- Crew Sign Out Button -->
                    <form method="POST" action="{{ route('staff.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-all flex items-center gap-1.5 border border-rose-200/80" title="Sign Out">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span class="hidden sm:inline">Sign Out</span>
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </header>

@else

    <!-- ========================================== -->
    <!-- 2. PUBLIC WEBSITE MARKETING HEADER (HOMEPAGE/PUBLIC) -->
    <!-- ========================================== -->
    <!-- ========================================== -->
    <!-- 2. PUBLIC WEBSITE MARKETING HEADER (HOMEPAGE/PUBLIC) -->
    <!-- ========================================== -->
    <header id="public-header" class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-2xs transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
            
            <!-- Left: Public Logo Branding -->
            <a href="{{ route('home') }}" class="flex items-center text-decoration-none group shrink-0">
                <img src="{{ asset('images/africrew_logo.jpg') }}" alt="AfriCrew - CREW CONNECT HUB" class="h-10 sm:h-11 w-auto object-contain group-hover:scale-105 transition-transform duration-200">
            </a>

            <!-- Center: Desktop Public Navigation Pill Capsule (Matching africrew.com) -->
            <nav class="hidden md:flex items-center bg-[#f0f3f8] rounded-full p-1.5 border border-slate-200/50 shadow-2xs gap-1">
                
                <!-- Home -->
                @if(request()->routeIs('home'))
                    <a href="{{ route('home') }}" class="bg-white text-slate-950 font-extrabold text-sm sm:text-base px-6 py-2 rounded-full shadow-xs border border-slate-200/40 text-decoration-none flex items-center justify-center transition-all">
                        Home
                    </a>
                @else
                    <a href="{{ route('home') }}" class="text-slate-700 hover:text-slate-950 font-bold text-sm sm:text-base px-4 sm:px-5 py-2 rounded-full hover:bg-white/50 transition-colors text-decoration-none">
                        Home
                    </a>
                @endif

                <!-- Hire Crew (In place of About Us) -->
                @if(request()->routeIs('crew.*'))
                    <a href="{{ route('crew.index') }}" class="bg-white text-slate-950 font-extrabold text-sm sm:text-base px-6 py-2 rounded-full shadow-xs border border-slate-200/40 text-decoration-none flex items-center justify-center transition-all">
                        Hire Crew
                    </a>
                @else
                    <a href="{{ route('crew.index') }}" class="text-slate-700 hover:text-slate-950 font-bold text-sm sm:text-base px-4 sm:px-5 py-2 rounded-full hover:bg-white/50 transition-colors text-decoration-none">
                        Hire Crew
                    </a>
                @endif

                <!-- Visa Hub / Hire Hub -->
                <a href="https://africrew.com/hire" class="text-slate-700 hover:text-slate-950 font-bold text-sm sm:text-base px-4 sm:px-5 py-2 rounded-full hover:bg-white/50 transition-colors text-decoration-none">
                    Hire Hub
                </a>

                <!-- Blog -->
                <a href="https://africrew.com/blog" target="_blank" rel="noopener noreferrer" class="text-slate-700 hover:text-slate-950 font-bold text-sm sm:text-base px-4 sm:px-5 py-2 rounded-full hover:bg-white/50 transition-colors text-decoration-none">
                    Blog
                </a>

                <!-- Contact Us -->
                <a href="#contact" class="text-slate-700 hover:text-slate-950 font-bold text-sm sm:text-base px-4 sm:px-5 py-2 rounded-full hover:bg-white/50 transition-colors text-decoration-none">
                    Contact Us
                </a>
            </nav>

            <!-- Right: Action CTA (Matching africrew.com) -->
            <div class="hidden md:flex items-center gap-3 lg:gap-3.5 shrink-0">
                @if(Auth::check() && (!Auth::user()->is_admin || Auth::user()->role === 'client'))
                    <a href="{{ route('client.dashboard') }}" class="px-5 py-2.5 rounded-full bg-amber-50 border border-amber-200 text-amber-900 font-extrabold text-sm text-decoration-none flex items-center gap-2 hover:bg-amber-100 transition-all shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-6 h-6 rounded-full object-cover border border-amber-400">
                        @else
                            <div class="w-6 h-6 rounded-full bg-slate-900 text-amber-400 font-black flex items-center justify-center text-[11px]">
                                {{ strtoupper(substr(Auth::user()->name ?? 'C', 0, 1)) }}
                            </div>
                        @endif
                        <span>Client Portal</span>
                    </a>
                    <form method="POST" action="{{ route('client.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-full bg-rose-50 text-rose-600 font-bold text-xs hover:bg-rose-100 transition-all border border-rose-200">Sign Out</button>
                    </form>
                @elseif(Auth::check() && Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-full bg-slate-900 text-amber-400 font-extrabold text-sm text-decoration-none flex items-center gap-2 hover:bg-slate-800 transition-all shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Admin Portal</span>
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-full bg-rose-50 text-rose-600 font-bold text-xs hover:bg-rose-100 transition-all border border-rose-200">Sign Out</button>
                    </form>
                @elseif($isLoggedIn && $currentProf)
                    <a href="{{ route('professional.dashboard') }}" class="px-5 py-2.5 rounded-full bg-amber-50 border border-amber-200 text-amber-900 font-extrabold text-sm text-decoration-none flex items-center gap-2 hover:bg-amber-100 transition-all shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $username }}" class="w-6 h-6 rounded-full object-cover border border-amber-400">
                        @else
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black flex items-center justify-center text-[11px]">
                                {{ strtoupper(substr($username ?? 'C', 0, 1)) }}
                            </div>
                        @endif
                        <span>Crew Portal ({{ $username }})</span>
                    </a>
                    <form method="POST" action="{{ route('staff.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-full bg-rose-50 text-rose-600 font-bold text-xs hover:bg-rose-100 transition-all border border-rose-200">Sign Out</button>
                    </form>
                @else
                    <a href="{{ route('client.login') }}" class="px-4 lg:px-5 py-2.5 rounded-full border border-slate-200/90 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-800 hover:text-slate-950 font-bold text-xs lg:text-sm text-decoration-none flex items-center gap-1.5 lg:gap-2 shadow-2xs hover:shadow-xs transition-all duration-200 whitespace-nowrap">
                        <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>Client Login</span>
                    </a>
                    <a href="{{ route('staff.login') }}" class="rounded-full px-5 lg:px-6 py-2.5 text-white font-extrabold text-xs lg:text-sm bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 hover:opacity-95 shadow-md shadow-rose-500/25 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 text-decoration-none whitespace-nowrap flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span>Crew Login</span>
                    </a>
                @endif
            </div>

            <!-- Mobile Right: Client / Crew Portal or Login Buttons + Navigation Toggle -->
            <div class="flex md:hidden items-center gap-1.5 sm:gap-2">
                @if(Auth::check() && (!Auth::user()->is_admin || Auth::user()->role === 'client'))
                    <a href="{{ route('client.dashboard') }}" class="px-3 py-1.5 rounded-full bg-amber-100 border border-amber-300 text-amber-950 font-black text-xs flex items-center gap-1.5 text-decoration-none shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Client Portal</span>
                    </a>
                @elseif(Auth::check() && Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-full bg-slate-900 text-amber-400 font-black text-xs flex items-center gap-1.5 text-decoration-none shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Admin</span>
                    </a>
                @elseif($isLoggedIn && $currentProf)
                    <a href="{{ route('professional.dashboard') }}" class="px-3 py-1.5 rounded-full bg-amber-100 border border-amber-300 text-amber-950 font-black text-xs flex items-center gap-1.5 text-decoration-none shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Crew Portal</span>
                    </a>
                @else
                    <a href="{{ route('client.login') }}" class="px-2.5 sm:px-3 py-1.5 rounded-full border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-800 font-bold text-[11px] sm:text-xs flex items-center gap-1 text-decoration-none shadow-2xs">
                        <span>Client Login</span>
                    </a>
                    <a href="{{ route('staff.login') }}" class="px-2.5 sm:px-3 py-1.5 rounded-full bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 hover:opacity-95 text-white font-black text-[11px] sm:text-xs flex items-center gap-1 text-decoration-none shadow-xs">
                        <span>Crew Login</span>
                    </a>
                @endif
                <button onclick="togglePublicMobileNav()" type="button" class="p-2 rounded-xl text-slate-700 hover:bg-slate-100 border border-slate-200 focus:outline-none" aria-label="Open Navigation">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>

        </div>

        <!-- Mobile Public Navigation Dropdown Drawer -->
        <div id="public-mobile-nav" class="hidden md:hidden border-t border-slate-200 bg-white px-5 pt-3 pb-6 space-y-3 shadow-xl">
            <nav class="flex flex-col space-y-1.5 text-sm font-bold text-slate-800">
                <a href="{{ route('home') }}" class="px-4 py-2.5 rounded-xl flex items-center justify-between {{ request()->routeIs('home') ? 'bg-slate-100 text-slate-950 font-black' : 'hover:bg-slate-50' }} text-decoration-none">
                    <span>Home</span>
                </a>
                <a href="{{ route('crew.index') }}" class="px-4 py-2.5 rounded-xl flex items-center justify-between {{ request()->routeIs('crew.*') ? 'bg-slate-100 text-slate-950 font-black' : 'hover:bg-slate-50' }} text-decoration-none">
                    <span>Hire Crew</span>
                </a>
                <a href="https://africrew.com/hire" class="px-4 py-2.5 rounded-xl flex items-center justify-between hover:bg-slate-50 text-decoration-none">
                    <span>Hire Hub</span>
                </a>
                <a href="https://africrew.com/blog" target="_blank" rel="noopener noreferrer" class="px-4 py-2.5 rounded-xl hover:bg-slate-50 text-decoration-none">
                    <span>Blog</span>
                </a>
                <a href="#contact" onclick="togglePublicMobileNav()" class="px-4 py-2.5 rounded-xl hover:bg-slate-50 text-decoration-none">
                    <span>Contact Us</span>
                </a>
            </nav>
            
            <div class="pt-3 border-t border-slate-100 flex flex-col gap-2.5">
                @if(Auth::check() && (!Auth::user()->is_admin || Auth::user()->role === 'client'))
                    <a href="{{ route('client.dashboard') }}" class="w-full text-center py-2.5 rounded-xl bg-amber-500 text-slate-950 font-black text-xs text-decoration-none">Go to Client Portal</a>
                    <form method="POST" action="{{ route('client.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="w-full text-center py-2 rounded-xl bg-rose-50 text-rose-600 font-bold text-xs border border-rose-200">Sign Out</button>
                    </form>
                @elseif(Auth::check() && Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="w-full text-center py-2.5 rounded-xl bg-slate-900 text-amber-400 font-black text-xs text-decoration-none">Go to Admin Portal</a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="w-full text-center py-2 rounded-xl bg-rose-50 text-rose-600 font-bold text-xs border border-rose-200">Sign Out</button>
                    </form>
                @elseif($isLoggedIn && $currentProf)
                    <a href="{{ route('professional.dashboard') }}" class="w-full text-center py-2.5 rounded-xl bg-amber-500 text-slate-950 font-black text-xs text-decoration-none">Go to Crew Portal</a>
                    <form method="POST" action="{{ route('staff.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="w-full text-center py-2 rounded-xl bg-rose-50 text-rose-600 font-bold text-xs border border-rose-200">Sign Out</button>
                    </form>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('client.login') }}" class="text-center py-2.5 rounded-xl bg-slate-100 text-slate-900 font-bold text-xs text-decoration-none border border-slate-200">Client Login</a>
                        <a href="{{ route('staff.login') }}" class="text-center py-2.5 rounded-xl bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 hover:opacity-95 text-white font-bold text-xs text-decoration-none shadow-xs">Crew Login</a>
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Top Spacer to compensate for fixed header -->
    <div class="h-[76px] sm:h-[84px] shrink-0 pointer-events-none"></div>

    <script>
        function togglePublicMobileNav() {
            const nav = document.getElementById('public-mobile-nav');
            if (nav) nav.classList.toggle('hidden');
        }
    </script>
@endif
