<!doctype html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'AfriCrew Operations Admin Portal' }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/fav.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/fav.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        gold: {
                            400: '#F5D061',
                            500: '#E5B84B',
                            600: '#D97706',
                        }
                    }
                }
            }
        }
    </script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        html {
            font-size: 14px !important;
        }
    </style>
</head>
<body class="h-full font-sans bg-slate-50 text-slate-900 flex min-h-screen">
    
    @php
        $pendingAppsCount = \App\Models\Professional::where('status', 'pending')->count();
        $activeRequestsCount = \App\Models\StaffingRequest::whereIn('status', ['new', 'under_review', 'staff_matching'])->count();
    @endphp

    <div class="flex w-full min-h-screen relative overflow-x-hidden">
        
        <!-- Mobile Sidebar Backdrop -->
        <div id="mobile-sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-slate-950/70 z-40 hidden lg:hidden transition-opacity backdrop-blur-sm"></div>

        <!-- Sticky Side Navigation Bar -->
        <aside id="mobile-sidebar" class="fixed lg:sticky lg:top-0 h-screen w-72 lg:w-64 bg-slate-950 text-white p-6 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col justify-between shrink-0 shadow-2xl border-r border-slate-800 overflow-y-auto">
            <div>
                <!-- Brand Header & Close Button -->
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-800/80">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                        <img src="{{ asset('images/africrew_logo.jpg') }}" alt="AfriCrew - Crew Connect Hub" class="h-9 w-auto object-contain bg-white rounded-lg p-1">
                    </a>
                    <button onclick="toggleMobileSidebar()" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>


                <!-- Core Navigation Links -->
                <nav class="space-y-1 text-xs font-bold">
                    <span class="text-[10px] uppercase font-extrabold text-slate-500 tracking-widest px-1 block mb-1">Operations</span>
                    
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span>Dashboard</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.requests.index') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.requests.*') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 022 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            <span>Crew Requests</span>
                        </div>
                        @if($activeRequestsCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-gold-400 border border-amber-500/30">
                                {{ $activeRequestsCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-sm">🏷️</span>
                            <span>Crew Categories</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.skills.index') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.skills.*') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-sm">⚡</span>
                            <span>Category Skills</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.professionals.index') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.professionals.*') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span>Vetted Crew Pool</span>
                        </div>
                        @if($pendingAppsCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-slate-950" title="Pending Verification Applications">
                                {{ $pendingAppsCount }} New
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.clients.index') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.clients.*') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-sm">🏢</span>
                            <span>Client Accounts</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.payments.index') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.payments.*') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-sm">💳</span>
                            <span>Payments & Escrow</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.tickets.index') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.tickets.*') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-sm">🎫</span>
                            <span>Support Desk</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.reviews.index') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.reviews.*') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-sm">⭐</span>
                            <span>Reviews & Ratings</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-sm">⚙️</span>
                            <span>System Settings</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.subadmins.index') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.subadmins.*') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-sm">🛡️</span>
                            <span>Sub-Admins & Roles</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.coupons.index') }}" 
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.coupons.*') ? 'bg-amber-500/15 text-gold-400 font-extrabold border-l-4 border-gold-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-sm">🏷️</span>
                            <span>Promos & Coupons</span>
                        </div>
                    </a>
                </nav>
            </div>

            <!-- Footer Account Badge -->
            <div class="pt-4 border-t border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 text-gold-400 font-bold flex items-center justify-center text-xs">
                        OP
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-white truncate">{{ Auth::user()?->name ?: 'Ops Manager' }}</div>
                        <div class="text-[10px] text-slate-400 truncate">{{ Auth::user()?->email }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <!-- Top Header Navbar -->
            <header class="bg-white border-b border-slate-200/80 px-4 sm:px-6 py-3.5 flex justify-between items-center sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <!-- Mobile Menu Toggle Button -->
                    <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 rounded-xl text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors focus:outline-none" aria-label="Toggle Navigation">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-slate-900 text-sm sm:text-base">Operations Admin</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">Active</span>
                    </div>
                </div>

                <!-- Account & Sign Out -->
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500 font-semibold hidden sm:inline">{{ Auth::user()?->email }}</span>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-slate-600 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 px-3.5 py-2 rounded-xl border border-slate-200 transition-all">
                            Sign Out
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="p-4 sm:p-6 md:p-8 max-w-7xl w-full mx-auto flex-grow">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold flex items-center gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Workspace Footer Partial -->
            @include('partials.footer')
        </div>
    </div>


    <!-- Mobile Sidebar Toggle Script & SweetAlert Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Admin Success',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#E5B84B',
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Admin Error',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#E5B84B'
                });
            @endif

            @if(isset($errors) && $errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Please check your inputs',
                    html: '<ul class="text-left text-xs text-rose-600 font-semibold space-y-1">@foreach($errors->all() as $err)<li>• {{ addslashes($err) }}</li>@endforeach</ul>',
                    confirmButtonColor: '#E5B84B'
                });
            @endif
        });

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
