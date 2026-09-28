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

                    <div class="flex items-center gap-3 pt-2 text-sm text-slate-600">
                        <span class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center hover:bg-amber-500 hover:text-slate-950 transition-colors cursor-pointer" title="LinkedIn">in</span>
                        <span class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center hover:bg-amber-500 hover:text-slate-950 transition-colors cursor-pointer" title="Instagram">📸</span>
                        <span class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center hover:bg-amber-500 hover:text-slate-950 transition-colors cursor-pointer" title="WhatsApp">💬</span>
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

                <!-- Col 4: Trust & Support -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">Trust & Access</h4>
                    <ul class="space-y-2 text-xs font-semibold text-slate-500 list-none p-0 m-0">

                        <li><span class="text-emerald-600 flex items-center gap-1 font-bold"><span>🛡️</span> 100% Vetted Guarantee</span></li>
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

