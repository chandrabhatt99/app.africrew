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
    <span class="text-slate-800">
        ✨ <strong>AfriCrew is even better on mobile!</strong> Download our app on iOS & Android to book crew faster & manage events on the go.
    </span>
</div>

<!-- ========================================================= -->
<!-- HERO SECTION (MATCHING SCREENSHOT MOCKUP SPECIFICATION) -->
<!-- ========================================================= -->
<section class="relative overflow-hidden bg-[#FAF9F6] py-8 sm:py-12 border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Title -->
        <div class="mb-6 space-y-1">
            <h1 class="text-3xl sm:text-5xl font-black text-slate-950 tracking-tight leading-none">
                Welcome to AfriCrew
            </h1>
            <p class="text-base sm:text-lg font-bold text-slate-600">
                Find vetted event professionals available where and when you need them.
            </p>
        </div>

        <!-- Main Unified Card Layout -->
        <div class="bg-white border border-slate-200/90 rounded-[32px] p-4 sm:p-6 lg:p-8 shadow-xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-stretch">
                
                <!-- LEFT: Interactive Map with Integrated Top & Bottom Search Controls -->
                <div class="lg:col-span-7 relative rounded-[28px] overflow-hidden border border-slate-200/90 shadow-lg bg-slate-950 min-h-[460px] lg:min-h-[500px] flex flex-col justify-between">
                    
                    <!-- TOP MAP OVERLAY: Crew Category (Top Left) & Event Date (Top Right) Selection Buttons -->
                    <div class="absolute top-4 left-4 right-4 z-20 flex items-center justify-between gap-2 pointer-events-auto">
                        
                        <!-- 1. CREW CATEGORY SELECTOR BUTTON (TOP LEFT) -->
                        <div class="relative">
                            <button type="button" id="map-category-btn" onclick="toggleCategoryMenu()" class="px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-2xl bg-slate-900/90 hover:bg-slate-900 text-white border border-slate-700/80 backdrop-blur-md shadow-xl text-[11px] sm:text-xs font-black flex items-center gap-2 cursor-pointer transition-all hover:border-amber-400">
                                <span class="text-amber-400 text-xs sm:text-sm">👥</span>
                                <span id="map-category-label">CREW CATEGORY</span>
                                <span class="text-[10px] text-slate-400 ml-0.5">▼</span>
                            </button>

                            <!-- Category Dropdown Menu (Populated dynamically from Backend) -->
                            <div id="map-category-menu" class="hidden absolute top-full left-0 mt-2 w-72 bg-slate-950/95 backdrop-blur-xl border border-slate-800 rounded-2xl p-2.5 shadow-2xl z-50 text-white space-y-1 max-h-72 overflow-y-auto">
                                <div class="px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                                    <span>Backend Categories</span>
                                    <span class="text-rose-400 font-bold">Step 1</span>
                                </div>
                                @if(isset($backendCategoriesList) && count($backendCategoriesList) > 0)
                                    @foreach($backendCategoriesList as $catName)
                                        <button type="button" onclick="selectCategory('{{ addslashes($catName) }}')" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold hover:bg-rose-500 hover:text-white transition-all flex items-center justify-between group">
                                            <span class="flex items-center gap-2">
                                                <span>⚡</span>
                                                <span>{{ $catName }}</span>
                                            </span>
                                            <span class="text-[10px] opacity-70 group-hover:opacity-100">Select</span>
                                        </button>
                                    @endforeach
                                @else
                                    @foreach(['Ushers', 'Host', 'Anchor / MC', 'DJ / Sound Engineer', 'Security Staff', 'Photographer / Videographer'] as $catName)
                                        <button type="button" onclick="selectCategory('{{ addslashes($catName) }}')" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold hover:bg-rose-500 hover:text-white transition-all flex items-center justify-between group">
                                            <span class="flex items-center gap-2">
                                                <span>⚡</span>
                                                <span>{{ $catName }}</span>
                                            </span>
                                            <span class="text-[10px] opacity-70 group-hover:opacity-100">Select</span>
                                        </button>
                                    @endforeach
                                @endif
                                <button type="button" onclick="selectCategory('All')" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold hover:bg-rose-500 hover:text-white transition-all flex items-center justify-between border-t border-slate-800 mt-1 pt-2">
                                    <span class="flex items-center gap-2"><span>✨</span><span>All Categories</span></span>
                                </button>
                            </div>
                        </div>

                        <!-- 2. EVENT DATE SELECTOR BUTTON (TOP RIGHT) -->
                        <div class="relative">
                            <button type="button" id="map-date-btn" onclick="openDatePicker()" class="px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-2xl bg-slate-900/90 hover:bg-slate-900 text-white border border-slate-700/80 backdrop-blur-md shadow-xl text-[11px] sm:text-xs font-black flex items-center gap-2 cursor-pointer transition-all hover:border-amber-400">
                                <span class="text-amber-400 text-xs sm:text-sm">📅</span>
                                <span id="map-date-label">EVENT DATE</span>
                                <span class="text-[10px] text-slate-400 ml-0.5">▼</span>
                            </button>
                            <input type="text" id="hero_dates_input" class="sr-only" placeholder="Select dates">
                        </div>

                    </div>

                    <!-- LEAFLET MAP CANVAS -->
                    <div id="hero-leaflet-map" class="absolute inset-0 w-full h-full z-0"></div>

                    <!-- BOTTOM MAP OVERLAY: Location Select Pill & Search Button -->
                    <div class="absolute bottom-4 left-4 right-4 z-20 pointer-events-auto">
                        <div class="p-2.5 bg-slate-950/90 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl flex items-center justify-between gap-3">
                            
                            <!-- Location Dropdown Pill -->
                            <div class="relative flex-1">
                                <button type="button" id="map-location-btn" onclick="toggleLocationMenu()" class="w-full text-left px-3.5 py-2.5 rounded-xl bg-slate-900 text-slate-400 border border-slate-800 text-xs font-bold flex items-center gap-3 transition-all cursor-not-allowed opacity-80">
                                    <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-500 flex items-center justify-center text-sm shrink-0" id="location-icon-box">
                                        🔒
                                    </div>
                                    <div class="truncate">
                                        <div id="map-location-title" class="font-extrabold text-xs text-white truncate">Select Event Location</div>
                                        <div id="map-location-sub" class="text-[10px] text-slate-400 truncate">Select Category & Date first</div>
                                    </div>
                                </button>

                                <!-- Popular Cities in Kenya Dropdown -->
                                <div id="map-location-menu" class="hidden absolute bottom-full left-0 mb-2 w-80 bg-slate-950/95 backdrop-blur-xl border border-slate-800 rounded-2xl p-3 shadow-2xl z-50 text-white space-y-2 max-h-80 overflow-y-auto">
                                    
                                    <!-- Auto-Detect Geolocation Button -->
                                    <button type="button" onclick="detectUserLocation()" class="w-full text-left px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-rose-500/20 to-amber-500/20 border border-rose-500/40 text-rose-300 font-extrabold text-xs hover:bg-rose-500 hover:text-white transition-all flex items-center justify-between group">
                                        <span class="flex items-center gap-2">
                                            <span class="animate-pulse">📍</span>
                                            <span id="location-detect-text">Detect My Location (GPS)</span>
                                        </span>
                                        <span class="text-[10px] bg-rose-500/40 px-2 py-0.5 rounded-md text-white font-extrabold">Auto</span>
                                    </button>

                                    <div class="px-2 pt-1 border-t border-slate-800 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                                        <span>Popular Cities in Kenya</span>
                                        <span>Available Crew</span>
                                    </div>

                                    <div id="kenya-cities-list" class="space-y-1">
                                        <!-- Rendered dynamically -->
                                    </div>
                                </div>
                            </div>

                            <!-- SEARCH CTA Button -->
                            <button type="button" id="hero-search-submit-btn" onclick="onHeroFormSubmit(event)" disabled class="px-6 py-3 rounded-xl bg-slate-800 text-slate-500 font-black text-xs uppercase tracking-wider opacity-50 cursor-not-allowed shadow-none transition-all shrink-0 flex items-center gap-2">
                                <span id="search-btn-label">SEARCH 🔒</span>
                            </button>

                        </div>
                    </div>

                </div>

                <!-- RIGHT: Dynamic State Text & Stats Counter Panel -->
                <div class="lg:col-span-5 bg-gradient-to-br from-rose-50/40 via-amber-50/20 to-slate-50 border border-slate-200/80 rounded-[28px] p-6 sm:p-8 flex flex-col justify-between space-y-6">
                    
                    <div class="space-y-4">
                        <!-- Dynamic State Pill Tag -->
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white border border-slate-200 shadow-2xs text-[11px] font-extrabold text-slate-800" id="hero-state-badge">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            <span id="hero-state-badge-text">PLAN YOUR BIG DAY WITH EASE</span>
                        </div>

                        <!-- Dynamic Title -->
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-950 tracking-tight leading-[1.1]" id="hero-state-title">
                            Find Your Perfect <span class="bg-gradient-to-r from-rose-500 via-pink-500 to-amber-500 bg-clip-text text-transparent">Event Crew</span>
                        </h2>

                        <!-- Dynamic Description -->
                        <p class="text-sm sm:text-base font-medium text-slate-600 leading-relaxed" id="hero-state-sub">
                            Tell us who you need and when — we'll match you with vetted, available professionals near your event.
                        </p>
                    </div>

                    <!-- Dynamic Steps Guidance Indicator -->
                    <div class="p-4 rounded-2xl bg-white/80 border border-slate-200/80 backdrop-blur-xs space-y-2" id="hero-steps-box">
                        <div class="text-[11px] font-black uppercase tracking-wider text-slate-400 flex items-center justify-between">
                            <span>Search Requirement Checklist</span>
                            <span id="hero-steps-progress" class="text-rose-500 font-extrabold">0/3 Completed</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-[11px] font-extrabold">
                            <div id="step-pill-1" class="py-2 px-2 rounded-xl bg-slate-100 text-slate-400 text-center transition-all border border-slate-200">
                                1. Category
                            </div>
                            <div id="step-pill-2" class="py-2 px-2 rounded-xl bg-slate-100 text-slate-400 text-center transition-all border border-slate-200">
                                2. Date
                            </div>
                            <div id="step-pill-3" class="py-2 px-2 rounded-xl bg-slate-100 text-slate-400 text-center transition-all border border-slate-200">
                                3. Location
                            </div>
                        </div>
                    </div>

                    <!-- Stats Counters Row -->
                    <div class="pt-6 border-t border-slate-200/80 grid grid-cols-3 gap-3">
                        <div>
                            <div class="text-xl sm:text-2xl font-black text-slate-950" id="hero-stat-1-val">42+</div>
                            <div class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-slate-400" id="hero-stat-1-lbl">CREW IN NAIROBI</div>
                        </div>
                        <div class="border-l border-slate-200/80 pl-3">
                            <div class="text-xl sm:text-2xl font-black text-slate-950 flex items-center gap-1">
                                <span>4.9</span>
                                <span class="text-amber-500 text-base">★</span>
                            </div>
                            <div class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-slate-400">AVG. RATING</div>
                        </div>
                        <div class="border-l border-slate-200/80 pl-3">
                            <div class="text-xl sm:text-2xl font-black text-slate-950">2hr</div>
                            <div class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-slate-400">AVG. RESPONSE</div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- SEARCH RESULTS CONTAINER (REVEALED WHEN SEARCH IS EXECUTED) -->
        <div id="home-search-results-section" class="hidden mt-10 pt-8 border-t border-slate-200/80 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-rose-500 block mb-0.5">MATCHING CREW FOUND</span>
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

            <!-- Dynamic Crew Cards Grid -->
            <div id="home-crew-cards-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Populated via JavaScript -->
            </div>
        </div>

    </div>
</section>


<!-- ========================================================= -->
<!-- SECTION 2: HOW TO HIRE ("FROM SEARCH TO EVENT-READY") -->
<!-- ========================================================= -->
<section id="how-it-works" class="py-20 bg-[#FAF9F6] border-b border-slate-200/80 scroll-mt-20">
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
<!-- SECTION 4: CALL TO ACTION BANNER -->
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

<!-- Leaflet Map & Flatpickr Scripts -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
let leafletMap = null;
let leafletMarkersGroup = null;
let flatpickrInstance = null;

const allApprovedCrew = @json($allApprovedCrewList ?? []);

// Popular Kenyan Cities with Coordinates
const kenyaCities = [
    { name: 'Nairobi', coords: [-1.286389, 36.817223], region: 'Capital' },
    { name: 'Mombasa', coords: [-4.043477, 39.668206], region: 'Coast' },
    { name: 'Kisumu', coords: [-0.091702, 34.767956], region: 'Nyanza' },
    { name: 'Nakuru', coords: [-0.303099, 36.080025], region: 'Rift Valley' },
    { name: 'Eldoret', coords: [0.514277, 35.269779], region: 'North Rift' },
    { name: 'Naivasha', coords: [-0.7167, 36.4333], region: 'Rift Valley' },
    { name: 'Diani', coords: [-4.2796, 39.5947], region: 'South Coast' },
    { name: 'Malindi', coords: [-3.2192, 40.1169], region: 'North Coast' },
    { name: 'Thika', coords: [-1.0333, 37.0667], region: 'Central' },
    { name: 'Machakos', coords: [-1.5167, 37.2667], region: 'Eastern' }
];

// Interactive Form State Variables
let selectedCategory = '';
let selectedDates = '';
let selectedCity = '';

document.addEventListener('DOMContentLoaded', () => {
    initLeafletMap();
    initDatePicker();
    renderCitiesDropdown();
    updateStateUI();
});

// Initialize Leaflet Map
function initLeafletMap() {
    const container = document.getElementById('hero-leaflet-map');
    if (!container) return;

    const initialCoords = kenyaCities[0].coords; // Default Nairobi
    leafletMap = L.map('hero-leaflet-map', {
        center: initialCoords,
        zoom: 12,
        zoomControl: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(leafletMap);

    leafletMarkersGroup = L.layerGroup().addTo(leafletMap);
    renderMapMarkers('Nairobi');

    leafletMap.on('click', function(e) {
        if (!selectedCategory || !selectedDates) {
            alert('Please select Crew Category and Event Date first to unlock location selection on the map!');
            return;
        }

        const clickedLat = e.latlng.lat;
        const clickedLng = e.latlng.lng;
        let closestCity = kenyaCities[0];
        let minDistance = Infinity;

        kenyaCities.forEach(city => {
            const dist = Math.hypot(city.coords[0] - clickedLat, city.coords[1] - clickedLng);
            if (dist < minDistance) {
                minDistance = dist;
                closestCity = city;
            }
        });

        selectCity(closestCity.name);
    });
}

function renderMapMarkers(cityToCenter) {
    if (!leafletMap || !leafletMarkersGroup) return;
    leafletMarkersGroup.clearLayers();

    const matchedCity = kenyaCities.find(c => c.name.toLowerCase() === cityToCenter.toLowerCase()) || kenyaCities[0];
    
    // Custom Logo Pin for City Center
    const centerIcon = L.divIcon({
        className: 'custom-map-center-pin',
        html: `<div style="background-color:white; width:34px; height:34px; border-radius:50%; border:3px solid #FF2D55; box-shadow:0 10px 25px rgba(255,45,85,0.6); display:flex; align-items:center; justify-content:center; overflow:hidden;"><img src="/images/africrew_logo.jpg" style="width:100%; height:100%; object-fit:contain; padding:2px;"></div>`,
        iconSize: [34, 34],
        iconAnchor: [17, 17]
    });

    L.marker(matchedCity.coords, { icon: centerIcon }).addTo(leafletMarkersGroup);

    // Add surrounding micro pins for crew members in this city
    const crewInCity = allApprovedCrew.filter(c => (c.city || '').toLowerCase().trim() === cityToCenter.toLowerCase().trim());
    
    crewInCity.slice(0, 10).forEach((crew, idx) => {
        // Offset coords slightly around city center
        const latOffset = (Math.sin(idx + 1) * 0.025);
        const lngOffset = (Math.cos(idx + 1) * 0.025);
        const pinCoords = [matchedCity.coords[0] + latOffset, matchedCity.coords[1] + lngOffset];

        const crewIcon = L.divIcon({
            className: 'custom-crew-pin',
            html: `<div style="background-color:#0F172A; width:22px; height:22px; border-radius:50%; border:2px solid #F59E0B; box-shadow:0 4px 10px rgba(0,0,0,0.4); display:flex; align-items:center; justify-content:center; color:#F59E0B; font-weight:900; font-size:9px;">★</div>`,
            iconSize: [22, 22],
            iconAnchor: [11, 11]
        });

        L.marker(pinCoords, { icon: crewIcon }).addTo(leafletMarkersGroup);
    });

    leafletMap.flyTo(matchedCity.coords, 12, { duration: 1.2 });
}

// Initialize Flatpickr Date Picker
function initDatePicker() {
    if (typeof flatpickr !== 'undefined') {
        flatpickrInstance = flatpickr('#hero_dates_input', {
            mode: 'range',
            dateFormat: 'd M Y',
            minDate: new Date(Date.now() + 6 * 60 * 60 * 1000),
            onChange: function(selectedDatesArr, dateStr, instance) {
                if (selectedDatesArr.length === 2) {
                    const diffMs = Math.abs(selectedDatesArr[1].getTime() - selectedDatesArr[0].getTime());
                    const daysCount = Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1;
                    const startStr = instance.formatDate(selectedDatesArr[0], 'd M');
                    const endStr = instance.formatDate(selectedDatesArr[1], 'd M Y');
                    selectedDates = `${startStr} - ${endStr} (${daysCount}d)`;
                } else if (selectedDatesArr.length === 1) {
                    selectedDates = instance.formatDate(selectedDatesArr[0], 'd M Y');
                } else {
                    selectedDates = '';
                }

                document.getElementById('map-date-label').textContent = selectedDates ? selectedDates : 'EVENT DATE';
                updateStateUI();
            }
        });
    }
}

function openDatePicker() {
    if (flatpickrInstance) {
        flatpickrInstance.open();
    }
}

// Category Selection Handlers
function toggleCategoryMenu() {
    const menu = document.getElementById('map-category-menu');
    const locMenu = document.getElementById('map-location-menu');
    if (locMenu) locMenu.classList.add('hidden');
    if (menu) menu.classList.toggle('hidden');
}

function selectCategory(cat) {
    selectedCategory = cat;
    document.getElementById('map-category-label').textContent = (cat === 'All' ? '⚡ ALL CATEGORIES' : cat.toUpperCase());
    const menu = document.getElementById('map-category-menu');
    if (menu) menu.classList.add('hidden');

    updateStateUI();
}

// Location Selector Dropdown & Geolocation
function toggleLocationMenu() {
    if (!selectedCategory || !selectedDates) {
        alert('🔒 Location Selection is locked!\n\nPlease select Crew Category (Step 1) and Event Date (Step 2) first.');
        return;
    }

    const menu = document.getElementById('map-location-menu');
    const catMenu = document.getElementById('map-category-menu');
    if (catMenu) catMenu.classList.add('hidden');
    if (menu) menu.classList.toggle('hidden');
}

function renderCitiesDropdown() {
    const container = document.getElementById('kenya-cities-list');
    if (!container) return;

    container.innerHTML = '';
    kenyaCities.forEach(city => {
        const count = allApprovedCrew.filter(c => (c.city || '').toLowerCase().trim() === city.name.toLowerCase().trim()).length;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.onclick = () => selectCity(city.name);
        btn.className = 'w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-slate-800 transition-all flex items-center justify-between group';
        btn.innerHTML = `
            <span class="flex items-center gap-2">
                <span class="text-slate-500 group-hover:text-rose-400">📍</span>
                <span>${city.name}</span>
                <span class="text-[10px] text-slate-500 font-normal">(${city.region})</span>
            </span>
            <span class="text-[10px] px-2 py-0.5 rounded-md ${count > 0 ? 'bg-emerald-500/20 text-emerald-400 font-extrabold' : 'bg-slate-800 text-slate-500'}">
                ${count} Crew
            </span>
        `;
        container.appendChild(btn);
    });
}

function selectCity(cityName, isAutoGps = false) {
    selectedCity = cityName;
    
    document.getElementById('map-location-title').textContent = `${cityName} ${isAutoGps ? '(GPS Detected)' : ''}`;
    document.getElementById('map-location-sub').textContent = 'Location Selected • Ready';

    const menu = document.getElementById('map-location-menu');
    if (menu) menu.classList.add('hidden');

    renderMapMarkers(cityName);
    updateStateUI();
}

function detectUserLocation() {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser.');
        return;
    }

    const detectText = document.getElementById('location-detect-text');
    if (detectText) detectText.textContent = 'Detecting GPS...';

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            let closest = kenyaCities[0];
            let minDist = Infinity;

            kenyaCities.forEach(city => {
                const dist = Math.hypot(city.coords[0] - lat, city.coords[1] - lng);
                if (dist < minDist) {
                    minDist = dist;
                    closest = city;
                }
            });

            if (detectText) detectText.textContent = 'Detect My Location (GPS)';
            selectCity(closest.name, true);
        },
        (error) => {
            if (detectText) detectText.textContent = 'Detect My Location (GPS)';
            alert('Location detection permission denied or unavailable. Please pick a city from the list below.');
        },
        { timeout: 8000 }
    );
}


// STATE EVALUATION & STEP LOCKING ENGINE
function updateStateUI() {
    const isCatSet = !!selectedCategory;
    const isDateSet = !!selectedDates;
    const isLocUnlocked = isCatSet && isDateSet;
    const isLocSet = !!selectedCity;
    const isSearchUnlocked = isLocUnlocked && isLocSet;

    // 1. Update Step Checklist Pills
    const p1 = document.getElementById('step-pill-1');
    const p2 = document.getElementById('step-pill-2');
    const p3 = document.getElementById('step-pill-3');
    const stepsProgress = document.getElementById('hero-steps-progress');

    let completedCount = 0;
    if (isCatSet) {
        completedCount++;
        p1.className = 'py-2 px-2 rounded-xl bg-emerald-500 text-white font-black text-center transition-all shadow-xs';
        p1.textContent = '✓ 1. Category';
    } else {
        p1.className = 'py-2 px-2 rounded-xl bg-slate-100 text-slate-400 text-center transition-all border border-slate-200';
        p1.textContent = '1. Category';
    }

    if (isDateSet) {
        completedCount++;
        p2.className = 'py-2 px-2 rounded-xl bg-emerald-500 text-white font-black text-center transition-all shadow-xs';
        p2.textContent = '✓ 2. Date';
    } else {
        p2.className = 'py-2 px-2 rounded-xl bg-slate-100 text-slate-400 text-center transition-all border border-slate-200';
        p2.textContent = '2. Date';
    }

    if (isLocSet) {
        completedCount++;
        p3.className = 'py-2 px-2 rounded-xl bg-emerald-500 text-white font-black text-center transition-all shadow-xs';
        p3.textContent = '✓ 3. Location';
    } else {
        p3.className = 'py-2 px-2 rounded-xl bg-slate-100 text-slate-400 text-center transition-all border border-slate-200';
        p3.textContent = '3. Location';
    }

    if (stepsProgress) stepsProgress.textContent = `${completedCount}/3 Completed`;

    // 2. Update Location Pill Lock State
    const locBtn = document.getElementById('map-location-btn');
    const locIconBox = document.getElementById('location-icon-box');
    const locTitle = document.getElementById('map-location-title');
    const locSub = document.getElementById('map-location-sub');

    if (isLocUnlocked) {
        locBtn.className = 'w-full text-left px-3.5 py-2.5 rounded-xl bg-white text-slate-900 border border-amber-400 shadow-lg flex items-center gap-3 transition-all cursor-pointer hover:border-rose-500 animate-pulse';
        locIconBox.className = 'w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center text-sm shrink-0 shadow-sm';
        locIconBox.textContent = '📍';
        
        if (!isLocSet) {
            locTitle.textContent = 'Select Event Location';
            locSub.textContent = 'Unlocked • Click to choose Kenyan city or GPS';
            locTitle.className = 'font-extrabold text-xs text-slate-900 truncate';
            locSub.className = 'text-[10px] text-rose-500 font-bold truncate';
        }
    } else {
        locBtn.className = 'w-full text-left px-3.5 py-2.5 rounded-xl bg-slate-900 text-slate-400 border border-slate-800 text-xs font-bold flex items-center gap-3 transition-all cursor-not-allowed opacity-80';
        locIconBox.className = 'w-8 h-8 rounded-lg bg-rose-500/10 text-rose-500 flex items-center justify-center text-sm shrink-0';
        locIconBox.textContent = '🔒';
        locTitle.textContent = 'Select Event Location';
        locSub.textContent = 'Select Category & Date first';
        locTitle.className = 'font-extrabold text-xs text-white truncate';
        locSub.className = 'text-[10px] text-slate-400 truncate';
    }

    // 3. Update Search Button Lock State
    const searchBtn = document.getElementById('hero-search-submit-btn');
    const searchLabel = document.getElementById('search-btn-label');

    if (isSearchUnlocked) {
        searchBtn.disabled = false;
        searchBtn.className = 'px-7 py-3 rounded-xl bg-gradient-to-r from-rose-500 via-pink-500 to-amber-500 hover:opacity-95 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-rose-500/30 hover:scale-105 cursor-pointer transition-all shrink-0 flex items-center gap-2 animate-bounce';
        searchLabel.textContent = 'SEARCH →';
    } else {
        searchBtn.disabled = true;
        searchBtn.className = 'px-6 py-3 rounded-xl bg-slate-800 text-slate-500 font-black text-xs uppercase tracking-wider opacity-50 cursor-not-allowed shadow-none transition-all shrink-0 flex items-center gap-2';
        searchLabel.textContent = 'SEARCH 🔒';
    }

    // 4. Update Dynamic Right-Side Text for state clicked
    const badgeText = document.getElementById('hero-state-badge-text');
    const stateTitle = document.getElementById('hero-state-title');
    const stateSub = document.getElementById('hero-state-sub');
    const stat1Val = document.getElementById('hero-stat-1-val');
    const stat1Lbl = document.getElementById('hero-stat-1-lbl');

    if (!isCatSet && !isDateSet) {
        // STATE 0: Nothing selected
        if (badgeText) badgeText.textContent = 'STEP 1 OF 3 • PLAN YOUR BIG DAY';
        if (stateTitle) stateTitle.innerHTML = 'Find Your Perfect <span class="bg-gradient-to-r from-rose-500 via-pink-500 to-amber-500 bg-clip-text text-transparent">Event Crew</span>';
        if (stateSub) stateSub.textContent = 'Tell us who you need and when — select crew category & date to unlock location search.';
        if (stat1Val) stat1Val.textContent = '42+';
        if (stat1Lbl) stat1Lbl.textContent = 'CREW IN NAIROBI';
    } else if (isCatSet && !isDateSet) {
        // STATE 1: Category Selected
        if (badgeText) badgeText.textContent = 'STEP 2 OF 3 • SELECT EVENT DATES';
        if (stateTitle) stateTitle.innerHTML = `Looking for <span class="bg-gradient-to-r from-rose-500 via-pink-500 to-amber-500 bg-clip-text text-transparent">${selectedCategory}</span>?`;
        if (stateSub) stateSub.textContent = `Great choice! Now click "EVENT DATE" on the map to pick your event date(s) and unlock location selection.`;
        
        const catCrewCount = allApprovedCrew.filter(c => selectedCategory === 'All' || (c.category || '').toLowerCase().includes(selectedCategory.toLowerCase())).length;
        if (stat1Val) stat1Val.textContent = `${catCrewCount}+`;
        if (stat1Lbl) stat1Lbl.textContent = `${selectedCategory.toUpperCase()} PROS`;
    } else if (isCatSet && isDateSet && !isLocSet) {
        // STATE 2: Category & Date Selected (Location Unlocked)
        if (badgeText) badgeText.textContent = 'STEP 3 OF 3 • LOCATION UNLOCKED';
        if (stateTitle) stateTitle.innerHTML = 'Where is your <span class="bg-gradient-to-r from-rose-500 via-pink-500 to-amber-500 bg-clip-text text-transparent">Event Located</span>?';
        if (stateSub) stateSub.textContent = 'Location selection is now unlocked! Auto-detect your GPS location or choose a popular Kenyan city below.';
        if (stat1Val) stat1Val.textContent = 'Ready';
        if (stat1Lbl) stat1Lbl.textContent = 'LOCATION UNLOCKED';
    } else if (isSearchUnlocked) {
        // STATE 3: All 3 Selected (Ready to Search!)
        if (badgeText) badgeText.textContent = 'READY TO SEARCH • ALL FIELDS FILLED';
        if (stateTitle) stateTitle.innerHTML = `<span class="bg-gradient-to-r from-rose-500 via-pink-500 to-amber-500 bg-clip-text text-transparent">${selectedCategory}</span> in ${selectedCity}`;
        
        const totalMatches = allApprovedCrew.filter(c => {
            const matchCat = selectedCategory === 'All' || (c.category || '').toLowerCase().includes(selectedCategory.toLowerCase());
            const matchCity = (c.city || '').toLowerCase().trim() === selectedCity.toLowerCase().trim();
            return matchCat && matchCity;
        }).length;

        if (stateSub) stateSub.textContent = `We found ${totalMatches} vetted crew member(s) available in ${selectedCity} for ${selectedDates}! Click SEARCH to browse.`;
        if (stat1Val) stat1Val.textContent = `${totalMatches}`;
        if (stat1Lbl) stat1Lbl.textContent = `CREW IN ${selectedCity.toUpperCase()}`;
    }
}

// SEARCH SUBMISSION & CREW CARD RENDERING
function onHeroFormSubmit(e) {
    e.preventDefault();

    if (!selectedCategory || !selectedDates || !selectedCity) {
        alert('Please fill all 3 fields (Category, Event Date, Location) before searching.');
        return;
    }

    renderCrewResultsGrid();

    const searchResultsSection = document.getElementById('home-search-results-section');
    if (searchResultsSection) {
        searchResultsSection.classList.remove('hidden');
        searchResultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function renderCrewResultsGrid() {
    const homeGrid = document.getElementById('home-crew-cards-grid');
    const homeTitle = document.getElementById('home-results-title');
    const homeSubtitle = document.getElementById('home-results-subtitle');
    const homeViewAll = document.getElementById('home-view-all-link');

    let matching = allApprovedCrew.filter(c => {
        let matchCat = true;
        if (selectedCategory && selectedCategory !== 'All') {
            const crewCat = (c.category || '').toLowerCase();
            const crewSkills = (c.skills || '').toLowerCase();
            const searchCat = selectedCategory.toLowerCase();

            matchCat = crewCat === searchCat || crewCat.includes(searchCat) || searchCat.includes(crewCat) || crewSkills.includes(searchCat);
        }

        let matchCity = true;
        if (selectedCity) {
            matchCity = (c.city || '').toLowerCase().trim() === selectedCity.toLowerCase().trim();
        }

        return matchCat && matchCity;
    });

    if (homeTitle) {
        homeTitle.textContent = `Available Crew in ${selectedCity} • ${selectedCategory}`;
    }
    if (homeSubtitle) {
        homeSubtitle.textContent = matching.length > 0
            ? `Found ${matching.length} vetted ${selectedCategory} professional(s) ready for your event on ${selectedDates}`
            : `Showing search results for ${selectedCategory} in ${selectedCity}`;
    }
    if (homeViewAll) {
        homeViewAll.href = `/crew?category=${encodeURIComponent(selectedCategory)}&city=${encodeURIComponent(selectedCity)}&dates=${encodeURIComponent(selectedDates)}`;
    }

    if (homeGrid) {
        homeGrid.innerHTML = '';
        if (matching.length === 0) {
            homeGrid.innerHTML = `
                <div class="col-span-full py-12 px-6 text-center rounded-3xl bg-white border border-slate-200/90 text-slate-900 space-y-3 shadow-sm">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 font-black text-2xl flex items-center justify-center mx-auto shadow-inner">
                        🔍
                    </div>
                    <h4 class="text-base font-black text-slate-950">No Approved Crew Found for "${selectedCategory}" in ${selectedCity}</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto font-medium">Currently no registered and approved crew members match this location and role. You can request crew via the custom booking flow.</p>
                </div>
            `;
        } else {
            matching.forEach((p, idx) => {
                const avatarHtml = p.profile_photo_url
                    ? `<img src="${p.profile_photo_url}" alt="${p.full_name}" class="w-16 h-16 rounded-2xl object-cover border-4 border-white shadow-lg shrink-0 bg-slate-100">`
                    : `<div class="w-16 h-16 rounded-2xl border-4 border-white bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-xl flex items-center justify-center shadow-lg shrink-0">${p.full_name.charAt(0).toUpperCase()}</div>`;
                
                const handle = p.username ? ('@' + p.username) : ('@' + p.full_name.toLowerCase().replace(/\s+/g, ''));
                const gridRatingBadgeHtml = (p.reviews_count > 0 && parseFloat(p.average_rating) > 0) ? `
                    <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-xl text-[11px] font-black text-slate-950 shadow-xs flex items-center gap-1">
                        <span class="text-amber-500">★</span>
                        <span>${p.average_rating}</span>
                    </div>
                ` : '';

                const cardHtml = `
                    <div class="bg-white border border-slate-200/90 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col justify-between group animate-crew-card" style="animation-delay: ${idx * 0.08}s">
                        <div>
                            <div class="relative h-32 w-full bg-slate-950 overflow-hidden">
                                <img src="/images/hero_event_ushers.jpg" alt="${p.full_name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
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
                                    ${handle} (${p.full_name})
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
</script>

@endsection
