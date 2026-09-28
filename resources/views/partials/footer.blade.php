@php
    $isCrmPanel = request()->routeIs('client.*') || request()->routeIs('professional.*') || request()->routeIs('messages') || request()->routeIs('admin.*');
@endphp

@if($isCrmPanel)
    <!-- Compact CRM Workspace Footer -->
    <footer id="main-footer" class="bg-white border-t border-slate-200/90 py-4 px-6 text-xs text-slate-500 font-semibold mt-auto transition-colors duration-200">
        <div class="w-full max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <span>&copy; {{ date('Y') }} AFRICREW. All rights reserved.</span>
                <span class="hidden sm:inline text-slate-300">|</span>
                <span class="hidden sm:inline text-slate-400">Developed by <a href="https://databrainit.com" target="_blank" rel="noopener noreferrer" class="font-bold text-amber-600 hover:text-amber-700 hover:underline">Databrain Technology Pvt. Ltd.</a></span>
            </div>
            <div class="flex items-center gap-4 text-[11px] text-slate-400 font-medium">
                <a href="{{ route('privacy') }}" class="hover:text-slate-700 cursor-pointer text-decoration-none text-slate-400">Privacy Policy</a>
                <span>•</span>
                <a href="{{ route('terms') }}" class="hover:text-slate-700 cursor-pointer text-decoration-none text-slate-400">Terms of Service</a>
                <span>•</span>
                <a href="{{ route('professional.support') }}" class="hover:text-amber-600 text-decoration-none text-slate-500 font-bold">Support Desk</a>
            </div>
        </div>
    </footer>
@else
    <!-- Global Public Marketing Footer -->
    <footer id="main-footer" class="bg-white text-slate-600 pt-12 pb-8 border-t border-slate-200/90 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 pb-10 border-b border-slate-100">
                
                <!-- Col 1: Brand & Bio (2 cols) -->
                <div class="lg:col-span-2 space-y-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-decoration-none group">
                        <img src="{{ asset('images/africrew_logo.jpg') }}" alt="AfriCrew - Crew Connect Hub" class="h-10 w-auto object-contain">
                    </a>
                    <p class="text-xs text-slate-500 leading-relaxed max-w-sm">
                        Africa's premier platform for booking vetted event ushers, VIP hostesses, brand ambassadors, protocol officers, and on-site hospitality talent.
                    </p>

                    <div class="flex items-center gap-2 pt-2 text-sm text-slate-600 flex-wrap">
                        <a href="https://instagram.com/africrew" target="_blank" rel="noopener noreferrer" class="w-8.5 h-8.5 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-700 hover:bg-gradient-to-r hover:from-amber-500 hover:to-pink-500 hover:text-white hover:border-transparent transition-all duration-200 shadow-xs flex items-center justify-center p-2 group" title="Instagram" aria-label="Instagram">
                            <svg class="w-4 h-4 transition-transform group-hover:scale-110 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
                        </a>
                        <a href="https://facebook.com/africrew" target="_blank" rel="noopener noreferrer" class="w-8.5 h-8.5 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-700 hover:bg-gradient-to-r hover:from-amber-500 hover:to-pink-500 hover:text-white hover:border-transparent transition-all duration-200 shadow-xs flex items-center justify-center p-2 group" title="Facebook" aria-label="Facebook">
                            <svg class="w-4 h-4 transition-transform group-hover:scale-110 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                        </a>
                        <a href="https://youtube.com/@africrew" target="_blank" rel="noopener noreferrer" class="w-8.5 h-8.5 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-700 hover:bg-gradient-to-r hover:from-amber-500 hover:to-pink-500 hover:text-white hover:border-transparent transition-all duration-200 shadow-xs flex items-center justify-center p-2 group" title="YouTube" aria-label="YouTube">
                            <svg class="w-4 h-4 transition-transform group-hover:scale-110 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"></path><path d="m10 15 5-3-5-3z"></path></svg>
                        </a>
                        <a href="https://tiktok.com/@africrew" target="_blank" rel="noopener noreferrer" class="w-8.5 h-8.5 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-700 hover:bg-gradient-to-r hover:from-amber-500 hover:to-pink-500 hover:text-white hover:border-transparent transition-all duration-200 shadow-xs flex items-center justify-center p-2 group" title="TikTok" aria-label="TikTok">
                            <svg class="w-4 h-4 transition-transform group-hover:scale-110 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525 2.015c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.82.55-1.35 1.52-1.37 2.51-.04 1.25.66 2.45 1.75 3.01 1.05.57 2.37.49 3.37-.17.84-.54 1.47 1.45-2.46.04-3.53.01-7.06.02-10.59z"></path></svg>
                        </a>
                        <a href="https://x.com/africrew" target="_blank" rel="noopener noreferrer" class="w-8.5 h-8.5 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-700 hover:bg-gradient-to-r hover:from-amber-500 hover:to-pink-500 hover:text-white hover:border-transparent transition-all duration-200 shadow-xs flex items-center justify-center p-2 group" title="Twitter / X" aria-label="Twitter / X">
                            <svg class="w-4 h-4 transition-transform group-hover:scale-110 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg>
                        </a>
                        <a href="https://www.linkedin.com/company/africrew" target="_blank" rel="noopener noreferrer" class="w-8.5 h-8.5 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-700 hover:bg-gradient-to-r hover:from-amber-500 hover:to-pink-500 hover:text-white hover:border-transparent transition-all duration-200 shadow-xs flex items-center justify-center p-2 group" title="LinkedIn" aria-label="LinkedIn">
                            <svg class="w-4 h-4 transition-transform group-hover:scale-110 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Event Crew Categories -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">Crew Categories</h4>
                    @php
                        try {
                            $footerCategories = \App\Models\Category::where('is_active', true)->get();
                        } catch (\Throwable $e) {
                            $footerCategories = collect();
                        }
                    @endphp
                    <ul class="space-y-2 text-xs font-semibold text-slate-500 list-none p-0 m-0">
                        @if($footerCategories->count() > 0)
                            @foreach($footerCategories as $fCat)
                                <li>
                                    <a href="{{ route('crew.index', ['category' => $fCat->name]) }}" class="hover:text-amber-600 transition-colors text-decoration-none flex items-center gap-1.5">
                                        @if($fCat->icon)
                                            <span>{{ $fCat->icon }}</span>
                                        @endif
                                        <span>{{ $fCat->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li><a href="{{ route('crew.index', ['category' => 'Event Ushers']) }}" class="hover:text-amber-600 transition-colors text-decoration-none">Event Ushers</a></li>
                            <li><a href="{{ route('crew.index', ['category' => 'VIP Hostesses']) }}" class="hover:text-amber-600 transition-colors text-decoration-none">VIP Hostesses</a></li>
                            <li><a href="{{ route('crew.index', ['category' => 'Protocol Officers']) }}" class="hover:text-amber-600 transition-colors text-decoration-none">Protocol Officers</a></li>
                            <li><a href="{{ route('crew.index', ['category' => 'Security & Bouncers']) }}" class="hover:text-amber-600 transition-colors text-decoration-none">Security & Bouncers</a></li>
                            <li><a href="{{ route('crew.index', ['category' => 'Mixologists & Bartenders']) }}" class="hover:text-amber-600 transition-colors text-decoration-none">Mixologists & Bartenders</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Col 3: Platform Quick Links -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">Navigation</h4>
                    <ul class="space-y-2 text-xs font-semibold text-slate-500 list-none p-0 m-0">
                        <li><a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors text-decoration-none">Home</a></li>
                        <li><a href="{{ route('crew.index') }}" class="hover:text-amber-600 transition-colors text-decoration-none">Browse Vetted Crew</a></li>
                        <li><a href="{{ route('crew.create') }}" class="hover:text-amber-600 transition-colors text-decoration-none">Join Our Crew</a></li>
                    </ul>
                </div>

                <!-- Col 4: Trust & Contact -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">Contact & Support</h4>
                    <ul class="space-y-2 text-xs font-semibold text-slate-500 list-none p-0 m-0">
                        <li><span class="text-emerald-600 flex items-center gap-1 font-bold"><span>🛡️</span> 100% Vetted Guarantee</span></li>
                        <li class="flex items-center gap-2 pt-1 text-slate-600">
                            <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Nairobi, Kenya</span>
                        </li>
                        <li class="flex items-center gap-2 text-slate-600">
                            <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:+254723742739" class="hover:text-amber-600 transition-colors text-decoration-none text-slate-600">+254 723 742739</a>
                        </li>
                        <li class="flex items-center gap-2 text-slate-600">
                            <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:info@africrew.com" class="hover:text-amber-600 transition-colors text-decoration-none text-slate-600">info@africrew.com</a>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright Bar -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-400 font-medium">
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('privacy') }}" class="hover:text-slate-700 cursor-pointer text-decoration-none text-slate-400">Privacy Policy</a>
                    <span>•</span>
                    <a href="{{ route('terms') }}" class="hover:text-slate-700 cursor-pointer text-decoration-none text-slate-400">Terms of Service</a>
                </div>
            </div>

        </div>
    </footer>
@endif

