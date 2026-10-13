@php
    $isAuthRoute = request()->routeIs('client.login') || request()->routeIs('client.register') || request()->routeIs('staff.login') || request()->routeIs('admin.login');
    $isCrmPanel = !$isAuthRoute && (request()->routeIs('client.*') || request()->routeIs('professional.*') || request()->routeIs('messages') || request()->routeIs('admin.*'));
@endphp

@if($isCrmPanel)
    <!-- Compact CRM Workspace Footer -->
    <footer id="main-footer" class="bg-white border-t border-slate-200/90 py-4 px-6 text-xs text-slate-500 font-semibold mt-auto transition-colors duration-200">
        <div class="w-full max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <span>&copy; {{ date('Y') }} AFRICREW. All rights reserved.</span>
            </div>
            <div class="flex items-center gap-4 text-[11px] text-slate-400 font-medium">
                <a href="{{ route('privacy-policy') }}" class="hover:text-slate-700 cursor-pointer text-decoration-none text-slate-400">Privacy Policy</a>
                <span>•</span>
                <a href="{{ route('terms-conditions') }}" class="hover:text-slate-700 cursor-pointer text-decoration-none text-slate-400">Terms & Conditions</a>
                <span>•</span>
                <a href="{{ route('professional.support') }}" class="hover:text-amber-600 text-decoration-none text-slate-500 font-bold">Support Desk</a>
            </div>
        </div>
    </footer>
@else
    <!-- Global Public Marketing Footer (Matching africrew.com) -->
    <footer id="contact" class="bg-white text-slate-700 pt-16 pb-10 border-t border-slate-200/90 transition-colors duration-200 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-6 sm:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pb-14 border-b border-slate-100">
                
                <!-- Col 1: Brand & Bio + Newsletter (lg:col-span-5) -->
                <div class="lg:col-span-5 space-y-6">
                    <a href="{{ route('home') }}" class="flex items-center text-decoration-none group">
                        <img src="{{ asset('images/africrew_logo.jpg') }}" alt="AfriCrew - CREW CONNECT HUB" class="h-11 sm:h-12 w-auto object-contain">
                    </a>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-sm font-medium">
                        AfriCrew connects event organisers, agencies and brands with trusted event professionals, making it easier to discover and hire the right crew for every event, campaign and production.
                    </p>

                    <!-- Subscribe For Staffing Insights & Updates -->
                    <div class="pt-4 space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-amber-800 m-0">
                            SUBSCRIBE FOR STAFFING INSIGHTS & UPDATES
                        </h4>
                        <form onsubmit="event.preventDefault(); Swal.fire({ icon: 'success', title: 'Subscribed!', text: 'Thank you for subscribing to AfriCrew staffing insights.', confirmButtonColor: '#F59E0B' });" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 max-w-md">
                            <input type="email" placeholder="Enter your work email address" required class="flex-1 px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200/90 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-amber-500 focus:bg-white transition-all shadow-2xs">
                            <button type="submit" class="px-6 py-3 rounded-2xl text-white font-extrabold text-xs sm:text-sm bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 hover:opacity-95 shadow-md shadow-rose-500/20 flex items-center justify-center gap-2 shrink-0 transition-all cursor-pointer">
                                <span>Subscribe</span>
                                <svg class="w-4 h-4 rotate-45" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Col 2: NAVIGATION (lg:col-span-2) -->
                <div class="lg:col-span-2 space-y-4">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-950 m-0">
                        NAVIGATION
                    </h4>
                    <ul class="space-y-3 text-xs sm:text-sm font-bold text-slate-700 list-none p-0 m-0">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                            <a href="{{ route('crew.index') }}" class="hover:text-amber-600 transition-colors text-decoration-none text-slate-700">Hire Crew</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                            <a href="https://africrew.com/hire" class="hover:text-amber-600 transition-colors text-decoration-none text-slate-700">Hire Hub</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                            <a href="https://africrew.com/blog" target="_blank" rel="noopener noreferrer" class="hover:text-amber-600 transition-colors text-decoration-none text-slate-700">Latest News</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                            <a href="#contact" class="hover:text-amber-600 transition-colors text-decoration-none text-slate-700">Contact Us</a>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: HIRE HUB (lg:col-span-2) -->
                <div class="lg:col-span-2 space-y-4">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-950 m-0">
                        HIRE HUB
                    </h4>
                    <ul class="space-y-3 text-xs sm:text-sm font-bold text-slate-700 list-none p-0 m-0">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                            <a href="{{ route('crew.index', ['category' => 'Ushers']) }}" class="hover:text-amber-600 transition-colors text-decoration-none text-slate-700">Event Ushers</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                            <a href="{{ route('crew.index', ['category' => 'DJ / Sound Engineer']) }}" class="hover:text-amber-600 transition-colors text-decoration-none text-slate-700">Technical Crew</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                            <a href="{{ route('crew.index', ['category' => 'Security Staff']) }}" class="hover:text-amber-600 transition-colors text-decoration-none text-slate-700">BTL Crew</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                            <a href="{{ route('crew.index', ['category' => 'Photographer / Videographer']) }}" class="hover:text-amber-600 transition-colors text-decoration-none text-slate-700">Multi-Media</a>
                        </li>
                        <li class="pt-1">
                            <a href="https://africrew.com/hire" class="text-xs font-extrabold text-amber-700 hover:text-amber-800 transition-colors text-decoration-none flex items-center gap-1.5">
                                <span>View More</span>
                                <span>→</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Col 4: CONTACT & SUPPORT (lg:col-span-3) -->
                <div class="lg:col-span-3 space-y-4">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-950 m-0">
                        CONTACT & SUPPORT
                    </h4>
                    <div class="space-y-3 text-xs sm:text-sm font-bold text-slate-800">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>RWQV+QW Ruiru, Kenya.</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:+254713633633" class="hover:text-amber-600 text-slate-800 transition-colors text-decoration-none">+254 713633633</a>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:mail@africrew.com" class="hover:text-amber-600 text-slate-800 transition-colors text-decoration-none">mail@africrew.com</a>
                        </div>
                    </div>

                    <!-- Social Media Rounded Icons Matching africrew.com -->
                    <div class="pt-2 space-y-2">
                        <div class="flex items-center gap-2.5">
                            <!-- Instagram -->
                            <a href="https://instagram.com/africrew" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shadow-2xs" title="Instagram" aria-label="Instagram">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
                            </a>
                            <!-- Facebook -->
                            <a href="https://facebook.com/africrew" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shadow-2xs" title="Facebook" aria-label="Facebook">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                            </a>
                            <!-- YouTube -->
                            <a href="https://youtube.com/@africrew" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shadow-2xs" title="YouTube" aria-label="YouTube">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"></path><path d="m10 15 5-3-5-3z"></path></svg>
                            </a>
                            <!-- TikTok -->
                            <a href="https://tiktok.com/@africrew" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shadow-2xs" title="TikTok" aria-label="TikTok">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525 2.015c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.82.55-1.35 1.52-1.37 2.51-.04 1.25.66 2.45 1.75 3.01 1.05.57 2.37.49 3.37-.17.84-.54 1.47 1.45-2.46.04-3.53.01-7.06.02-10.59z"></path></svg>
                            </a>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <!-- X (Twitter) -->
                            <a href="https://x.com/africrew" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shadow-2xs" title="X" aria-label="X">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg>
                            </a>
                            <!-- LinkedIn -->
                            <a href="https://linkedin.com/company/africrew" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shadow-2xs" title="LinkedIn" aria-label="LinkedIn">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Bar Matching africrew.com -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-bold text-slate-500">
                <div>
                    &copy; {{ date('Y') }}. All Rights Reserved. AfriCrew™
                </div>
                <div class="flex items-center gap-6">
                    <a href="{{ route('privacy-policy') }}" class="hover:text-slate-900 transition-colors text-decoration-none text-slate-500">Privacy Policy</a>
                    <a href="{{ route('terms-conditions') }}" class="hover:text-slate-900 transition-colors text-decoration-none text-slate-500">Terms of Service</a>
                    <a href="{{ route('terms-conditions') }}" class="hover:text-slate-900 transition-colors text-decoration-none text-slate-500">Crew Guidelines</a>
                </div>
            </div>

        </div>
    </footer>
@endif