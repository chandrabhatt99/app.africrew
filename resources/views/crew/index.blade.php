@extends('layouts.app')

@section('content')
<div class="bg-[#FAF9F6] text-slate-900 min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- BREADCRUMB NAVIGATION & TOP ACTIVE FILTER BAR (PDF PAGE 3) -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
            <div class="flex items-center gap-2 text-xs font-extrabold text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-slate-900 text-decoration-none transition-colors">Home</a>
                <span>›</span>
                <span class="text-slate-900">Search Results</span>
            </div>

            <!-- Active Search Filter Pills Bar & Edit Search Button -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="px-3.5 py-1.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 font-extrabold flex items-center gap-1.5 shadow-2xs">
                    <span>👥</span>
                    <span>{{ request('category') ?: 'All Categories' }}</span>
                </span>
                @if(request('dates'))
                <span class="px-3.5 py-1.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 font-extrabold flex items-center gap-1.5 shadow-2xs">
                    <span>📅</span>
                    <span>{{ request('dates') }}</span>
                </span>
                @endif
                <span class="px-3.5 py-1.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 font-extrabold flex items-center gap-1.5 shadow-2xs">
                    <span>📍</span>
                    <span>{{ request('city') ?: 'All Locations' }}</span>
                </span>
                
                <a href="{{ route('home') }}" class="px-3.5 py-1.5 rounded-full bg-rose-50 border border-rose-200 text-rose-600 font-extrabold text-decoration-none hover:bg-rose-100 transition-all">
                    Edit Search
                </a>
            </div>
        </div>

        <!-- PAGE HEADER TITLE & SORT SELECTOR -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight">
                    Who would you like to add to your event crew?
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-normal mt-1">
                    Select vetted professionals matching your specific requirements or add them to your team basket.
                </p>
                <div class="text-xs font-bold text-slate-400 mt-1 flex items-center gap-1.5">
                    <span>📍 {{ request('city') ?: 'Nairobi' }}</span>
                    <span>•</span>
                    <span>📅 {{ request('dates') ?: '12 Sep – 14 Sep 2026' }}</span>
                </div>
            </div>

            <!-- Sort By & Mobile Filter Button Controls -->
            <div class="flex items-center gap-2 shrink-0">
                <!-- Mobile Filter Toggle Button -->
                <button type="button" onclick="toggleMobileFilterModal()" class="lg:hidden px-3.5 py-2.5 rounded-2xl bg-white border border-slate-200 hover:border-amber-400 text-slate-900 font-extrabold text-xs shadow-2xs flex items-center gap-1.5 cursor-pointer">
                    <span>🎛️</span>
                    <span>Filter</span>
                    @if(request('category') || request('city') || request('exp_range') || request('min_rating'))
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    @endif
                </button>

                <form method="GET" action="{{ route('crew.index') }}" class="m-0">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="city" value="{{ request('city') }}">
                    <input type="hidden" name="dates" value="{{ request('dates') }}">
                    <input type="hidden" name="exp_range" value="{{ request('exp_range') }}">
                    <input type="hidden" name="min_rating" value="{{ request('min_rating') }}">

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-bold text-slate-500 whitespace-nowrap hidden sm:inline">Sort:</label>
                        <select name="sort" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-extrabold text-slate-900 outline-none focus:border-rose-500 shadow-2xs cursor-pointer">
                            <option value="best_match" {{ request('sort', 'best_match') == 'best_match' ? 'selected' : '' }}>Sort: Best Match</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Sort: Highest Rating</option>
                            <option value="experience" {{ request('sort') == 'experience' ? 'selected' : '' }}>Sort: Most Experienced</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- MAIN CONTENT LAYOUT: SIDEBAR FILTERS LEFT + 3-COLUMN GRID RIGHT -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pt-4">
            
            <!-- DESKTOP LEFT SIDEBAR FILTER PANEL -->
            <div class="hidden lg:block lg:col-span-3 space-y-6">
                <form method="GET" action="{{ route('crew.index') }}" class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm space-y-6 m-0">
                    <input type="hidden" name="dates" value="{{ request('dates') }}">

                    <div class="text-xs font-black uppercase tracking-wider text-slate-950 border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span>Filters</span>
                        <a href="{{ route('crew.index') }}" class="text-[10px] text-rose-500 font-bold hover:underline text-decoration-none">Reset All</a>
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">Category</label>
                        <select name="category" onchange="this.form.submit()" class="w-full bg-[#FAF9F6] border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-900 outline-none focus:border-rose-500">
                            <option value="">All Categories</option>
                            @if(isset($categories) && count($categories) > 0)
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Location / City Filter Dropdown -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">Location (Registered Crew)</label>
                        <select name="city" class="w-full bg-[#FAF9F6] border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-900 outline-none focus:border-rose-500">
                            <option value="">All Locations</option>
                            @if(isset($availableCities) && count($availableCities) > 0)
                                @foreach($availableCities as $c)
                                    <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Experience Checkboxes -->
                    <div class="space-y-3 pt-2 border-t border-slate-100">
                        <label class="block text-xs font-extrabold text-slate-900">Experience</label>
                        <div class="space-y-2 text-xs text-slate-600 font-medium">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="radio" name="exp_range" value="1-3" {{ request('exp_range') == '1-3' ? 'checked' : '' }} class="rounded text-rose-500 focus:ring-rose-500">
                                <span>1 - 3 years</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="radio" name="exp_range" value="4-7" {{ request('exp_range') == '4-7' ? 'checked' : '' }} class="rounded text-rose-500 focus:ring-rose-500">
                                <span>4 - 7 years</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="radio" name="exp_range" value="8+" {{ request('exp_range') == '8+' ? 'checked' : '' }} class="rounded text-rose-500 focus:ring-rose-500">
                                <span>8+ years</span>
                            </label>
                        </div>
                    </div>

                    <!-- Rating Filter Checkboxes -->
                    <div class="space-y-3 pt-2 border-t border-slate-100">
                        <label class="block text-xs font-extrabold text-slate-900">Rating</label>
                        <div class="space-y-2 text-xs text-slate-600 font-medium">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="radio" name="min_rating" value="4.5" {{ request('min_rating') == '4.5' ? 'checked' : '' }} class="rounded text-rose-500 focus:ring-rose-500">
                                <span class="flex items-center gap-1">
                                    <span class="text-amber-500">★</span>
                                    <span>4.5 & up</span>
                                </span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="radio" name="min_rating" value="4.0" {{ request('min_rating') == '4.0' ? 'checked' : '' }} class="rounded text-rose-500 focus:ring-rose-500">
                                <span class="flex items-center gap-1">
                                    <span class="text-amber-500">★</span>
                                    <span>4.0 & up</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Apply Filters Button -->
                    <button type="submit" class="w-full py-3 rounded-2xl bg-gradient-to-r from-rose-500 to-amber-500 hover:opacity-95 text-white font-extrabold text-xs shadow-md shadow-rose-500/20 transition-all uppercase tracking-wider cursor-pointer">
                        Apply Filters
                    </button>
                </form>
            </div>

            <!-- RIGHT 3-COLUMN CREW CARDS GRID (PDF PAGE 3) -->
            <div class="lg:col-span-9 space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($professionals as $idx => $p)
                        @php
                            $handle = $p->username ? ('@' . $p->username) : ('@' . Str::slug($p->full_name));
                            $rating = number_format($p->average_rating, 1);
                            $coverUrl = $p->cover_photo_url ?: asset('images/hero_event_ushers.jpg');
                            $avatarUrl = $p->profile_photo_url;
                        @endphp
                        <div class="bg-white border border-slate-200/90 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col justify-between group">
                            
                            <div>
                                <!-- Photo Cover Banner with Rating Top Right -->
                                <div class="relative h-36 w-full bg-slate-900 overflow-hidden">
                                    <img src="{{ $coverUrl }}" alt="{{ $p->full_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 to-transparent"></div>
                                    
                                    @if(($p->reviews_count ?? 0) > 0 && ($p->average_rating ?? 0) > 0)
                                        <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-xl text-[11px] font-black text-slate-900 shadow-xs flex items-center gap-1">
                                            <span class="text-amber-500">★</span>
                                            <span>{{ $rating }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Overlapping Avatar & Available Status -->
                                <div class="px-4 relative -mt-8 mb-2 flex items-end justify-between">
                                    @if($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="{{ $p->full_name }}" class="w-16 h-16 rounded-2xl object-cover border-4 border-white shadow-lg shrink-0 bg-slate-100">
                                    @else
                                        <div class="w-16 h-16 rounded-2xl border-4 border-white bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-xl flex items-center justify-center shadow-lg shrink-0">
                                            {{ strtoupper(substr($p->full_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Available</span>
                                    </span>
                                </div>

                                <!-- Handle, Role, City & Stats -->
                                <div class="px-4 space-y-1">
                                    <h3 class="text-sm font-black text-slate-950 group-hover:text-rose-500 transition-colors truncate">
                                        {{ $handle }}
                                    </h3>
                                    <p class="text-xs text-slate-500 font-semibold truncate">
                                        {{ $p->category ?: 'Event Staff' }} • {{ $p->city ?: 'Nairobi' }}
                                    </p>
                                    
                                    <div class="flex items-center gap-3 pt-2 text-[11px] font-bold text-slate-400">
                                        <span>{{ $p->experience_years ? $p->experience_years . ' yrs exp' : 'Vetted Crew' }}</span>
                                        @if($p->completed_gigs_count)
                                            <span>•</span>
                                            <span>{{ $p->completed_gigs_count }}+ events</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons: View Profile & ADD TO TEAM -->
                            <div class="p-4 pt-3 space-y-2">
                                <a href="{{ route('crew.show', $p) }}" class="w-full text-center py-2 px-3 rounded-xl border border-slate-300 text-slate-800 font-extrabold text-xs hover:bg-slate-900 hover:text-white transition-all shadow-2xs text-decoration-none block">
                                    View Profile
                                </a>
                                <button type="button" onclick="addCrewToTeamBasket({{ json_encode([
                                    'id' => $p->id,
                                    'full_name' => $p->full_name,
                                    'profile_photo_url' => $avatarUrl,
                                    'category' => $p->category ?: 'Event Usher'
                                ]) }})" class="w-full py-2 px-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 font-extrabold text-xs hover:bg-amber-500 hover:text-slate-950 transition-all flex items-center justify-center gap-1">
                                    <span>⊕ Add to Team</span>
                                </button>
                            </div>

                        </div>
                    @empty
                        <div class="col-span-full text-center py-16 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm space-y-3">
                            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 mx-auto flex items-center justify-center text-2xl shadow-inner">
                                🔍
                            </div>
                            <h3 class="text-lg font-black text-slate-950">No {{ request('category') ?: 'Crew' }} Found {{ request('city') ? 'in ' . request('city') : '' }}</h3>
                            <p class="text-xs text-slate-500 max-w-md mx-auto">
                                There are currently no approved {{ request('category') ?: 'crew members' }} matching your exact filter requirements in {{ request('city') ?: 'this area' }}.
                            </p>
                            <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                                @if(request('city'))
                                    <a href="{{ route('crew.index', ['category' => request('category'), 'dates' => request('dates')]) }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-extrabold text-xs text-decoration-none shadow-sm hover:bg-slate-800">
                                        View {{ request('category') ?: 'Crew' }} in All Cities
                                    </a>
                                @endif
                                @if(request('category'))
                                    <a href="{{ route('crew.index', ['city' => request('city'), 'dates' => request('dates')]) }}" class="px-4 py-2 rounded-xl bg-amber-500 text-slate-950 font-extrabold text-xs text-decoration-none shadow-sm hover:bg-amber-600">
                                        View All Categories in {{ request('city') ?: 'Location' }}
                                    </a>
                                @endif
                                <a href="{{ route('crew.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-extrabold text-xs text-decoration-none hover:bg-slate-200">
                                    Reset All Filters
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- PAGINATION CONTROLS -->
                @if($professionals->hasPages())
                    <div class="flex items-center justify-center gap-2 pt-6">
                        @if ($professionals->onFirstPage())
                            <span class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 text-slate-300 font-bold text-xs flex items-center justify-center cursor-not-allowed">&lt;</span>
                        @else
                            <a href="{{ $professionals->previousPageUrl() }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center hover:bg-slate-50 text-decoration-none transition-colors">&lt;</a>
                        @endif

                        @foreach ($professionals->getUrlRange(1, $professionals->lastPage()) as $page => $url)
                            @if ($page == $professionals->currentPage())
                                <span class="w-9 h-9 rounded-xl bg-slate-950 text-white font-black text-xs flex items-center justify-center shadow-md">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center hover:bg-slate-50 text-decoration-none transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($professionals->hasMorePages())
                            <a href="{{ $professionals->nextPageUrl() }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center hover:bg-slate-50 text-decoration-none transition-colors">&gt;</a>
                        @else
                            <span class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 text-slate-300 font-bold text-xs flex items-center justify-center cursor-not-allowed">&gt;</span>
                        @endif
                    </div>
                @endif

            </div>

        </div>

    </div>
</div>

<!-- MOBILE FILTER POPUP DRAWER MODAL -->
<div id="mobile-filter-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/80 backdrop-blur-md transition-opacity duration-300">
    <div class="min-h-screen px-4 text-center flex items-center justify-center py-6">
        <div class="fixed inset-0" onclick="toggleMobileFilterModal()"></div>
        <div class="inline-block w-full max-w-lg p-6 my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl relative z-10 border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-base">🎛️</span>
                    <h3 class="text-base font-black text-slate-900">Filter Crew Results</h3>
                </div>
                <button type="button" onclick="toggleMobileFilterModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 font-black text-xs hover:bg-slate-200 flex items-center justify-center cursor-pointer">✕</button>
            </div>
            
            <form method="GET" action="{{ route('crew.index') }}" class="space-y-5">
                <input type="hidden" name="sort" value="{{ request('sort', 'best_match') }}">
                <input type="hidden" name="dates" value="{{ request('dates') }}">

                <!-- Category -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Category</label>
                    <select name="category" class="w-full bg-[#FAF9F6] border border-slate-200 rounded-xl px-3.5 py-3 text-xs font-bold text-slate-900 outline-none focus:border-rose-500">
                        <option value="">All Categories</option>
                        @if(isset($categories) && count($categories) > 0)
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Location -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Location (Registered Crew)</label>
                    <select name="city" class="w-full bg-[#FAF9F6] border border-slate-200 rounded-xl px-3.5 py-3 text-xs font-bold text-slate-900 outline-none focus:border-rose-500">
                        <option value="">All Locations</option>
                        @if(isset($availableCities) && count($availableCities) > 0)
                            @foreach($availableCities as $c)
                                <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Experience -->
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-extrabold text-slate-900">Experience</label>
                    <div class="grid grid-cols-3 gap-2 text-xs font-medium">
                        <label class="p-3 rounded-xl border text-center cursor-pointer {{ request('exp_range') == '1-3' ? 'bg-amber-400 border-amber-500 font-black text-slate-950' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                            <input type="radio" name="exp_range" value="1-3" {{ request('exp_range') == '1-3' ? 'checked' : '' }} class="hidden">
                            <span>1 - 3 yrs</span>
                        </label>
                        <label class="p-3 rounded-xl border text-center cursor-pointer {{ request('exp_range') == '4-7' ? 'bg-amber-400 border-amber-500 font-black text-slate-950' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                            <input type="radio" name="exp_range" value="4-7" {{ request('exp_range') == '4-7' ? 'checked' : '' }} class="hidden">
                            <span>4 - 7 yrs</span>
                        </label>
                        <label class="p-3 rounded-xl border text-center cursor-pointer {{ request('exp_range') == '8+' ? 'bg-amber-400 border-amber-500 font-black text-slate-950' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                            <input type="radio" name="exp_range" value="8+" {{ request('exp_range') == '8+' ? 'checked' : '' }} class="hidden">
                            <span>8+ yrs</span>
                        </label>
                    </div>
                </div>

                <!-- Rating -->
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-extrabold text-slate-900">Minimum Rating</label>
                    <div class="grid grid-cols-2 gap-2 text-xs font-medium">
                        <label class="p-3 rounded-xl border text-center cursor-pointer {{ request('min_rating') == '4.5' ? 'bg-amber-400 border-amber-500 font-black text-slate-950' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                            <input type="radio" name="min_rating" value="4.5" {{ request('min_rating') == '4.5' ? 'checked' : '' }} class="hidden">
                            <span>★ 4.5 & up</span>
                        </label>
                        <label class="p-3 rounded-xl border text-center cursor-pointer {{ request('min_rating') == '4.0' ? 'bg-amber-400 border-amber-500 font-black text-slate-950' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                            <input type="radio" name="min_rating" value="4.0" {{ request('min_rating') == '4.0' ? 'checked' : '' }} class="hidden">
                            <span>★ 4.0 & up</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between gap-3">
                    <a href="{{ route('crew.index') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs text-decoration-none">
                        Reset All
                    </a>
                    <button type="submit" class="flex-grow py-3 rounded-2xl bg-gradient-to-r from-rose-500 to-amber-500 text-white font-extrabold text-xs uppercase tracking-wider shadow-md">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleMobileFilterModal() {
    const modal = document.getElementById('mobile-filter-modal');
    if (modal) {
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }
}
</script>
@endsection
