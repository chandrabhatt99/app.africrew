@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Custom Keyframe Animations -->
<style>
@keyframes crewCardEntrance {
    0% {
        opacity: 0;
        transform: translateY(16px) scale(0.96);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.animate-crew-card {
    animation: crewCardEntrance 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>

<!-- Top Announcement Bar -->
<div class="bg-gradient-to-r from-amber-50 via-amber-100 to-amber-50 text-slate-900 py-2.5 px-4 sm:px-6 border-b border-amber-200/80 text-xs font-semibold text-center flex flex-wrap items-center justify-center gap-2 shadow-2xs">
    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-amber-500 text-slate-950 font-black text-[10px] uppercase tracking-wider shrink-0">
        <span>📱</span>
        <span>AfriCrew App</span>
    </span>
    <span class="text-slate-800">
        ✨ <strong>AfriCrew is even better on mobile!</strong> Download our app on iOS & Android to book crew faster & manage events on the go.
    </span>
    <a href="#download-app" onclick="alert('AfriCrew Mobile App is available on the Apple App Store and Google Play Store!')" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-[11px] transition-all text-decoration-none ml-1">
        <span>Download on iOS / Android →</span>
    </a>
</div>

<!-- ========================================================= -->
<!-- HERO SECTION (PDF PAGE 1 SPECIFICATION) -->
<!-- ========================================================= -->
<section class="relative overflow-hidden bg-[#FAF9F6] py-8 sm:py-12 border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Text -->
        <div class="mb-8 space-y-2">
            <h1 class="text-3xl sm:text-5xl font-black text-slate-950 tracking-tight leading-none">
                Welcome to AfriCrew
            </h1>
            <p class="text-lg sm:text-xl font-bold text-slate-700">
                Find vetted event professionals available where and when you need them.
            </p>
        </div>

        <!-- Main Hero Layout Grid: Search Card Left + Map Container Right -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <!-- LEFT: Search Card Container -->
            <div class="lg:col-span-6 bg-white border border-slate-200/90 rounded-[32px] p-6 sm:p-8 shadow-xl space-y-6 flex flex-col justify-between">
                
                <form id="hero_search_form" onsubmit="onHeroFormSubmit(event)" action="{{ route('crew.index') }}" method="GET" class="space-y-4 m-0">
                    
                    <!-- STEP 1: WHO DO YOU NEED? Parent Category Select (Google Pill Style) -->
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-400 pl-1">
                            WHO DO YOU NEED?
                        </label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none group-focus-within:text-rose-500 transition-colors">⚡</span>
                            <select id="hero_parent_category" name="parent_category" onchange="onParentCategoryChanged()" class="w-full bg-slate-50 hover:bg-white border border-slate-200/90 rounded-full pl-11 pr-10 py-3 text-slate-900 font-extrabold text-sm outline-none focus:border-rose-500 focus:bg-white focus:ring-4 focus:ring-rose-500/10 shadow-xs transition-all appearance-none cursor-pointer">
                                <option value="" selected disabled hidden>Select category...</option>
                                <option value="All">All Parent Categories</option>
                                <option value="Ushers">Ushers</option>
                                <option value="Technical Crew">Technical Crew</option>
                                <option value="Decorations">Decorations</option>
                                <option value="Entertainers">Entertainers</option>
                                <option value="Multi-Media & Brand Activations">Multi-Media & Brand Activations</option>
                            </select>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none">▼</span>
                        </div>
                    </div>

                    <!-- STEP 2: REVEALED SUB-CATEGORY / SPECIALIZATION (Dynamic Google Pill) -->
                    <div id="hero_subcategory_wrapper" class="hidden space-y-1.5 transition-all duration-300 transform scale-95 opacity-0">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-rose-500 pl-1 flex items-center gap-1">
                            <span>🎯 SPECIFIC ROLE / SPECIALIZATION</span>
                        </label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-rose-500 text-base pointer-events-none">🪄</span>
                            <select id="hero_subcategory" name="category" onchange="onSubCategoryChanged()" class="w-full bg-rose-50/50 hover:bg-white border border-rose-200 rounded-full pl-11 pr-10 py-3 text-slate-900 font-extrabold text-sm outline-none focus:border-rose-500 focus:bg-white focus:ring-4 focus:ring-rose-500/10 shadow-xs transition-all appearance-none cursor-pointer">
                                <!-- Populated dynamically based on chosen parent category -->
                            </select>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-rose-400 text-xs pointer-events-none">▼</span>
                        </div>
                    </div>

                    <!-- WHEN IS YOUR EVENT? Date Range Selector -->
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-400 pl-1">
                            WHEN IS YOUR EVENT?
                        </label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none group-focus-within:text-rose-500 transition-colors">📅</span>
                            <input type="text" id="hero_dates" name="dates" value="" onchange="updateSearchPills()" placeholder="Select event date(s)..." class="w-full bg-slate-50 hover:bg-white border border-slate-200/90 rounded-full pl-11 pr-6 py-3 text-slate-900 font-bold text-sm outline-none focus:border-rose-500 focus:bg-white focus:ring-4 focus:ring-rose-500/10 shadow-xs transition-all cursor-pointer">
                        </div>
                    </div>

                    <!-- WHERE IS YOUR EVENT? Registered Location Pills -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between pl-1">
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                                WHERE IS YOUR EVENT?
                            </label>
                            <span class="text-[10px] text-slate-400 font-semibold">Registered Crew Locations</span>
                        </div>
                        
                        <input type="hidden" id="hero_location" name="city" value="Nairobi">
                        
                        <div class="flex flex-wrap gap-2 pt-0.5" id="location-pills-container">
                            <!-- Dynamically populated based on registered crew of searched category -->
                        </div>
                    </div>

                    <!-- Find Available Crew Button (Google Rounded Pill CTA) -->
                    <button type="submit" class="w-full py-3.5 rounded-full bg-gradient-to-r from-rose-500 via-pink-500 to-amber-500 hover:opacity-95 text-white font-black text-sm tracking-wider shadow-lg shadow-pink-500/25 hover:shadow-pink-500/40 transition-all flex items-center justify-center gap-2 uppercase cursor-pointer">
                        <span>FIND AVAILABLE CREW</span>
                        <span>→</span>
                    </button>
                </form>

                <!-- Search Filter Tags Preview Bar -->
                <div class="pt-3 border-t border-slate-100 flex items-center gap-2 flex-wrap text-xs font-bold text-slate-600">
                    <span id="pill-tag-category" class="px-3 py-1 rounded-full bg-slate-100 text-slate-700">All Categories</span>
                    <span id="pill-tag-location" class="px-3 py-1 rounded-full bg-slate-100 text-slate-700">Nairobi</span>
                    <span id="pill-tag-dates" class="px-3 py-1 rounded-full bg-slate-100 text-slate-700">Flexible Dates</span>
                </div>

            </div>

            <!-- RIGHT: Dual-View Interactive Container (Live Map + Animated Crew Results) -->
            <div class="lg:col-span-6 relative rounded-[32px] overflow-hidden border border-slate-200 shadow-xl bg-slate-900 min-h-[440px] lg:min-h-[500px] flex flex-col justify-between">
                
                <!-- Header Tab Switcher Bar -->
                <div class="relative z-20 p-3.5 bg-slate-950/90 backdrop-blur-md border-b border-slate-800 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <button type="button" id="tab-btn-map" onclick="switchHeroRightView('map')" class="px-3.5 py-1.5 rounded-xl text-xs font-black transition-all bg-rose-500 text-white shadow-md flex items-center gap-1.5 cursor-pointer">
                            <span>🗺️ Live Map</span>
                        </button>
                        <button type="button" id="tab-btn-crew" onclick="switchHeroRightView('crew')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center gap-1.5 cursor-pointer">
                            <span>👥 Available Crew</span>
                            <span id="crew-count-pill" class="px-1.5 py-0.5 rounded-md bg-rose-500/20 text-rose-400 font-extrabold text-[10px]">0</span>
                        </button>
                    </div>

                    <div id="map-pinned-count" class="bg-white/95 backdrop-blur-md px-3 py-1 rounded-xl border border-slate-200 text-slate-950 font-black text-[11px] shadow-sm flex items-center gap-1.5 animate-bounce">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span id="map-crew-count-text">Crew Available • Nairobi</span>
                    </div>
                </div>

                <!-- VIEW 1: LEAFLET MAP CANVAS -->
                <div id="hero-view-map" class="relative w-full h-full flex-1 min-h-[360px]">
                    <div id="hero-leaflet-map" class="absolute inset-0 w-full h-full z-0"></div>
                </div>

                <!-- VIEW 2: ANIMATED MATCHING CREW CARDS RESULTS -->
                <div id="hero-view-crew" class="hidden relative w-full h-full flex-1 p-4 overflow-y-auto max-h-[420px] bg-slate-950/95 space-y-3">
                    <!-- Animated matching crew result cards populated via JavaScript -->
                </div>

                <!-- Bottom Floating Location Indicator Card -->
                <div class="relative z-20 p-3.5 m-3 bg-white/95 backdrop-blur-md border border-slate-200 rounded-2xl text-slate-900 shadow-xl flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center text-lg shrink-0">
                            📍
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-slate-900" id="map-indicator-title">Select crew category, date & location to search</div>
                            <div class="text-[10px] font-bold text-slate-500" id="map-indicator-sub">Nairobi • All Categories</div>
                        </div>
                    </div>
                    
                    <a id="hero-catalog-btn" href="{{ route('crew.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-[11px] transition-all text-decoration-none shrink-0">
                        View Catalog →
                    </a>
                </div>

            </div>

        </div>

        <!-- MATCHING CREW CARDS SECTION DIRECTLY UNDER SEARCH (HIDDEN UNTIL USER SEARCHES) -->
        <div id="home-search-results-section" class="hidden mt-10 pt-8 border-t border-slate-200/80 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-rose-500 block mb-0.5">SEARCH RESULTS</span>
                    <h2 id="home-results-title" class="text-xl sm:text-3xl font-black text-slate-950 tracking-tight">
                        Available Crew in Nairobi
                    </h2>
                    <p id="home-results-subtitle" class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                        Select vetted professionals matching your search criteria
                    </p>
                </div>
                <a id="home-view-all-link" href="{{ route('crew.index') }}" class="px-5 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs transition-all shadow-sm border border-slate-800 text-decoration-none shrink-0 self-start sm:self-auto flex items-center gap-1.5">
                    <span>View Full Catalog</span>
                    <span>→</span>
                </a>
            </div>

            <!-- Dynamic Crew Cards Grid under Search Box -->
            <div id="home-crew-cards-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Dynamically rendered via JavaScript based on selected Category and Location -->
            </div>
        </div>

    </div>
</section>


<!-- ========================================================= -->
<!-- SECTION 2: HOW TO HIRE ("FROM SEARCH TO EVENT-READY") -->
<!-- ========================================================= -->
<section class="py-20 bg-[#FAF9F6] border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-xl mx-auto mb-16 space-y-2">
            <h2 class="text-3xl sm:text-4xl font-black text-slate-950 tracking-tight">
                From Search to Event-Ready
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">
                Hiring your event crew shouldn't be complicated.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
            @foreach([
                ['01', '🔍', 'Tell Us What You Need', 'Choose crew type, date and event location.'],
                ['02', '🛍️', 'Discover Available Crew', 'Browse vetted professionals available for your event.'],
                ['03', '👥', 'Select Your Crew', 'Review profiles and choose the people who fit your event.'],
                ['04', '📋', 'Confirm & Get Event-Ready', 'Complete your booking and AfriCrew handles the rest.'],
            ] as $step)
                <div class="bg-white border border-slate-200/90 rounded-3xl p-6 text-center space-y-4 shadow-sm hover:shadow-lg transition-all relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-700 font-black text-xl flex items-center justify-center mx-auto shadow-2xs">
                        {{ $step[1] }}
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-rose-500 block">{{ $step[0] }}</span>
                    <h3 class="text-base font-extrabold text-slate-950">{{ $step[2] }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-normal">{{ $step[3] }}</p>
                </div>
            @endforeach
        </div>

    </div>
</section>


<!-- ========================================================= -->
<!-- SECTION 4: CALL TO ACTION BANNER (PDF PAGE 2 SPECIFICATION) -->
<!-- ========================================================= -->
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-slate-950 text-white rounded-[32px] p-8 sm:p-14 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8 border border-slate-800">
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 max-w-xl space-y-3">
            <h3 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight">
                Your Perfect Event Crew Is Closer Than You Think.
            </h3>
            <p class="text-slate-400 text-xs sm:text-sm leading-relaxed font-normal">
                Tell us what you need and discover vetted professionals ready for your event.
            </p>
        </div>

        <div class="relative z-10 flex flex-wrap gap-4 shrink-0">
            <a href="{{ route('hire.create') }}" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-rose-500 via-pink-500 to-amber-500 text-white font-black text-xs sm:text-sm tracking-wider shadow-lg shadow-pink-500/30 hover:scale-105 transition-all text-decoration-none uppercase">
                Find My Crew
            </a>
            <a href="{{ route('crew.index') }}" class="px-7 py-4 rounded-2xl bg-slate-900 border border-slate-700 text-white font-bold text-xs sm:text-sm hover:bg-slate-800 transition-all text-decoration-none">
                Talk to AfriCrew
            </a>
        </div>
    </div>
</section>

<!-- Leaflet Map & Flatpickr Date Picker Script Handlers -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
let leafletMap = null;
let leafletMarker = null;

const categoryLocationsMap = @json($categoryLocations ?? []);
const allApprovedCrew = @json($allApprovedCrewList ?? []);

const locationCoordinates = {
    'Nairobi': [-1.286389, 36.817223],
    'Mombasa': [-4.043477, 39.668206],
    'Kisumu': [-0.091702, 34.767956],
    'Nakuru': [-0.303099, 36.080025],
    'Eldoret': [0.514277, 35.269779]
};

const categoryHierarchy = {
    'Ushers': [
        'All Ushers Specializations',
        'Protocol & VIP Ushers',
        'Registration Desk Staff',
        'Guest Relations & Ticketing',
        'Door & Seat Coordinators'
    ],
    'Technical Crew': [
        'All Technical Specializations',
        'Sound Engineers / Audio Technicians',
        'Lighting Technicians & Operators',
        'Stage Managers / Hands',
        'Riggers & AV Crew'
    ],
    'Decorations': [
        'All Decoration Specializations',
        'Floral & Stage Designers',
        'Balloon & Backdrop Stylists',
        'Lighting Decorators',
        'Set Builders & Prop Setup'
    ],
    'Entertainers': [
        'All Entertainer Specializations',
        'MCs & Stage Hosts',
        'DJs & Music Curators',
        'Live Bands & Vocalists',
        'Dancers & Performance Artists'
    ],
    'Multi-Media & Brand Activations': [
        'All Media & Activation Specializations',
        'Photographers',
        'Videographers & Drone Pilots',
        'Brand Ambassadors & Promo Models',
        'Content Creators & Livestreaming Crew'
    ]
};

document.addEventListener('DOMContentLoaded', () => {
    initLeafletMap();
    onParentCategoryChanged();

    if (typeof flatpickr !== 'undefined') {
        flatpickr('#hero_dates', {
            mode: 'range',
            dateFormat: 'd M Y',
            minDate: 'today',
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const diffMs = Math.abs(selectedDates[1].getTime() - selectedDates[0].getTime());
                    const daysCount = Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1;
                    const startStr = instance.formatDate(selectedDates[0], 'd M Y');
                    const endStr = instance.formatDate(selectedDates[1], 'd M Y');
                    instance.input.value = `${startStr} - ${endStr} (${daysCount} ${daysCount === 1 ? 'Day' : 'Days'})`;
                } else if (selectedDates.length === 1) {
                    const startStr = instance.formatDate(selectedDates[0], 'd M Y');
                    instance.input.value = `${startStr} (1 Day)`;
                }
                updateSearchPills();
            }
        });
    }
});

function initLeafletMap() {
    const container = document.getElementById('hero-leaflet-map');
    if (!container) return;

    const initialCoords = locationCoordinates['Nairobi'];
    leafletMap = L.map('hero-leaflet-map', {
        center: initialCoords,
        zoom: 12,
        zoomControl: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(leafletMap);

    const customIcon = L.divIcon({
        className: 'custom-map-pin',
        html: `<div style="background-color:#FF2D55; width:24px; height:24px; border-radius:50%; border:3px solid white; box-shadow:0 10px 20px rgba(0,0,0,0.3); transform: scale(1.2);"></div>`,
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });

    leafletMarker = L.marker(initialCoords, { icon: customIcon }).addTo(leafletMap);

    leafletMap.on('click', function(e) {
        const clickedLat = e.latlng.lat;
        const clickedLng = e.latlng.lng;
        let closestCity = 'Nairobi';
        let minDistance = Infinity;

        for (const [cityName, coords] of Object.entries(locationCoordinates)) {
            const dist = Math.hypot(coords[0] - clickedLat, coords[1] - clickedLng);
            if (dist < minDistance) {
                minDistance = dist;
                closestCity = cityName;
            }
        }

        selectLocationPill(closestCity, '- Selected Area');
    });
}

function getEffectiveCategory() {
    const parentVal = document.getElementById('hero_parent_category')?.value || 'All';
    const subWrapper = document.getElementById('hero_subcategory_wrapper');
    const subVal = document.getElementById('hero_subcategory')?.value;

    if (subWrapper && !subWrapper.classList.contains('hidden') && subVal && !subVal.startsWith('All ')) {
        return subVal;
    }
    return parentVal;
}

function onParentCategoryChanged() {
    const parentSelect = document.getElementById('hero_parent_category');
    const subWrapper = document.getElementById('hero_subcategory_wrapper');
    const subSelect = document.getElementById('hero_subcategory');

    const parentVal = parentSelect?.value || 'All';

    if (parentVal && parentVal !== 'All' && categoryHierarchy[parentVal]) {
        if (subSelect) {
            subSelect.innerHTML = '';
            categoryHierarchy[parentVal].forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub;
                opt.textContent = sub;
                subSelect.appendChild(opt);
            });
        }
        if (subWrapper) {
            subWrapper.classList.remove('hidden');
            setTimeout(() => {
                subWrapper.classList.remove('opacity-0', 'scale-95');
                subWrapper.classList.add('opacity-100', 'scale-100');
            }, 10);
        }
    } else {
        if (subWrapper) {
            subWrapper.classList.remove('opacity-100', 'scale-100');
            subWrapper.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                subWrapper.classList.add('hidden');
            }, 200);
        }
        if (subSelect) subSelect.innerHTML = '';
    }

    const effectiveCat = getEffectiveCategory();
    renderLocationPills(parentVal);
    updateSearchPills();
    const city = document.getElementById('hero_location')?.value || 'Nairobi';
    renderMatchingCrewCards(effectiveCat, city);
}

function onSubCategoryChanged() {
    const effectiveCat = getEffectiveCategory();
    updateSearchPills();
    const city = document.getElementById('hero_location')?.value || 'Nairobi';
    renderMatchingCrewCards(effectiveCat, city);
}

function renderLocationPills(selectedCategory) {
    const container = document.getElementById('location-pills-container');
    if (!container) return;

    let availableCities = categoryLocationsMap[selectedCategory];

    if (!availableCities) {
        const matchingKey = Object.keys(categoryLocationsMap).find(k => k.toLowerCase() === selectedCategory.toLowerCase());
        if (matchingKey) {
            availableCities = categoryLocationsMap[matchingKey];
        }
    }

    if (!availableCities && selectedCategory === 'All') {
        availableCities = categoryLocationsMap['All'] || [];
    }

    container.innerHTML = '';

    if (!availableCities || availableCities.length === 0) {
        container.innerHTML = `
            <div class="w-full py-2.5 px-3.5 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-800 text-xs font-extrabold flex items-center gap-2">
                <span>📍</span>
                <span>No registered crew available for "${selectedCategory}" yet.</span>
            </div>
        `;
        const heroLoc = document.getElementById('hero_location');
        if (heroLoc) heroLoc.value = '';
        updateSearchPills();
        renderMatchingCrewCards(selectedCategory, '');
        return;
    }

    const currentSelectedCity = document.getElementById('hero_location')?.value;
    let cityToSelect = availableCities.includes(currentSelectedCity) ? currentSelectedCity : availableCities[0];

    availableCities.forEach((city, index) => {
        const isSelected = (city === cityToSelect);
        const sub = (index === 0) ? '- Near you' : '';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('data-city', city);
        btn.onclick = () => selectLocationPill(city, sub);

        if (isSelected) {
            btn.className = 'location-pill active-pill px-4 py-2 rounded-2xl text-xs font-black transition-all bg-rose-500 text-white shadow-md';
        } else {
            btn.className = 'location-pill px-4 py-2 rounded-2xl text-xs font-bold transition-all bg-slate-100 text-slate-700 hover:bg-slate-200';
        }

        btn.textContent = sub ? `${city} ${sub}` : city;
        container.appendChild(btn);
    });

    selectLocationPill(cityToSelect, (availableCities[0] === cityToSelect ? '- Near you' : ''));
}

function selectLocationPill(city, subtitle) {
    const heroLoc = document.getElementById('hero_location');
    if (heroLoc) heroLoc.value = city;

    document.querySelectorAll('.location-pill').forEach(btn => {
        const btnCity = btn.getAttribute('data-city') || btn.textContent.trim().split(' ')[0];
        if (btnCity === city) {
            btn.className = 'location-pill active-pill px-4 py-2 rounded-2xl text-xs font-black transition-all bg-rose-500 text-white shadow-md';
        } else {
            btn.className = 'location-pill px-4 py-2 rounded-2xl text-xs font-bold transition-all bg-slate-100 text-slate-700 hover:bg-slate-200';
        }
    });

    updateSearchPills();

    if (leafletMap && city && locationCoordinates[city]) {
        const coords = locationCoordinates[city];
        leafletMap.flyTo(coords, 12, { duration: 1.5 });
        if (leafletMarker) {
            leafletMarker.setLatLng(coords);
        }
    }

    const cat = getEffectiveCategory();
    renderMatchingCrewCards(cat, city);
}

function switchHeroRightView(view) {
    const mapView = document.getElementById('hero-view-map');
    const crewView = document.getElementById('hero-view-crew');
    const btnMap = document.getElementById('tab-btn-map');
    const btnCrew = document.getElementById('tab-btn-crew');

    if (view === 'crew') {
        mapView?.classList.add('hidden');
        crewView?.classList.remove('hidden');
        if (btnCrew) {
            btnCrew.className = 'px-3.5 py-1.5 rounded-xl text-xs font-black transition-all bg-rose-500 text-white shadow-md flex items-center gap-1.5 cursor-pointer';
        }
        if (btnMap) {
            btnMap.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center gap-1.5 cursor-pointer';
        }
    } else {
        crewView?.classList.add('hidden');
        mapView?.classList.remove('hidden');
        if (btnMap) {
            btnMap.className = 'px-3.5 py-1.5 rounded-xl text-xs font-black transition-all bg-rose-500 text-white shadow-md flex items-center gap-1.5 cursor-pointer';
        }
        if (btnCrew) {
            btnCrew.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center gap-1.5 cursor-pointer';
        }
        if (leafletMap) {
            setTimeout(() => leafletMap.invalidateSize(), 150);
        }
    }
}

function renderMatchingCrewCards(cat, city) {
    const container = document.getElementById('hero-view-crew');
    const homeGrid = document.getElementById('home-crew-cards-grid');
    const homeTitle = document.getElementById('home-results-title');
    const homeSubtitle = document.getElementById('home-results-subtitle');
    const homeViewAll = document.getElementById('home-view-all-link');

    let matching = allApprovedCrew.filter(c => {
        let matchCat = true;
        if (cat && cat !== 'All' && !cat.startsWith('All ')) {
            const crewCat = (c.category || '').toLowerCase();
            const crewSkills = (c.skills || '').toLowerCase();
            const searchCat = cat.toLowerCase();

            matchCat = crewCat === searchCat || crewCat.includes(searchCat) || searchCat.includes(crewCat) || crewSkills.includes(searchCat) ||
                (searchCat.includes('usher') && (crewCat.includes('usher') || crewSkills.includes('usher'))) ||
                (searchCat.includes('protocol') && (crewCat.includes('protocol') || crewSkills.includes('protocol') || crewCat.includes('usher'))) ||
                (searchCat.includes('host') && (crewCat.includes('host') || crewSkills.includes('host'))) ||
                (searchCat.includes('tech') && (crewCat.includes('tech') || crewCat.includes('sound') || crewCat.includes('av') || crewSkills.includes('sound'))) ||
                (searchCat.includes('sound') && (crewCat.includes('sound') || crewCat.includes('audio') || crewSkills.includes('sound'))) ||
                (searchCat.includes('lighting') && (crewCat.includes('light') || crewSkills.includes('light'))) ||
                (searchCat.includes('decor') && (crewCat.includes('decor') || crewCat.includes('design') || crewSkills.includes('decor'))) ||
                (searchCat.includes('entertain') && (crewCat.includes('entertain') || crewCat.includes('mc') || crewCat.includes('dj'))) ||
                (searchCat.includes('mc') && (crewCat.includes('mc') || crewCat.includes('anchor') || crewSkills.includes('mc'))) ||
                (searchCat.includes('dj') && (crewCat.includes('dj') || crewSkills.includes('dj'))) ||
                ((searchCat.includes('media') || searchCat.includes('photo') || searchCat.includes('video') || searchCat.includes('brand')) && (crewCat.includes('media') || crewCat.includes('photo') || crewCat.includes('video') || crewCat.includes('brand') || crewCat.includes('ambassador')));
        }

        let matchCity = true;
        if (city) {
            matchCity = (c.city || '').toLowerCase().trim() === city.toLowerCase().trim();
        }

        return matchCat && matchCity;
    });

    const countPill = document.getElementById('crew-count-pill');
    if (countPill) countPill.textContent = matching.length;

    const countText = document.getElementById('map-crew-count-text');
    if (countText) countText.textContent = `${matching.length} Crew Available • ${city || 'Kenya'}`;

    const catalogBtn = document.getElementById('hero-catalog-btn');
    if (catalogBtn) {
        catalogBtn.href = `/crew?category=${encodeURIComponent(cat || '')}&city=${encodeURIComponent(city || '')}`;
    }

    if (homeTitle) {
        homeTitle.textContent = `Available Crew ${city ? 'in ' + city : ''} ${cat && cat !== 'All' ? '• ' + cat : ''}`;
    }
    if (homeSubtitle) {
        homeSubtitle.textContent = matching.length > 0
            ? `${matching.length} vetted ${cat && cat !== 'All' ? cat : 'professional(s)'} ready for your event`
            : `Showing search results for ${cat || 'All Categories'} in ${city || 'all locations'}`;
    }
    if (homeViewAll) {
        homeViewAll.href = `/crew?category=${encodeURIComponent(cat || '')}&city=${encodeURIComponent(city || '')}`;
    }

    // 1. Populate right map tab container
    if (container) {
        container.innerHTML = '';
        if (matching.length === 0) {
            container.innerHTML = `
                <div class="py-12 px-6 text-center rounded-3xl bg-slate-900 border border-slate-800 text-white space-y-3 my-auto">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/20 text-rose-400 font-black text-2xl flex items-center justify-center mx-auto shadow-inner">
                        ⚡
                    </div>
                    <h4 class="text-base font-black text-white">No Registered Crew in ${city || 'this location'}</h4>
                    <p class="text-xs text-slate-400 max-w-xs mx-auto font-medium">Try selecting another location pill or category to view available vetted staff.</p>
                </div>
            `;
        } else {
            matching.forEach((p, idx) => {
                const delay = (idx * 0.07).toFixed(2);
                const avatar = p.profile_photo_url ? `<img src="${p.profile_photo_url}" class="w-12 h-12 rounded-2xl object-cover border-2 border-slate-100 shadow-sm">` : `<div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-lg flex items-center justify-center border-2 border-slate-100 shadow-sm">${p.full_name.charAt(0).toUpperCase()}</div>`;
                const ratingBadgeHtml = (p.reviews_count > 0 && parseFloat(p.average_rating) > 0) ? `
                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/80">
                        <span>★</span>
                        <span>${p.average_rating}</span>
                    </span>
                ` : '';
                const cardHtml = `
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-md hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group animate-crew-card" style="animation-delay: ${delay}s">
                        <div class="flex items-center gap-3.5">
                            <div class="relative shrink-0">
                                ${avatar}
                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white animate-pulse"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-xs font-black text-slate-950 group-hover:text-rose-500 transition-colors">${p.full_name}</h4>
                                    ${ratingBadgeHtml}
                                </div>
                                <div class="text-[11px] font-bold text-slate-500 mt-0.5">
                                    ${p.category} • <span class="text-slate-900 font-extrabold">${p.city}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-semibold mt-0.5 flex items-center gap-2">
                                    <span>${p.experience_years ? p.experience_years + ' yrs exp' : 'Vetted Crew'}</span>
                                    ${p.completed_gigs_count ? '<span>•</span><span>' + p.completed_gigs_count + '+ events</span>' : ''}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                            <a href="/crew/${p.id}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-[11px] transition-all text-decoration-none">
                                Profile
                            </a>
                            <button type="button" onclick="addCrewToTeamBasket({id:${p.id}, full_name:'${p.full_name.replace(/'/g, "\\'")}', category:'${p.category}', profile_photo_url:'${p.profile_photo_url || ''}'})" class="px-4 py-2 rounded-xl bg-gradient-to-r from-rose-500 via-pink-500 to-amber-500 hover:opacity-95 text-white font-black text-[11px] shadow-sm shadow-rose-500/20 transition-all flex items-center gap-1 cursor-pointer">
                                <span>+ Add</span>
                            </button>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', cardHtml);
            });
        }
    }

    // 2. Populate Home Grid directly under hero search form
    if (homeGrid) {
        homeGrid.innerHTML = '';
        if (matching.length === 0) {
            homeGrid.innerHTML = `
                <div class="col-span-full py-12 px-6 text-center rounded-3xl bg-white border border-slate-200/90 text-slate-900 space-y-3 shadow-sm">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 font-black text-2xl flex items-center justify-center mx-auto shadow-inner">
                        🔍
                    </div>
                    <h4 class="text-base font-black text-slate-950">No Approved Crew Found for "${cat || 'Selected Category'}" in ${city || 'this location'}</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto font-medium">Currently no registered and approved crew members match this location and role.</p>
                </div>
            `;
        } else {
            matching.slice(0, 6).forEach((p, idx) => {
                const avatarHtml = p.profile_photo_url
                    ? `<img src="${p.profile_photo_url}" alt="${p.full_name}" class="w-16 h-16 rounded-2xl object-cover border-4 border-white shadow-lg shrink-0 bg-slate-100">`
                    : `<div class="w-16 h-16 rounded-2xl border-4 border-white bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-xl flex items-center justify-center shadow-lg shrink-0">${p.full_name.charAt(0).toUpperCase()}</div>`;
                const handle = p.username ? ('@' + p.username) : ('@' + p.full_name.toLowerCase().replace(/\s+/g, ''));
                const gridRatingBadgeHtml = (p.reviews_count > 0 && parseFloat(p.average_rating) > 0) ? `
                    <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-xl text-[11px] font-black text-slate-900 shadow-xs flex items-center gap-1">
                        <span class="text-amber-500">★</span>
                        <span>${p.average_rating}</span>
                    </div>
                ` : '';

                const cardHtml = `
                    <div class="bg-white border border-slate-200/90 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col justify-between group">
                        <div>
                            <div class="relative h-32 w-full bg-slate-900 overflow-hidden">
                                <img src="/images/hero_event_ushers.jpg" alt="${p.full_name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-85">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 to-transparent"></div>
                                ${gridRatingBadgeHtml}
                            </div>

                            <div class="px-4 relative -mt-8 mb-2 flex items-end justify-between">
                                ${avatarHtml}
                                <span class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Available</span>
                                </span>
                            </div>

                            <div class="px-4 space-y-1">
                                <h3 class="text-sm font-black text-slate-950 group-hover:text-rose-500 transition-colors truncate">
                                    ${handle}
                                </h3>
                                <p class="text-xs text-slate-500 font-semibold truncate">
                                    ${p.category} • ${p.city}
                                </p>
                                <div class="flex items-center gap-2 pt-2 text-[11px] font-bold text-slate-400">
                                    <span>${p.experience_years ? p.experience_years + ' yrs exp' : 'Vetted Crew'}</span>
                                    ${p.completed_gigs_count ? '<span>•</span><span>' + p.completed_gigs_count + '+ events</span>' : ''}
                                </div>
                            </div>
                        </div>

                        <div class="p-4 pt-3 space-y-2">
                            <a href="/crew/${p.id}" class="w-full text-center py-2 px-3 rounded-xl border border-slate-300 text-slate-800 font-extrabold text-xs hover:bg-slate-900 hover:text-white transition-all shadow-2xs text-decoration-none block">
                                View Profile
                            </a>
                            <button type="button" onclick="addCrewToTeamBasket({id:${p.id}, full_name:'${p.full_name.replace(/'/g, "\\'")}', category:'${p.category}', profile_photo_url:'${p.profile_photo_url || ''}'})" class="w-full py-2 px-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 font-extrabold text-xs hover:bg-amber-500 hover:text-slate-950 transition-all flex items-center justify-center gap-1">
                                <span>⊕ Add to Team</span>
                            </button>
                        </div>
                    </div>
                `;
                homeGrid.insertAdjacentHTML('beforeend', cardHtml);
            });
        }
    }
}

function onHeroFormSubmit(e) {
    e.preventDefault();

    const category = getEffectiveCategory();
    const selectedCity = document.getElementById('hero_location')?.value || 'Nairobi';

    renderMatchingCrewCards(category, selectedCity);

    const searchResultsSection = document.getElementById('home-search-results-section');
    if (searchResultsSection) {
        searchResultsSection.classList.remove('hidden');
        searchResultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function updateSearchPills() {
    const parentVal = document.getElementById('hero_parent_category')?.value || 'All Categories';
    const effectiveCat = getEffectiveCategory();
    const loc = document.getElementById('hero_location')?.value || 'Nairobi';
    const dates = document.getElementById('hero_dates')?.value;

    const displayCat = effectiveCat !== 'All' ? effectiveCat : (parentVal !== 'All' ? parentVal : 'All Categories');

    if (document.getElementById('pill-tag-category')) document.getElementById('pill-tag-category').textContent = displayCat;
    if (document.getElementById('pill-tag-location')) document.getElementById('pill-tag-location').textContent = loc || 'All Locations';
    if (document.getElementById('pill-tag-dates')) document.getElementById('pill-tag-dates').textContent = dates ? dates : 'Flexible Dates';

    if (document.getElementById('map-indicator-sub')) {
        document.getElementById('map-indicator-sub').textContent = `${loc || 'Kenya'} • ${displayCat}`;
    }

    const catalogBtn = document.getElementById('hero-catalog-btn');
    if (catalogBtn) {
        catalogBtn.href = `/crew?category=${encodeURIComponent(displayCat)}&dates=${encodeURIComponent(dates || '')}&city=${encodeURIComponent(loc)}`;
    }
}
</script>


@endsection
