<!-- CREW PROFILE MODAL POPUP (PDF PAGE 4 SPECIFICATION) -->
<div id="crew-profile-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/80 backdrop-blur-md transition-opacity duration-300">
    <div class="min-h-screen px-4 text-center flex items-center justify-center py-8">
        
        <!-- Modal Backdrop Click Handler -->
        <div class="fixed inset-0 transition-opacity" onclick="closeCrewModal()"></div>

        <!-- Modal Content Container Box -->
        <div class="inline-block w-full max-w-4xl p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl relative z-10 border border-slate-200">
            
            <!-- Top Fixed Modal Header Bar -->
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-amber-500 text-slate-950 font-black text-xs flex items-center justify-center">4/4</span>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block leading-tight">CREW PROFILE</span>
                        <h2 class="text-sm font-extrabold text-white leading-tight m-0" id="modal-crew-title">Viewing Patrick Mwangi</h2>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="shareCrewProfile()" class="p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition-all text-xs" title="Share Profile">
                        🔗
                    </button>
                    <button type="button" onclick="closeCrewModal()" class="p-2 rounded-xl bg-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white transition-all text-xs font-black">
                        ✕
                    </button>
                </div>
            </div>

            <!-- Scrollable Inner Content Area -->
            <div class="p-6 sm:p-8 space-y-8 max-h-[80vh] overflow-y-auto">
                
                <!-- Main Header Grid: Photo Banner Left + Quick Stats & Quote Right -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Left: Large Photo Banner with Avatar & Role -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="relative rounded-3xl overflow-hidden bg-slate-900 shadow-md h-64 sm:h-72 group">
                            <img id="modal-cover-img" src="{{ asset('images/hero_event_ushers.jpg') }}" alt="Crew Cover Photo" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/20"></div>
                            
                            <!-- Available Badge -->
                            <div class="absolute top-4 left-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/90 text-white font-extrabold text-[11px] uppercase tracking-wider shadow-sm backdrop-blur-sm">
                                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                    <span>AVAILABLE FOR HIRE</span>
                                </span>
                            </div>

                            <!-- Overlapping Profile Avatar & Name Info -->
                            <div class="absolute bottom-4 left-4 right-4 flex items-end gap-3.5">
                                <div class="relative shrink-0">
                                    <img id="modal-avatar-img" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400" alt="Avatar" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border-2 border-white shadow-xl">
                                    <span class="w-4 h-4 rounded-full bg-emerald-500 border-2 border-white absolute -bottom-1 -right-1"></span>
                                </div>
                                <div class="text-white space-y-0.5">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-lg sm:text-xl font-black tracking-tight" id="modal-crew-name">Patrick Mwangi</h3>
                                        <span class="px-2 py-0.5 rounded-md bg-amber-500 text-slate-950 font-black text-[9px] uppercase tracking-wider">PRO</span>
                                    </div>
                                    <p class="text-xs text-slate-300 font-medium" id="modal-crew-category">Professional Event Usher & Guest Experience Specialist</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Quick Stats Card & Testimonial Card -->
                    <div class="lg:col-span-5 space-y-4">
                        
                        <!-- Quick Stats Grid -->
                        <div class="bg-slate-50 border border-slate-200/80 rounded-3xl p-5 shadow-xs space-y-3">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">QUICK STATS</span>
                            
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div class="bg-white p-3 rounded-2xl border border-slate-200/60 shadow-2xs">
                                    <div class="text-xl font-black text-slate-950" id="modal-stat-events">142</div>
                                    <div class="text-[10px] font-bold text-slate-500 uppercase mt-0.5">Events done</div>
                                </div>
                                <div class="bg-white p-3 rounded-2xl border border-slate-200/60 shadow-2xs">
                                    <div class="text-xl font-black text-amber-600" id="modal-stat-rating">4.9</div>
                                    <div class="text-[10px] font-bold text-slate-500 uppercase mt-0.5">Avg. rating</div>
                                </div>
                                <div class="bg-white p-3 rounded-2xl border border-slate-200/60 shadow-2xs">
                                    <div class="text-xl font-black text-slate-950" id="modal-stat-ontime">98%</div>
                                    <div class="text-[10px] font-bold text-slate-500 uppercase mt-0.5">On-time rate</div>
                                </div>
                                <div class="bg-white p-3 rounded-2xl border border-slate-200/60 shadow-2xs">
                                    <div class="text-xl font-black text-slate-950" id="modal-stat-exp">7+</div>
                                    <div class="text-[10px] font-bold text-slate-500 uppercase mt-0.5">Years exp.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial / Quote Box -->
                        <div class="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-5 relative space-y-2">
                            <div class="text-amber-600 font-serif text-3xl font-black leading-none opacity-40">“</div>
                            <p class="text-xs font-semibold text-slate-800 italic leading-relaxed" id="modal-quote-text">
                                "Great events are built by great people. Let's create something amazing together!"
                            </p>
                            <div class="text-[11px] font-black text-amber-800 text-right">— <span id="modal-quote-author">Patrick Mwangi</span></div>
                        </div>

                    </div>

                </div>

                <!-- Tabs & Tab Content -->
                <div class="border-b border-slate-200">
                    <nav class="flex gap-6 text-xs font-extrabold text-slate-500">
                        <button type="button" onclick="switchModalTab('about')" id="modal-tab-btn-about" class="pb-3 border-b-2 border-amber-500 text-slate-950 font-black">ABOUT</button>
                        <button type="button" onclick="switchModalTab('experience')" id="modal-tab-btn-experience" class="pb-3 border-b-2 border-transparent hover:text-slate-900">EXPERIENCE</button>
                        <button type="button" onclick="switchModalTab('services')" id="modal-tab-btn-services" class="pb-3 border-b-2 border-transparent hover:text-slate-900">SERVICES</button>
                        <button type="button" onclick="switchModalTab('reviews')" id="modal-tab-btn-reviews" class="pb-3 border-b-2 border-transparent hover:text-slate-900">REVIEWS</button>
                    </nav>
                </div>

                <!-- Tab Content Sections -->
                <div id="modal-tab-content-about" class="space-y-4">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-700 block">THE PERSONA</span>
                    <blockquote class="text-base sm:text-lg font-black text-slate-900 italic border-l-4 border-amber-500 pl-4 py-1" id="modal-persona-quote">
                        "Guests rarely remember who showed them their seat — but they always remember how they were treated."
                    </blockquote>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal" id="modal-about-body">
                        Hello, I'm Patrick. With over seven years of experience across corporate galas, product launches, and high-profile conferences, I bring professionalism and grace to every project.
                    </p>
                </div>

                <div id="modal-tab-content-experience" class="hidden space-y-3 text-xs text-slate-700">
                    <h4 class="font-extrabold text-slate-900 text-sm">Key Event Highlights</h4>
                    <ul class="list-disc pl-5 space-y-1 text-slate-600">
                        <li>Lead Guest Coordinator — Annual East Africa Tech Summit (2,500+ attendees)</li>
                        <li>VIP Ushers Lead — Nairobi International Trade Gala</li>
                        <li>Brand Ambassador Lead — Safaricom Corporate Expo</li>
                    </ul>
                </div>

                <div id="modal-tab-content-services" class="hidden space-y-3 text-xs">
                    <h4 class="font-extrabold text-slate-900 text-sm">Offered Crew Services</h4>
                    <div class="flex flex-wrap gap-2" id="modal-skills-list">
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 font-bold">Guest Check-In</span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 font-bold">VIP Hospitality</span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 font-bold">Badge Printing</span>
                    </div>
                </div>

                <div id="modal-tab-content-reviews" class="hidden space-y-3 text-xs">
                    <h4 class="font-extrabold text-slate-900 text-sm">Client Reviews & Testimonials</h4>
                    <div id="modal-reviews-list" class="space-y-3">
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                            <div class="flex justify-between font-bold text-slate-900">
                                <span>Sarah Jenkins (Event Director)</span>
                                <span class="text-amber-500">★★★★★ 5.0</span>
                            </div>
                            <p class="text-slate-600">Patrick was exceptionally punctual and managed our guest seating perfectly!</p>
                        </div>
                    </div>
                </div>

                <!-- Rates & Responds Box -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">STARTING FROM</span>
                        <div class="text-lg font-black text-slate-950">
                            <span id="modal-rate-amount">KES 8,500</span> <span class="text-xs text-slate-500 font-normal">/ day</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-emerald-600 font-extrabold bg-emerald-50 px-3.5 py-1.5 rounded-full border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>Responds within 1 hr</span>
                    </div>
                </div>

                <!-- Bottom Sticky Footer Actions Bar -->
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between gap-4">
                    <!-- Search More Button -->
                    <button type="button" onclick="closeCrewModal()" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs transition-all flex items-center gap-2 shadow-2xs">
                        <span>🔍</span>
                        <span>Search More</span>
                    </button>

                    <!-- ADD ME TO YOUR TEAM Action Button -->
                    <button type="button" id="modal-add-team-btn" onclick="addCurrentModalCrewToTeam()" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-rose-500 to-amber-500 hover:from-rose-600 hover:to-amber-600 text-white font-black text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center gap-2 uppercase tracking-wider">
                        <span>⊕ ADD ME TO YOUR TEAM</span>
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
let currentModalCrew = null;

function openCrewModal(crewId) {
    if (crewId) {
        window.location.href = '/crew/' + crewId;
        return;
    }
}

function closeCrewModal() {
    const modal = document.getElementById('crew-profile-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

function switchModalTab(tabName) {
    ['about', 'experience', 'services', 'reviews'].forEach(t => {
        const btn = document.getElementById(`modal-tab-btn-${t}`);
        const content = document.getElementById(`modal-tab-content-${t}`);
        if (btn && content) {
            if (t === tabName) {
                btn.className = 'pb-3 border-b-2 border-amber-500 text-slate-950 font-black';
                content.classList.remove('hidden');
            } else {
                btn.className = 'pb-3 border-b-2 border-transparent hover:text-slate-900 text-slate-500 font-bold';
                content.classList.add('hidden');
            }
        }
    });
}

function shareCrewProfile() {
    if (navigator.clipboard && currentModalCrew) {
        const url = `${window.location.origin}/crew/${currentModalCrew.id}`;
        navigator.clipboard.writeText(url);
        Swal.fire({
            icon: 'success',
            title: 'Link Copied!',
            text: `Profile link for ${currentModalCrew.full_name} copied to clipboard.`,
            timer: 2000,
            confirmButtonColor: '#F59E0B'
        });
    }
}

function addCurrentModalCrewToTeam() {
    if (currentModalCrew && typeof addCrewToTeamBasket === 'function') {
        addCrewToTeamBasket(currentModalCrew);
        closeCrewModal();
    }
}
</script>
