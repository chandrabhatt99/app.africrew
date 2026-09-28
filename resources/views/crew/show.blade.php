@extends('layouts.app')

@section('content')
    <div class="bg-slate-50 text-slate-900 min-h-screen font-sans selection:bg-rose-500 selection:text-white pb-28">

        <!-- Flash Error / Success Notifications -->
        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4">
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-xs animate-fadeIn">
                    <div class="flex items-center gap-2">
                        <span class="text-base">⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 font-black text-xs hover:text-rose-700">✕</button>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4">
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs animate-fadeIn">
                    <div class="flex items-center gap-2">
                        <span class="text-base">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 font-black text-xs hover:text-emerald-800">✕</button>
                </div>
            </div>
        @endif

        <!-- Top Container: Hero Cover Banner & Profile Card -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-2 sm:pt-4">
            <div class="relative rounded-3xl overflow-hidden shadow-2xl bg-slate-950 border border-slate-800">

                <!-- Cover Header Photo Banner -->
                <div class="relative h-64 sm:h-80 md:h-96 w-full overflow-hidden bg-gradient-to-r from-slate-950 via-slate-900 to-amber-950">
                    @php
                        $coverPhoto = $professional->cover_photo ? get_storage_url($professional->cover_photo) : asset('images/hero_event_ushers.jpg');
                    @endphp
                    <img src="{{ $coverPhoto }}" alt="Cover Header" class="w-full h-full object-cover object-center opacity-70 transform hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-slate-950/20"></div>

                    <!-- Top Left Status Pill Badge -->
                    <div class="absolute top-5 left-5 z-10 flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-950/80 backdrop-blur-md border border-white/20 text-white text-[11px] font-black uppercase tracking-wider shadow-lg">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_10px_#34d399]"></span>
                            <span>Available for Hire</span>
                        </span>
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-amber-500/20 backdrop-blur-md border border-amber-400/30 text-amber-300 text-[11px] font-extrabold uppercase tracking-wider shadow-md">
                            <span>🛡️ Verified Pro</span>
                        </span>
                    </div>

                    <!-- Top Right Quick Action Buttons -->
                    <div class="absolute top-5 right-5 z-10 flex items-center gap-2">
                        <button onclick="copyProfileLink()" class="px-3.5 py-2 rounded-2xl bg-slate-950/80 backdrop-blur-md border border-white/20 text-white hover:bg-slate-900 font-extrabold text-xs transition-all shadow-md flex items-center gap-1.5 cursor-pointer" title="Share Profile">
                            <span>🔗</span>
                            <span class="hidden sm:inline">Share Profile</span>
                        </button>
                        <form method="POST" action="{{ route('client.favorites.toggle', $professional->id) }}" class="inline-block">
                            @csrf
                            <button type="submit" class="w-9 h-9 rounded-2xl bg-slate-950/80 backdrop-blur-md border border-white/20 text-white hover:bg-slate-900 flex items-center justify-center text-sm transition-all shadow-md cursor-pointer" title="Bookmark / Save Favorite">
                                @if(Auth::check() && Auth::user()->favorites->contains($professional->id))
                                    ❤️
                                @else
                                    🔖
                                @endif
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Overlapping Profile Details Bar (Bottom Section of Cover) -->
                <div class="relative bg-slate-950 px-6 sm:px-8 pb-6 pt-4 border-t border-slate-800/80">
                    <div class="flex flex-col lg:flex-row items-start lg:items-end justify-between gap-6 -mt-20 sm:-mt-24 relative z-20">

                        <!-- Left: Avatar & Name/Title -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-5">

                            <!-- Avatar Squircle Image Container -->
                            <div class="relative shrink-0">
                                @php
                                    $photoUrl = $professional->profile_photo ? get_storage_url($professional->profile_photo) : null;
                                @endphp
                                @if($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="{{ $professional->full_name }}" class="w-28 h-28 sm:w-36 sm:h-36 rounded-3xl border-4 border-slate-950 shadow-2xl object-cover bg-slate-800 ring-2 ring-amber-400/40">
                                @else
                                    <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-3xl border-4 border-slate-950 bg-gradient-to-br from-amber-400 via-rose-500 to-pink-500 text-slate-950 font-black text-4xl flex items-center justify-center shadow-2xl ring-2 ring-amber-400/40">
                                        {{ strtoupper(substr($professional->full_name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="w-6 h-6 rounded-full bg-emerald-500 border-2 border-slate-950 absolute -bottom-1 -right-1 shadow-lg flex items-center justify-center text-xs text-white font-black" title="Verified Professional">✓</span>
                            </div>

                            <!-- Name & Category Title -->
                            <div class="space-y-1.5 sm:mb-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-white tracking-tight">
                                        {{ $professional->full_name }}
                                    </h1>
                                    <span class="px-3 py-0.5 rounded-full bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 text-white font-black text-[10px] uppercase tracking-wider shadow-md">
                                        VERIFIED CREW
                                    </span>
                                </div>

                                <p class="text-xs sm:text-sm font-bold text-amber-400 flex items-center gap-2 flex-wrap">
                                    <span>{{ $professional->category }}</span>
                                    @if($professional->experience_years)
                                        <span class="text-slate-600">•</span>
                                        <span class="text-slate-300 font-semibold">{{ $professional->experience_years }} {{ Str::plural('Year', $professional->experience_years) }} Experience</span>
                                    @endif
                                </p>

                                <div class="text-xs text-slate-400 font-medium flex items-center gap-3 flex-wrap">
                                    <span class="flex items-center gap-1">📍 {{ $professional->city ?: 'Nairobi' }}, {{ $professional->country ?: 'Kenya' }}</span>
                                    @if($professional->availability)
                                        <span class="text-slate-700">•</span>
                                        <span class="text-emerald-400 font-extrabold flex items-center gap-1">⚡ {{ ucfirst($professional->availability) }}</span>
                                    @endif
                                    @if($professional->reviews_count > 0 && $professional->average_rating > 0)
                                        <span class="text-slate-700">•</span>
                                        <span class="text-amber-400 font-bold flex items-center gap-1">⭐ {{ number_format($professional->average_rating, 1) }} ({{ $professional->reviews_count }})</span>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <!-- Right Action Buttons & Quick Rate -->
                        <div class="w-full lg:w-auto shrink-0 lg:mb-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl px-4 py-2 text-center sm:text-right hidden sm:block">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Daily Rate</span>
                                <span class="text-base font-black text-amber-400">
                                    {{ $professional->currency ?: 'KES' }} {{ number_format($professional->one_day_rate ?? ($professional->full_day_rate ?? 5000)) }}
                                </span>
                            </div>

                            @php
                                $isSelf = (session('professional_id') && session('professional_id') == $professional->id) || (Auth::check() && strtolower(Auth::user()->email) === strtolower($professional->email));
                            @endphp
                            @if($isSelf)
                                <a href="{{ route('professional.profile') }}" class="px-6 py-3.5 rounded-full bg-slate-800 hover:bg-slate-700 text-amber-400 border border-slate-700 font-black text-xs transition-all shadow-lg flex items-center justify-center gap-2 text-decoration-none">
                                    <span>✏️ Edit My Profile</span>
                                </a>
                            @else
                                <a href="{{ route('crew.select', $professional) }}" class="px-8 py-3.5 rounded-full bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 hover:opacity-95 text-white font-black text-xs tracking-wider uppercase transition-all shadow-xl shadow-pink-500/25 flex items-center justify-center gap-2 text-decoration-none">
                                    <span>Hire {{ strtok($professional->full_name, ' ') }} Now →</span>
                                </a>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Sticky Section Navigation Tab Bar -->
        <div class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs my-6 transition-all">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex items-center gap-2 sm:gap-6 overflow-x-auto no-scrollbar py-3 text-xs font-bold text-slate-600">
                    <a href="#about-section" class="nav-tab-btn px-4 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-all shrink-0 font-extrabold flex items-center gap-1.5">
                        <span>👤</span> <span>Overview</span>
                    </a>
                    <a href="#skills-section" class="nav-tab-btn px-4 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-all shrink-0 font-extrabold flex items-center gap-1.5">
                        <span>⚡</span> <span>Capabilities & Skills</span>
                    </a>
                    <a href="#experience-section" class="nav-tab-btn px-4 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-all shrink-0 font-extrabold flex items-center gap-1.5">
                        <span>💼</span> <span>Work Experience</span>
                    </a>
                    <a href="#portfolio-section" class="nav-tab-btn px-4 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-all shrink-0 font-extrabold flex items-center gap-1.5">
                        <span>🖼️</span> <span>Portfolio</span>
                    </a>
                    <a href="#reviews-section" class="nav-tab-btn px-4 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-all shrink-0 font-extrabold flex items-center gap-1.5">
                        <span>⭐</span> <span>Client Reviews</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main 2-Column Layout -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-2">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- LEFT COLUMN: Main Profile Content (Col Span 8) -->
                <div class="lg:col-span-8 space-y-10">

                    <!-- SECTION 1: ABOUT / THE PERSON -->
                    <section id="about-section" class="scroll-mt-28 bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                        <div>
                            <span class="text-[11px] font-black uppercase tracking-widest text-rose-500 block mb-1">THE PERSON</span>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Biography & Profile Summary</h2>
                        </div>

                        <!-- Dynamic Highlight Quote -->
                        @if(!empty($professional->highlight_quote))
                            <div class="p-5 rounded-2xl bg-amber-50/80 border-l-4 border-amber-500 text-slate-900 space-y-1 shadow-2xs">
                                <span class="text-amber-500 font-black text-xl leading-none">“</span>
                                <blockquote class="text-base sm:text-lg font-extrabold italic leading-snug tracking-tight text-slate-900">
                                    {{ e($professional->highlight_quote) }}
                                </blockquote>
                            </div>
                        @endif

                        <!-- Bio Content -->
                        <div class="prose prose-slate max-w-none text-slate-600 text-xs sm:text-sm leading-relaxed space-y-3 font-medium">
                            @if(!empty($professional->about))
                                {!! nl2br(e($professional->about)) !!}
                            @else
                                <p>Hello, I'm {{ $professional->full_name }}. I am a vetted {{ $professional->category ?: 'event staff professional' }} based in {{ $professional->city ?: 'Nairobi' }}, {{ $professional->country ?: 'Kenya' }}. Dedicated to bringing hospitality excellence, punctuality, and professional etiquette to corporate and private events.</p>
                            @endif
                        </div>

                        <!-- Preferred Working Locations & Social Links -->
                        <div class="pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            @if(!empty($professional->preferred_locations))
                                @php
                                    $pLocs = is_array($professional->preferred_locations) ? implode(', ', $professional->preferred_locations) : $professional->preferred_locations;
                                @endphp
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">📍 Preferred Working Locations</span>
                                    <span class="font-extrabold text-slate-900 block">{{ $pLocs }}</span>
                                </div>
                            @endif

                            @if(!empty($professional->social_links) && is_array($professional->social_links) && count(array_filter($professional->social_links)) > 0)
                                <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 space-y-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-900 block">🌐 Social Media Profiles</span>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if(!empty($professional->social_links['instagram']))
                                            <a href="{{ Str::startsWith($professional->social_links['instagram'], 'http') ? $professional->social_links['instagram'] : 'https://instagram.com/' . ltrim($professional->social_links['instagram'], '@') }}" target="_blank" class="px-3 py-1 rounded-xl bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 font-extrabold text-[11px] transition-all flex items-center gap-1">
                                                <span>📷</span> <span>Instagram</span>
                                            </a>
                                        @endif
                                        @if(!empty($professional->social_links['linkedin']))
                                            <a href="{{ Str::startsWith($professional->social_links['linkedin'], 'http') ? $professional->social_links['linkedin'] : 'https://linkedin.com/in/' . $professional->social_links['linkedin'] }}" target="_blank" class="px-3 py-1 rounded-xl bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 font-extrabold text-[11px] transition-all flex items-center gap-1">
                                                <span>💼</span> <span>LinkedIn</span>
                                            </a>
                                        @endif
                                        @if(!empty($professional->social_links['facebook']))
                                            <a href="{{ Str::startsWith($professional->social_links['facebook'], 'http') ? $professional->social_links['facebook'] : 'https://facebook.com/' . $professional->social_links['facebook'] }}" target="_blank" class="px-3 py-1 rounded-xl bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 font-extrabold text-[11px] transition-all flex items-center gap-1">
                                                <span>📘</span> <span>Facebook</span>
                                            </a>
                                        @endif
                                        @if(!empty($professional->social_links['tiktok']))
                                            <a href="{{ Str::startsWith($professional->social_links['tiktok'], 'http') ? $professional->social_links['tiktok'] : 'https://tiktok.com/@' . ltrim($professional->social_links['tiktok'], '@') }}" target="_blank" class="px-3 py-1 rounded-xl bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 font-extrabold text-[11px] transition-all flex items-center gap-1">
                                                <span>🎵</span> <span>TikTok / X</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Key Skills & Capabilities (What I Can Help You With) -->
                        <div id="skills-section" class="pt-8 border-t border-slate-100 space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] font-black uppercase tracking-widest text-rose-500 block mb-0.5">CAPABILITIES</span>
                                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Key Skills & What I Can Help You With</h3>
                                </div>
                            </div>

                            <!-- Key Skills Badges -->
                            @if(!empty($professional->skills))
                                @php
                                    $skillsList = is_array($professional->skills) ? $professional->skills : array_map('trim', explode(',', $professional->skills));
                                @endphp
                                <div class="flex flex-wrap gap-2.5">
                                    @foreach($skillsList as $sk)
                                        @if(trim($sk))
                                            <span class="px-4 py-2 rounded-2xl bg-slate-900 text-white font-black text-xs flex items-center gap-2 shadow-xs hover:bg-slate-800 transition-colors">
                                                <span class="text-amber-400">⚡</span> {{ trim($sk) }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <!-- Capabilities / What I Can Help You With Cards Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                @if(!empty($professional->services) && is_array($professional->services) && count($professional->services) > 0)
                                    @foreach($professional->services as $idx => $svc)
                                        @php
                                            $sItem = is_string($svc) ? json_decode($svc, true) : $svc;
                                            if (!is_array($sItem)) {
                                                $sItem = ['title' => is_string($svc) ? $svc : 'Event Service'];
                                            }
                                            $rawIcon = trim($sItem['icon'] ?? '');
                                            $iconDisplay = (mb_strlen($rawIcon) > 0 && mb_strlen($rawIcon) <= 4)
                                                ? $rawIcon
                                                : ($idx % 4 === 0 ? '👥' : ($idx % 4 === 1 ? '👑' : ($idx % 4 === 2 ? '⚡' : '🎙️')));
                                            $taglineDisplay = !empty($sItem['tagline']) ? $sItem['tagline'] : (mb_strlen($rawIcon) > 4 ? $rawIcon : '');
                                            $titleDisplay = $sItem['title'] ?? ($sItem['name'] ?? 'Event Service');
                                            $descDisplay = $sItem['description'] ?? 'Professional crewing service delivered with precision, etiquette, and hospitality excellence.';
                                        @endphp
                                        <div class="p-6 rounded-2xl bg-slate-50/80 border border-slate-200/90 hover:border-amber-400 hover:bg-white transition-all flex flex-col justify-between space-y-4 shadow-2xs group">
                                            <div class="space-y-3">
                                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                                    <div class="w-11 h-11 rounded-2xl bg-slate-900 text-amber-400 flex items-center justify-center text-xl font-black shrink-0 shadow-xs border border-slate-800 group-hover:scale-105 transition-transform">
                                                        {{ $iconDisplay }}
                                                    </div>
                                                    @if(!empty($taglineDisplay))
                                                        <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-900 border border-amber-500/20 text-[10px] font-black uppercase">
                                                            {{ $taglineDisplay }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <h4 class="text-sm font-black text-slate-900 leading-snug">{{ $titleDisplay }}</h4>
                                                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                                                    {{ $descDisplay }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif(!empty($professional->skills))
                                    @php
                                        $skillsListForCards = is_array($professional->skills) ? $professional->skills : array_map('trim', explode(',', $professional->skills));
                                    @endphp
                                    @foreach(array_slice($skillsListForCards, 0, 4) as $idx => $sk)
                                        <div class="p-6 rounded-2xl bg-slate-50/80 border border-slate-200/90 hover:border-amber-400 hover:bg-white transition-all flex flex-col justify-between space-y-4 shadow-2xs group">
                                            <div class="space-y-3">
                                                <div class="w-11 h-11 rounded-2xl bg-slate-900 text-amber-400 flex items-center justify-center text-xl font-black shrink-0 shadow-xs border border-slate-800 group-hover:scale-105 transition-transform">
                                                    {{ $idx % 2 === 0 ? '👥' : '👑' }}
                                                </div>
                                                <h4 class="text-sm font-black text-slate-900">{{ $sk }}</h4>
                                                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                                                    Professional {{ strtolower($sk) }} services tailored for corporate galas, VIP guest reception, and stage management.
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 text-center space-y-2 col-span-full">
                                        <span class="text-2xl block">⚡</span>
                                        <h4 class="text-sm font-black text-slate-900">Services & Capabilities</h4>
                                        <p class="text-xs text-slate-500 max-w-sm mx-auto">No specific services listed yet. Select this crew member to inquire about custom event roles.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Optional Education Background Section -->
                        @if(!empty($professional->education) && is_array($professional->education) && count($professional->education) > 0)
                            <div class="pt-8 border-t border-slate-100 space-y-5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-[11px] font-black uppercase tracking-widest text-amber-600 block mb-0.5">ACADEMIC & QUALIFICATIONS</span>
                                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Education Background</h3>
                                    </div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-900 bg-amber-100 px-3 py-1 rounded-xl border border-amber-300 shadow-2xs">
                                        🎓 Verified Education
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($professional->education as $idx => $edu)
                                        @php
                                            $eItem = is_string($edu) ? json_decode($edu, true) : $edu;
                                            $startEduDisp = trim(($eItem['start_month'] ?? '') . ' ' . ($eItem['start_year'] ?? ($eItem['start_date'] ?? '')));
                                            $endEduDisp = trim(($eItem['end_month'] ?? '') . ' ' . ($eItem['end_year'] ?? ($eItem['end_date'] ?? '')));
                                        @endphp
                                        <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-200/80 flex items-start gap-4 hover:border-amber-400 transition-all shadow-2xs">
                                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 flex items-center justify-center text-lg shrink-0 font-bold">
                                                {{ $idx % 2 === 0 ? '🎓' : '📜' }}
                                            </div>
                                            <div class="space-y-0.5">
                                                <span class="text-[10px] text-amber-900 font-extrabold uppercase tracking-wider block">
                                                    {{ !empty($eItem['degree']) ? $eItem['degree'] : ($eItem['field'] ?? 'Education & Training') }}
                                                </span>
                                                <h4 class="text-xs sm:text-sm font-black text-slate-900">{{ $eItem['institution'] ?? 'Academic Institution' }}</h4>
                                                @if($startEduDisp || $endEduDisp)
                                                    <p class="text-[11px] text-slate-600 font-medium pt-0.5 flex items-center gap-1">
                                                        <span>📅</span>
                                                        <span>{{ $startEduDisp }} {{ $endEduDisp ? '- ' . $endEduDisp : '' }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </section>

                    <!-- SECTION 2: WORK EXPERIENCE (OBSIDIAN DARK CONTAINER) -->
                    <section id="experience-section" class="scroll-mt-28 bg-slate-950 text-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-800 space-y-6">
                        <div>
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">Work Experience</h2>
                                <span class="text-[10px] font-black uppercase tracking-wider text-rose-400 bg-rose-500/10 px-3.5 py-1 rounded-full border border-rose-500/20">
                                    Proven Track Record
                                </span>
                            </div>
                            <div class="w-16 h-1 bg-gradient-to-r from-rose-500 to-amber-400 rounded-full mt-2"></div>
                        </div>

                        <div class="space-y-4">
                            @if(!empty($professional->experience_records) && is_array($professional->experience_records) && count($professional->experience_records) > 0)
                                @foreach($professional->experience_records as $exp)
                                    @php
                                        $xItem = is_string($exp) ? json_decode($exp, true) : $exp;
                                        $startDisp = trim(($xItem['start_month'] ?? '') . ' ' . ($xItem['start_year'] ?? ($xItem['start_date'] ?? '2021')));
                                        $endDisp = trim(($xItem['end_month'] ?? '') . ' ' . ($xItem['end_year'] ?? ($xItem['end_date'] ?? 'Present')));
                                    @endphp
                                    <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-rose-500/40 transition-all space-y-2">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <div>
                                                <h3 class="text-base font-black text-white">{{ $xItem['role'] ?? 'Lead Usher & Host' }}</h3>
                                                <p class="text-xs text-amber-400 font-extrabold">{{ $xItem['employer'] ?? 'Safaricom PLC' }}</p>
                                            </div>
                                            <span class="px-3 py-1 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 text-white font-extrabold text-[10px] uppercase shadow-xs">
                                                {{ $startDisp }} - {{ $endDisp }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-300 font-medium leading-relaxed">
                                            📍 {{ $professional->city ?: 'Nairobi' }}, Kenya • {{ $xItem['responsibilities'] ?? 'Coordinated guest seating, stage protocol, and VIP lounge hospitality for annual conferences.' }}
                                        </p>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 text-center space-y-2">
                                    <span class="text-2xl block">💼</span>
                                    <h3 class="text-sm font-black text-white">Work Experience</h3>
                                    <p class="text-xs text-slate-400 max-w-sm mx-auto">No work experience records have been added to this profile yet.</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <!-- SECTION 3: PORTFOLIO GALLERY / PROOF OF PROFESSIONALISM -->
                    <section id="portfolio-section" class="scroll-mt-28 bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[11px] font-black uppercase tracking-widest text-rose-500 block mb-1">EVIDENCE</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Proof of Professionalism</h2>
                            </div>
                        </div>

                        <!-- Gallery Photos Grid -->
                        @php
                            $rawGallery = is_array($professional->gallery_photos) ? $professional->gallery_photos : [];
                            $galleryPhotos = array_values(array_unique(array_filter($rawGallery)));
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @if(count($galleryPhotos) > 0)
                                @foreach($galleryPhotos as $idx => $gPhoto)
                                    @php $gUrl = get_storage_url($gPhoto); @endphp
                                    <div class="relative h-48 rounded-2xl overflow-hidden border border-slate-200 group cursor-pointer shadow-2xs" onclick="openLightbox('{{ $gUrl }}', 'Event Project')">
                                        <img src="{{ $gUrl }}" alt="Event photo" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-slate-950/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white font-black text-xs gap-1.5">
                                            <span>🔍</span> <span>View Photo</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200/90 text-center space-y-2 sm:col-span-3">
                                    <span class="text-3xl block">🖼️</span>
                                    <h3 class="text-sm font-black text-slate-900">Portfolio Gallery</h3>
                                    <p class="text-xs text-slate-500 max-w-sm mx-auto">No event portfolio photos uploaded yet.</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <!-- SECTION 4: CLIENT REVIEWS & TESTIMONIALS -->
                    <section id="reviews-section" class="scroll-mt-28 bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                            <div>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">What Clients Say</h2>
                                <p class="text-xs text-slate-500 font-medium">Verified reviews from event organizers who hired {{ $professional->full_name }}.</p>
                            </div>

                            @if($professional->reviews_count > 0 && $professional->average_rating > 0)
                                <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-4 py-2 shrink-0 shadow-2xs">
                                    <span class="text-2xl font-black text-slate-900 leading-none">{{ number_format($professional->average_rating, 1) }}</span>
                                    <div>
                                        <div class="text-amber-500 text-xs font-black">★★★★★</div>
                                        <span class="text-[10px] text-amber-900 font-extrabold">{{ $professional->reviews_count }} Verified {{ Str::plural('Review', $professional->reviews_count) }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-xs text-slate-500 font-bold shrink-0">
                                    No reviews yet
                                </div>
                            @endif
                        </div>

                        <!-- Reviews Cards Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @php
                                $visibleReviews = $professional->visibleReviews;
                            @endphp
                            @if($visibleReviews->count() > 0)
                                @foreach($visibleReviews as $rev)
                                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 shadow-2xs">
                                        <div class="text-rose-500 text-xs font-black">
                                            @for($s = 1; $s <= 5; $s++)
                                                {{ $s <= $rev->rating ? '★' : '☆' }}
                                            @endfor
                                        </div>
                                        <p class="text-xs text-slate-700 font-medium leading-relaxed italic">
                                            "{{ $rev->comment }}"
                                        </p>
                                        <div class="flex items-center gap-2.5 pt-2 border-t border-slate-200/60">
                                            <div class="w-8 h-8 rounded-full bg-slate-900 text-amber-400 font-black text-xs flex items-center justify-center">
                                                {{ strtoupper(substr($rev->client_name ?? 'C', 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-black text-slate-900">{{ $rev->client_name ?? 'Verified Client' }}</h4>
                                                <span class="text-[10px] text-slate-400 font-bold block">{{ $rev->client_company ?: ($rev->event_name ?: 'Event Organizer') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="sm:col-span-2 p-8 rounded-2xl bg-slate-50 border border-slate-200/90 text-center space-y-2">
                                    <span class="text-3xl block">⭐</span>
                                    <h4 class="text-sm font-black text-slate-900">No Client Reviews Yet</h4>
                                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                                        When clients complete event bookings with {{ $professional->full_name }}, their verified reviews will appear here.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </section>

                </div>

                <!-- RIGHT COLUMN: Sticky Booking & Stats Sidebar (Col Span 4) -->
                <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-6">

                    <!-- CARD 1: Quick Booking Form Widget -->
                    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-xl space-y-5" id="booking-sidebar">

                        <!-- Chat Speech Bubble Header -->
                        <div class="flex items-start gap-3 bg-slate-950 text-white border border-slate-800 rounded-2xl p-4 shadow-md">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="{{ $professional->full_name }}" class="w-11 h-11 rounded-xl object-cover border-2 border-amber-400 shadow-xs shrink-0">
                            @else
                                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-400 to-rose-500 text-slate-950 font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                                    {{ strtoupper(substr($professional->full_name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="space-y-1">
                                <h3 class="text-xs font-black text-amber-400">Hi, I'm {{ strtok($professional->full_name, ' ') }}. 👋</h3>
                                <p class="text-[11px] text-slate-300 font-medium leading-relaxed">
                                    "Tell me a little about your event and what you need. I'd be happy to see if I'm the right fit!"
                                </p>
                            </div>
                        </div>

                        <!-- Quick Booking Inputs Form -->
                        <form method="GET" action="{{ route('crew.select', $professional) }}" class="space-y-4">

                            <!-- Question 1: What service do you need -->
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1">
                                    WHAT SERVICE DO YOU NEED?
                                </label>
                                <select name="category" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-3 text-xs text-slate-900 font-bold outline-none focus:border-rose-500 transition-all cursor-pointer">
                                    <option value="{{ $professional->category }}">{{ $professional->category }}</option>
                                    <option value="Corporate Ushering">Corporate Ushering</option>
                                    <option value="VIP & Protocol Assistance">VIP & Protocol Assistance</option>
                                    <option value="Registration Desk">Registration & Ticketing</option>
                                </select>
                            </div>

                            <!-- Question 2: Date & Location -->
                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1 flex items-center justify-between">
                                        <span>EVENT DATE</span>
                                        <span id="crew_days_badge" class="text-[9px] font-black text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">1 Day</span>
                                    </label>
                                    <input type="text" id="crew_show_event_date" name="event_date" value="{{ date('d M Y', strtotime('+3 days')) }} (1 Day)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs text-slate-900 font-bold outline-none focus:border-rose-500 cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1">
                                        LOCATION
                                    </label>
                                    <input type="text" name="location" value="{{ $professional->city ?: 'Nairobi' }}, Kenya" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs text-slate-900 font-bold outline-none focus:border-rose-500">
                                </div>
                            </div>

                            <!-- Question 3: Shift Duration -->
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1">
                                    HOW LONG DO YOU NEED ME?
                                </label>
                                <select name="shift_duration" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-3 text-xs text-slate-900 font-bold outline-none focus:border-rose-500 transition-all cursor-pointer">
                                    <option value="1_day" selected>1 Day Shift</option>
                                    <option value="2_days">2 Days Event</option>
                                    <option value="3_days">3 Days Event</option>
                                    <option value="4_days">4 Days Event</option>
                                    <option value="5_days">5 Days Event</option>
                                    <option value="multi_day">6+ Days (Multi-Day Package)</option>
                                </select>
                            </div>

                            <!-- Question 4: Optional Notes -->
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1">
                                    EVENT DETAILS / NOTES (OPTIONAL)
                                </label>
                                <textarea name="requirements" rows="2" placeholder="Tell me more about your event..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 outline-none focus:border-rose-500 font-medium"></textarea>
                            </div>

                            <!-- Submit CTA Buttons -->
                            <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 hover:opacity-95 text-white font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-pink-500/25 flex items-center justify-center gap-2 cursor-pointer">
                                <span>HIRE {{ strtoupper(strtok($professional->full_name, ' ')) }} NOW →</span>
                            </button>

                            <button type="button" onclick="addCrewToTeamBasket({{ json_encode([
                                'id' => $professional->id,
                                'full_name' => $professional->full_name,
                                'profile_photo_url' => $photoUrl ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400',
                                'category' => $professional->category ?: 'Event Usher'
                            ]) }})" class="w-full py-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 hover:bg-amber-500 hover:text-slate-950 font-black text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
                                <span>⊕ ADD TO MY TEAM BASKET</span>
                            </button>

                            <div class="text-center pt-1">
                                <span class="text-[10px] text-slate-400 font-semibold flex items-center justify-center gap-1">
                                    <span>🔒</span> <span>Direct verified crewing booking</span>
                                </span>
                            </div>

                        </form>

                    </div>

                    <!-- CARD 2: Quick Stats Cards (2x2 Grid) -->
                    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm space-y-4">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                            Quick Stats
                        </h4>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <div class="flex items-center gap-2 text-slate-900 font-black text-base">
                                    <span>💼</span>
                                    <span>{{ $professional->completed_gigs_count }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mt-0.5">Events done</span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200/80">
                                <div class="flex items-center gap-2 text-amber-900 font-black text-base">
                                    <span>★</span>
                                    <span>{{ $professional->reviews_count > 0 ? number_format($professional->average_rating, 1) : '4.9' }}</span>
                                </div>
                                <span class="text-[10px] text-amber-800 font-bold uppercase tracking-wider block mt-0.5">Avg. rating</span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <div class="flex items-center gap-2 text-slate-900 font-black text-base">
                                    <span>⚡</span>
                                    <span>{{ $professional->on_time_rate }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mt-0.5">On-time rate</span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200/80">
                                <div class="flex items-center gap-2 text-rose-950 font-black text-base">
                                    <span>🏆</span>
                                    <span>{{ $professional->experience_years ? $professional->experience_years . '+' : '1+' }}</span>
                                </div>
                                <span class="text-[10px] text-rose-800 font-bold uppercase tracking-wider block mt-0.5">Years exp</span>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 3: Personal Quote Box -->
                    <div class="p-6 rounded-3xl bg-amber-50/80 border border-amber-200/90 space-y-3 text-center shadow-2xs">
                        <span class="text-3xl text-amber-500 font-black leading-none block">“</span>
                        <p class="text-xs font-bold text-amber-950 italic leading-relaxed">
                            "{{ $professional->highlight_quote ?: "Great events are built by great people. Let's create something amazing together!" }}"
                        </p>
                        <div class="pt-2 border-t border-amber-200/60">
                            <span class="text-xs font-black text-slate-900 block">— {{ $professional->full_name }}</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <!-- Lightbox Modal for Portfolio Photos -->
    <div id="lightbox-modal" class="fixed inset-0 z-50 bg-slate-950/90 hidden items-center justify-center p-4 backdrop-blur-md" onclick="closeLightbox()">
        <div class="relative max-w-4xl w-full bg-slate-900 rounded-3xl overflow-hidden border border-slate-800 p-2 shadow-2xl" onclick="event.stopPropagation()">
            <button onclick="closeLightbox()" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-white/20 text-white font-black text-sm flex items-center justify-center hover:bg-white/40 transition-colors">✕</button>
            <img id="lightbox-img" src="" alt="Gallery Image" class="w-full h-auto max-h-[80vh] object-contain rounded-2xl">
            <p id="lightbox-caption" class="text-center text-xs font-extrabold text-amber-400 py-3"></p>
        </div>
    </div>

    <script>
    function copyProfileLink() {
        navigator.clipboard.writeText(window.location.href);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Profile link copied to clipboard!',
                showConfirmButton: false,
                timer: 2500
            });
        } else {
            alert('Profile link copied to clipboard!');
        }
    }

    function openLightbox(url, caption) {
        document.getElementById('lightbox-img').src = url;
        document.getElementById('lightbox-caption').innerText = caption;
        document.getElementById('lightbox-modal').classList.remove('hidden');
        document.getElementById('lightbox-modal').classList.add('flex');
    }

    function closeLightbox() {
        document.getElementById('lightbox-modal').classList.add('hidden');
        document.getElementById('lightbox-modal').classList.remove('flex');
    }
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof flatpickr !== 'undefined') {
            flatpickr('#crew_show_event_date', {
                mode: 'range',
                dateFormat: 'd M Y',
                minDate: new Date().fp_incr(1),
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const diffMs = Math.abs(selectedDates[1].getTime() - selectedDates[0].getTime());
                        const daysCount = Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1;
                        const startStr = instance.formatDate(selectedDates[0], 'd M Y');
                        const endStr = instance.formatDate(selectedDates[1], 'd M Y');
                        instance.input.value = `${startStr} - ${endStr} (${daysCount} ${daysCount === 1 ? 'Day' : 'Days'})`;
                        const badge = document.getElementById('crew_days_badge');
                        if (badge) badge.textContent = `${daysCount} ${daysCount === 1 ? 'Day' : 'Days'}`;
                    } else if (selectedDates.length === 1) {
                        const startStr = instance.formatDate(selectedDates[0], 'd M Y');
                        instance.input.value = `${startStr} (1 Day)`;
                        const badge = document.getElementById('crew_days_badge');
                        if (badge) badge.textContent = '1 Day';
                    }
                }
            });
        }

        // Auto ScrollSpy Active Tab Highlight
        const sections = document.querySelectorAll('section[id]');
        const navTabs = document.querySelectorAll('.nav-tab-btn');

        function highlightActiveNavTab() {
            let scrollY = window.pageYOffset;
            sections.forEach(current => {
                const sectionHeight = current.offsetHeight;
                const sectionTop = current.offsetTop - 150;
                const sectionId = current.getAttribute('id');

                if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                    navTabs.forEach(tab => {
                        tab.classList.remove('bg-slate-900', 'text-white', 'shadow-xs');
                        tab.classList.add('hover:bg-slate-100', 'text-slate-600');
                        if (tab.getAttribute('href') === `#${sectionId}`) {
                            tab.classList.remove('hover:bg-slate-100', 'text-slate-600');
                            tab.classList.add('bg-slate-900', 'text-white', 'shadow-xs');
                        }
                    });
                }
            });
        }

        window.addEventListener('scroll', highlightActiveNavTab);
    });
    </script>
@endsection
