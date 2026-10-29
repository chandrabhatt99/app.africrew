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
    <!-- Global Public Marketing Footer (Matching Africrew Reference Specification) -->
    <footer id="contact" class="bg-[#080D1D] text-slate-300 pt-16 sm:pt-20 pb-12 transition-colors duration-200 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            
            <!-- Top Section: Newsletter Banner -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 pb-16">
                <!-- Left Heading & Subtitle -->
                <div class="max-w-2xl">
                    <h2 class="text-3xl sm:text-4xl lg:text-[46px] font-black text-white tracking-tight leading-[1.12] m-0">
                        Stay connected to the people<br class="hidden md:inline"> behind great events.
                    </h2>
                    <p class="text-slate-300 text-sm sm:text-base font-normal mt-3 m-0">
                        Crew stories, practical event tips and AfriCrew updates.
                    </p>
                </div>

                <!-- Right Newsletter Subscribe Input -->
                <div class="w-full lg:w-auto lg:min-w-[440px]">
                    <form onsubmit="handleNewsletterSubscribe(event)" class="w-full">
                        <div class="flex items-stretch rounded-md overflow-hidden bg-white shadow-sm border border-transparent focus-within:ring-2 focus-within:ring-amber-500/50">
                            <input 
                                type="email" 
                                id="footer-newsletter-email" 
                                placeholder="Enter your email address" 
                                required 
                                class="flex-1 min-w-0 px-4 py-3 sm:py-3.5 text-sm sm:text-base text-slate-900 placeholder-slate-400 bg-white border-0 outline-none focus:outline-none focus:ring-0"
                            >
                            <button 
                                type="submit" 
                                class="px-6 sm:px-8 py-3 sm:py-3.5 bg-[#FF6B00] hover:bg-[#e85f00] text-slate-950 font-extrabold text-sm sm:text-base shrink-0 transition-colors cursor-pointer border-0 flex items-center justify-center tracking-tight"
                            >
                                Subscribe
                            </button>
                        </div>
                        <p class="text-xs text-slate-400 mt-2 m-0 font-normal">
                            Unsubscribe anytime.
                        </p>
                    </form>
                </div>
            </div>

            <!-- Middle Section: 4 Columns (Seamless Flow, No Hard Border) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pt-6 pb-20">
                
                <!-- Col 1: Brand Logo, Description & Social Icons (lg:col-span-4) -->
                <div class="lg:col-span-4 space-y-4">
                    <a href="{{ route('home') }}" class="inline-block text-decoration-none group">
                        <img src="{{ asset('images/logo-white.png') }}" alt="AfriCrew - CREW CONNECT HUB" class="h-14 sm:h-16 w-auto object-contain">
                    </a>
                    
                    <p class="text-sm text-slate-300 leading-relaxed font-normal max-w-sm m-0">
                        Connecting skilled event professionals with organisers, agencies and brands across Kenya.
                    </p>

                    <!-- Social Media Outline Icons (WhatsApp, LinkedIn, Instagram, Facebook, X, YouTube) -->
                    <div class="flex items-center gap-4 sm:gap-5 pt-3 text-white">
                        <!-- Chat / WhatsApp (Speech Bubble with tail) -->
                        <a href="https://wa.me/254713633633" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-400 transition-colors text-decoration-none" title="Chat on WhatsApp" aria-label="WhatsApp">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                            </svg>
                        </a>

                        <!-- LinkedIn -->
                        <a href="https://linkedin.com/company/africrew" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-400 transition-colors text-decoration-none" title="LinkedIn" aria-label="LinkedIn">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="20" x="2" y="2" rx="4" ry="4"></rect>
                                <path d="M8 11v5"></path>
                                <path d="M8 8v.01"></path>
                                <path d="M12 16v-5"></path>
                                <path d="M16 16v-3a2 2 0 0 0-4 0"></path>
                            </svg>
                        </a>

                        <!-- Instagram -->
                        <a href="https://instagram.com/africrew" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-400 transition-colors text-decoration-none" title="Instagram" aria-label="Instagram">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                            </svg>
                        </a>

                        <!-- Facebook -->
                        <a href="https://facebook.com/africrew" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-400 transition-colors text-decoration-none" title="Facebook" aria-label="Facebook">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </a>

                        <!-- X (Twitter) -->
                        <a href="https://x.com/africrew" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-400 transition-colors text-decoration-none" title="X" aria-label="X">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>

                        <!-- YouTube -->
                        <a href="https://youtube.com/@africrew" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-400 transition-colors text-decoration-none" title="YouTube" aria-label="YouTube">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17z"></path>
                                <path fill="currentColor" stroke="none" d="m10 15 5-3-5-3z"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Find Crew (lg:col-span-3) -->
                <div class="lg:col-span-3 space-y-4">
                    <h3 class="text-base sm:text-lg font-bold text-white tracking-wide m-0">
                        Find Crew
                    </h3>
                    <ul class="space-y-2.5 text-sm font-medium text-slate-300 list-none p-0 m-0">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                            <a href="{{ route('crew.index', ['category' => 'Ushers']) }}" class="hover:text-white transition-colors text-decoration-none text-slate-300">Event Ushers</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                            <a href="{{ route('crew.index', ['category' => 'DJ / Sound Engineer']) }}" class="hover:text-white transition-colors text-decoration-none text-slate-300">Technical Crew</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                            <a href="{{ route('crew.index', ['category' => 'Decorations']) }}" class="hover:text-white transition-colors text-decoration-none text-slate-300">Decorations Crew</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                            <a href="{{ route('crew.index', ['category' => 'Photographer / Videographer']) }}" class="hover:text-white transition-colors text-decoration-none text-slate-300">Multi-Media Crew</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                            <a href="{{ route('crew.index', ['category' => 'Entertainers']) }}" class="hover:text-white transition-colors text-decoration-none text-slate-300">Entertainers &amp; Performers</a>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Explore AfriCrew (lg:col-span-2) -->
                <div class="lg:col-span-2 space-y-4">
                    <h3 class="text-base sm:text-lg font-bold text-white tracking-wide m-0">
                        Explore AfriCrew
                    </h3>
                    <ul class="space-y-2.5 text-sm font-medium text-slate-300 list-none p-0 m-0">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                            <a href="https://africrew.com" class="hover:text-white transition-colors text-decoration-none text-slate-300">About AfriCrew</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                            <a href="{{ route('crew.create') }}" class="hover:text-white transition-colors text-decoration-none text-slate-300">Join as Crew</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                            <a href="https://africrew.com/hire" class="hover:text-white transition-colors text-decoration-none text-slate-300">Hire Hub</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                            <a href="https://africrew.com/blog" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors text-decoration-none text-slate-300">Blog</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                            <a href="#contact" class="hover:text-white transition-colors text-decoration-none text-slate-300">Contact Us</a>
                        </li>
                    </ul>
                </div>

                <!-- Col 4: Contact & Support (lg:col-span-3) -->
                <div class="lg:col-span-3 space-y-4">
                    <h3 class="text-base sm:text-lg font-bold text-white tracking-wide m-0">
                        Contact &amp; Support
                    </h3>
                    
                    <div class="space-y-2 text-sm font-normal text-slate-300">
                        <div>RWQV+QW Central, Kenya.</div>
                        <div>
                            <a href="tel:+254713633633" class="text-slate-300 hover:text-white transition-colors text-decoration-none font-medium">+254 713633633</a>
                        </div>
                        <div>
                            <a href="mailto:mail@africrew.com" class="text-slate-300 hover:text-white transition-colors text-decoration-none font-medium">mail@africrew.com</a>
                        </div>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal pt-1 m-0">
                        Manage on-site shifts, coordinate bookings, and hire vetted crew on the go.
                    </p>

                    <!-- App Download Badges with "SOON" Pills -->
                    <div class="flex items-center gap-3 pt-2 flex-wrap">
                        <!-- App Store Badge -->
                        <div class="relative inline-block">
                            <a href="javascript:void(0)" onclick="notifyAppSoon('iOS App Store')" class="block transition-transform hover:scale-105 active:scale-95" title="Download on the App Store">
                                <img src="{{ asset('images/app-store-badge.svg') }}" alt="Download on the App Store" class="h-9.5 w-auto rounded-lg">
                            </a>
                            <span class="absolute -top-2.5 -right-2 px-2 py-0.5 rounded-full bg-[#FF4500] text-white text-[9px] font-black uppercase tracking-wider shadow-sm pointer-events-none">
                                SOON
                            </span>
                        </div>

                        <!-- Google Play Badge -->
                        <div class="relative inline-block">
                            <a href="javascript:void(0)" onclick="notifyAppSoon('Google Play')" class="block transition-transform hover:scale-105 active:scale-95" title="Get it on Google Play">
                                <img src="{{ asset('images/google-play-badge.svg') }}" alt="Get it on Google Play" class="h-9.5 w-auto rounded-lg">
                            </a>
                            <span class="absolute -top-2.5 -right-2 px-2 py-0.5 rounded-full bg-[#FF4500] text-white text-[9px] font-black uppercase tracking-wider shadow-sm pointer-events-none">
                                SOON
                            </span>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Bottom Bar: Copyright & Legal Navigation -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs sm:text-sm text-slate-400 font-normal">
                <div>
                    &copy; 2026. All Rights Reserved. AfriCrew&trade;
                </div>
                <div class="flex items-center gap-4 sm:gap-6 flex-wrap justify-center">
                    <a href="{{ route('privacy-policy') }}" class="text-slate-400 hover:text-white transition-colors text-decoration-none">Privacy Policy</a>
                    <span class="text-slate-600 select-none">|</span>
                    <a href="{{ route('terms-conditions') }}" class="text-slate-400 hover:text-white transition-colors text-decoration-none">Terms of Service</a>
                    <span class="text-slate-600 select-none">|</span>
                    <a href="{{ route('terms-conditions') }}" class="text-slate-400 hover:text-white transition-colors text-decoration-none">Crew Guidelines</a>
                </div>
            </div>

        </div>
    </footer>

    <!-- Interactive Handlers for Newsletter & App Notifications -->
    <script>
        function handleNewsletterSubscribe(event) {
            event.preventDefault();
            const input = document.getElementById('footer-newsletter-email');
            const email = input ? input.value : '';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Subscribed!',
                    text: 'Thank you for subscribing to AfriCrew updates. We will keep you posted on crew stories and event tips.',
                    confirmButtonColor: '#FF6B00',
                    background: '#080D1D',
                    color: '#ffffff'
                });
            } else {
                alert('Thank you for subscribing to AfriCrew updates!');
            }

            if (input) input.value = '';
        }

        function notifyAppSoon(platform) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'App Coming Soon',
                    text: `The AfriCrew mobile app for ${platform} is currently in final testing and will launch soon!`,
                    confirmButtonColor: '#FF6B00',
                    background: '#080D1D',
                    color: '#ffffff'
                });
            } else {
                alert(`The AfriCrew mobile app for ${platform} is launching soon!`);
            }
        }
    </script>
@endif