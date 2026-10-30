@extends('layouts.app')

@section('content')
    @php
        $photoUrl = $professional->profile_photo ? get_storage_url($professional->profile_photo) : null;
        $coverPhoto = $professional->cover_photo ? get_storage_url($professional->cover_photo) : asset('images/hero_event_ushers.jpg');
        
        // Name: Prefer full name or displayName
        $rawUsername = !empty($professional->username) ? ltrim(trim($professional->username), '@') : null;
        $nameParts = preg_split('/\s+/', trim($professional->full_name));
        $firstName = $nameParts[0] ?? '';
        $displayName = $professional->full_name ?: ($rawUsername ?: ($firstName ?: 'Crew Member'));

        $currency = $professional->currency ?: 'KES';
        $dailyChargeNum = (float)($professional->full_day_rate ?: ($professional->one_day_rate ?: 2000));
        if ($dailyChargeNum <= 5000) {
            $bandTier = 1;
            $tierSymbol = '£ $';
        } elseif ($dailyChargeNum <= 8000) {
            $bandTier = 2;
            $tierSymbol = '£ $$';
        } elseif ($dailyChargeNum <= 12000) {
            $bandTier = 3;
            $tierSymbol = '£ $$$';
        } elseif ($dailyChargeNum <= 50000) {
            $bandTier = 4;
            $tierSymbol = '£ $$$$';
        } else {
            $bandTier = 5;
            $tierSymbol = '£ $$$$$';
        }
        $locationParts = array_filter([$professional->city, $professional->country]);
        $locationStr = count($locationParts) > 0 ? implode(', ', $locationParts) : ($professional->country ?: 'Nairobi, Kenya');

        $isFavorited = auth()->check() ? $professional->favoritedBy()->where('user_id', auth()->id())->exists() : false;

        // Skills & Expertises from backend
        $rawSkills = is_array($professional->skills) ? $professional->skills : array_map('trim', explode(',', $professional->skills ?: ''));
        $featuredSkillsArr = is_array($professional->featured_skills) ? $professional->featured_skills : [];
        $allSkillsList = array_values(array_unique(array_filter(array_merge($rawSkills, $featuredSkillsArr))));

        $rawExpertises = is_array($professional->expertises) ? $professional->expertises : [];
        $savedExpertises = [];
        foreach ($rawExpertises as $expItem) {
            $exp = is_string($expItem) ? json_decode($expItem, true) : $expItem;
            if (is_array($exp) && !empty(trim($exp['name'] ?? ''))) {
                $savedExpertises[] = $exp;
            }
        }

        // Resolve exactly 4 expertise cards and 3 key skills each, plus 3 core skills
        $resolveKeySkills = function($card, $category, $allSkills, $cardIndex) {
            $explicit = $card['key_skills'] ?? ($card['skills'] ?? []);
            if (is_string($explicit)) {
                $explicit = array_map('trim', explode(',', $explicit));
            }
            $skills = array_values(array_filter((array)$explicit));
            if (count($skills) >= 3) {
                return array_slice($skills, 0, 3);
            }

            $name = $card['name'] ?? '';
            $desc = $card['description'] ?? '';
            $text = strtolower($name . ' ' . $desc);

            $matched = [];
            if (str_contains($text, 'vip') || str_contains($text, 'protocol') || str_contains($text, 'diplomatic') || str_contains($text, 'hospitality')) {
                $matched = ['VIP Protocol & Etiquette', 'Executive Guest Escort', 'High-Profile Seating'];
            } elseif (str_contains($text, 'ticket') || str_contains($text, 'scan') || str_contains($text, 'register') || str_contains($text, 'badge') || str_contains($text, 'pos') || str_contains($text, 'access')) {
                $matched = ['Badge Verification', 'Fast-Track Check-In', 'Access Control Scanning'];
            } elseif (str_contains($text, 'crowd') || str_contains($text, 'seat') || str_contains($text, 'hall') || str_contains($text, 'logistics') || str_contains($text, 'flow') || str_contains($text, 'direct')) {
                $matched = ['Auditorium Flow Control', 'Crowd Guidance', 'Queue Management'];
            } elseif (str_contains($text, 'stage') || str_contains($text, 'speaker') || str_contains($text, 'floor') || str_contains($text, 'mic') || str_contains($text, 'mc') || str_contains($text, 'host')) {
                $matched = ['Stage Assistance', 'Speaker Coordination', 'Mic Running & Tech Cue'];
            } elseif (str_contains($text, 'video') || str_contains($text, 'film') || str_contains($text, 'cinema') || str_contains($text, 'stream')) {
                $matched = ['Cinematic 4K Video', 'Gimbal Tracking', 'Multi-Angle Coverage'];
            } elseif (str_contains($text, 'photo') || str_contains($text, 'camera') || str_contains($text, 'portrait') || str_contains($text, 'image')) {
                $matched = ['Event Photography', 'Framing & Lighting', 'Portrait Capture'];
            } elseif (str_contains($text, 'audio') || str_contains($text, 'sound') || str_contains($text, 'mix') || str_contains($text, 'acoustic')) {
                $matched = ['FOH Live Mixing', 'Dante Audio Network', 'Acoustic Tuning'];
            } elseif (str_contains($text, 'light') || str_contains($text, 'dmx') || str_contains($text, 'rigging')) {
                $matched = ['GrandMA3 Programming', 'DMX Protocols', 'Stage Illumination'];
            }

            foreach ($matched as $m) {
                if (!in_array($m, $skills)) {
                    $skills[] = $m;
                }
                if (count($skills) >= 3) break;
            }

            $isMedia = (bool)preg_match('/photo|video|camera|film/i', $category ?? '');
            if ($isMedia) {
                $catPools = [
                    0 => ['Event Photography', 'Framing & Lighting', 'Portrait Capture'],
                    1 => ['Cinematic 4K Video', 'Gimbal Tracking', 'Multi-Angle Coverage'],
                    2 => ['Same-Day Edits', 'Color Grading', 'Batch Processing'],
                    3 => ['Studio Strobe Lighting', 'Red Carpet Staging', 'Backdrop Alignment'],
                ];
            } else {
                $catPools = [
                    0 => ['VIP Protocol & Etiquette', 'Executive Guest Escort', 'High-Profile Seating'],
                    1 => ['Badge Verification', 'Fast-Track Check-In', 'Access Control Scanning'],
                    2 => ['Auditorium Flow Control', 'Crowd Guidance', 'Queue Management'],
                    3 => ['Stage Assistance', 'Speaker Coordination', 'Mic Running & Tech Cue'],
                ];
            }

            $pool = $catPools[$cardIndex % 4];
            foreach ($pool as $ps) {
                if (!in_array($ps, $skills)) {
                    $skills[] = $ps;
                }
                if (count($skills) >= 3) break;
            }

            return array_slice($skills, 0, 3);
        };

        $isMediaCat = (bool)preg_match('/photo|video|camera|film/i', $professional->category ?? '');
        if ($isMediaCat) {
            $fallbackExpertises = [
                [
                    'name' => 'High-End Event Photography',
                    'icon' => '📸',
                    'description' => 'Capturing crisp keynote moments, VIP portraits, high-energy attendee interactions, and atmosphere shots with professional prime glass.',
                    'key_skills' => ['Event Photography', 'Framing & Lighting', 'Portrait Capture']
                ],
                [
                    'name' => 'Cinematic Video & Live Coverage',
                    'icon' => '🎥',
                    'description' => 'Dynamic 4K multi-cam filming, smooth gimbal tracking of speakers and stage presentations, and highlight reel production.',
                    'key_skills' => ['Live Video Capture', 'Gimbal Stabilization', 'Multi-Angle Coverage']
                ],
                [
                    'name' => 'Rapid On-Site Editing & Delivery',
                    'icon' => '⚡',
                    'description' => 'Fast turnaround processing for same-day press releases, social media broadcast packages, and high-resolution digital galleries.',
                    'key_skills' => ['Same-Day Edits', 'Color Grading', 'Batch Processing']
                ],
                [
                    'name' => 'Lighting & Studio Staging',
                    'icon' => '💡',
                    'description' => 'Custom off-camera flash setups, backdrop alignment, red-carpet photo booths, and ambient lighting optimization.',
                    'key_skills' => ['Studio Strobe Lighting', 'Red Carpet Setups', 'Backdrop Management']
                ]
            ];
        } else {
            $fallbackExpertises = [
                [
                    'name' => 'VIP Protocol & Diplomatic Ushering',
                    'icon' => '👑',
                    'description' => 'Managing high-profile guests, ministerial delegations, seating etiquette, and discreet executive lounge hospitality.',
                    'key_skills' => ['VIP Protocol & Etiquette', 'Executive Guest Escort', 'High-Profile Seating']
                ],
                [
                    'name' => 'Guest Registration & Access Control',
                    'icon' => '📋',
                    'description' => 'Fast-track badge accreditation, digital ticket & RFID verification, and welcoming delegates at front reception counters.',
                    'key_skills' => ['Badge Verification', 'Fast-Track Check-In', 'Access Control Scanning']
                ],
                [
                    'name' => 'Crowd Logistics & Seating Directing',
                    'icon' => '👥',
                    'description' => 'Auditorium crowd navigation, reserved section coordination, session transitions, and venue exit management.',
                    'key_skills' => ['Auditorium Flow Control', 'Crowd Guidance', 'Queue Management']
                ],
                [
                    'name' => 'Stage Coordination & Live Floor Support',
                    'icon' => '🎤',
                    'description' => 'Floor microphone handling, keynote speaker assistance, awards ceremony queueing, and rapid liaison with event directorship.',
                    'key_skills' => ['Stage Assistance', 'Speaker Coordination', 'Mic Running & Tech Cue']
                ]
            ];
        }

        $displayExpertises = [];
        foreach (array_slice($savedExpertises, 0, 4) as $idx => $sExp) {
            $rawDesc = trim($sExp['description'] ?? '');
            if (empty($rawDesc)) {
                $rawDesc = $fallbackExpertises[$idx % 4]['description'];
            }
            $displayExpertises[] = [
                'name' => !empty(trim($sExp['name'] ?? '')) ? $sExp['name'] : $fallbackExpertises[$idx % 4]['name'],
                'icon' => !empty(trim($sExp['icon'] ?? '')) ? $sExp['icon'] : $fallbackExpertises[$idx % 4]['icon'],
                'image' => !empty($sExp['image']) ? get_storage_url($sExp['image']) : null,
                'description' => $rawDesc,
                'key_skills' => $resolveKeySkills($sExp, $professional->category, $allSkillsList, $idx),
            ];
        }

        while (count($displayExpertises) < 4) {
            $idx = count($displayExpertises);
            $fb = $fallbackExpertises[$idx % 4];
            $displayExpertises[] = [
                'name' => $fb['name'],
                'icon' => $fb['icon'],
                'image' => null,
                'description' => $fb['description'],
                'key_skills' => $fb['key_skills'],
            ];
        }

        // Fixed 3 Core Skills
        $fixedCoreSkills = [];
        foreach ($allSkillsList as $s) {
            $clean = trim($s);
            if (!empty($clean) && !in_array($clean, $fixedCoreSkills)) {
                $fixedCoreSkills[] = $clean;
            }
            if (count($fixedCoreSkills) === 3) break;
        }
        $defaultCoreSkills = $isMediaCat
            ? ['High-Res Photography', '4K Video Recording', 'Lighting Calibration']
            : ['VIP Guest Hospitality', 'Badge Registration', 'Crowd Coordination'];

        foreach ($defaultCoreSkills as $dfs) {
            if (count($fixedCoreSkills) >= 3) break;
            if (!in_array($dfs, $fixedCoreSkills)) {
                $fixedCoreSkills[] = $dfs;
            }
        }
        $fixedCoreSkills = array_slice($fixedCoreSkills, 0, 3);

        // Subtitle summary for roles/expertises
        if (!empty($displayExpertises) && count($displayExpertises) > 0) {
            $expNames = array_slice(array_column($displayExpertises, 'name'), 0, 3);
            $expertisesSummary = implode(', ', $expNames);
        } elseif (!empty($allSkillsList) && count($allSkillsList) > 0) {
            $expertisesSummary = implode(', ', array_slice($allSkillsList, 0, 3));
        } else {
            $expertisesSummary = $professional->category ?: 'Sound Engineer, Light Tech';
        }

        // Top skills for pill badges row
        $topSkills = array_slice($allSkillsList, 0, 3);
        if (empty($topSkills)) {
            $topSkills = $fixedCoreSkills;
        }

        $expYears = (int)($professional->experience_years ?: 2);
        $bandNum = min(5, max(1, (int)ceil($expYears / 2)));

        // Gallery Photos from backend
        $rawGallery = is_array($professional->gallery_photos) ? $professional->gallery_photos : [];
        $portfolioPhotos = array_values(array_unique(array_filter($rawGallery)));
        $galleryItems = [];
        foreach(array_slice($portfolioPhotos, 0, 16) as $idx => $gPhoto) {
            $galleryItems[] = [
                'url' => get_storage_url($gPhoto),
                'caption' => $displayName . ' — Portfolio Project #' . ($idx + 1),
            ];
        }

        // Education items from backend
        $eduItems = is_array($professional->education) ? array_values(array_filter($professional->education)) : [];

        // Experience timeline records from backend
        $expRecords = is_array($professional->experience_records) ? array_values(array_filter($professional->experience_records)) : [];

        // Reviews and dynamic rating from backend
        $reviews = $professional->visibleReviews ?? collect();
        $reviewsCount = $professional->reviews_count;
        $avgRating = (float)$professional->average_rating;

        // Publicly verified work references
        $rawReferees = is_array($professional->referees) ? $professional->referees : [];
        $publicVerifiedReferees = [];
        foreach ($rawReferees as $ref) {
            $item = is_string($ref) ? json_decode($ref, true) : $ref;
            if (is_array($item)) {
                $status = strtolower($item['status'] ?? '');
                $isConfirmed = ($status === 'verified' || !empty($item['is_confirmed']));
                if ($isConfirmed && !empty($item['name'])) {
                    $publicVerifiedReferees[] = $item;
                }
            }
        }

        // Highlight Quote from backend
        $highlightQuote = !empty(trim($professional->highlight_quote ?? '')) && trim($professional->highlight_quote) !== 'dsyhbh dosb'
            ? trim($professional->highlight_quote)
            : "Take away my factories, but leave my people, and soon we will have a new and better factory";

        // Tier 1: Today (My Update - Up to 200 characters)
        $todayHighlight = !empty(trim($professional->my_update ?? '')) && !str_starts_with(trim($professional->my_update), 'Writing an "about myself"')
            ? trim($professional->my_update)
            : "Good sound starts long before the first microphone goes live. Preparation, patching and a proper soundcheck make all the difference.";
        $todayHighlight = \Illuminate\Support\Str::limit($todayHighlight, 200, '');

        // Today formatted date (Matching 24.10.26 format from user mockup)
        $todayDateFormatted = $professional->updated_at ? $professional->updated_at->format('d.m.y') : '24.10.26';

        // Tier 2: Biography paragraphs from backend (Aim for 80-150 words)
        $bioRaw = trim($professional->about ?? '');
        $bioParagraphs = [];
        if (!empty($bioRaw)) {
            $bioParagraphs = array_values(array_filter(array_map('trim', preg_split('/\r\n\r\n|\n\n|\r\r/', $bioRaw))));
        }
        if (empty($bioParagraphs)) {
            $bioParagraphs = [
                "A sound engineer with experience in live events, broadcasting, studio production and cruise entertainment. Dante Level 2 certified, with a background in Electrical and Electronics Engineering, I combine technical knowledge with a practical ear for clear, balanced sound.",
                "My journey includes audio engineering and studio management at Ratina Studios, broadcast and event roles with Homeboyz, and work as an entertainment technician. I'm comfortable troubleshooting technical challenges, working closely with performers and production teams, and staying calm under pressure.",
                "For me, good sound means every word is heard, every performance connects, and the audience enjoys the moment."
            ];
        }

        // Tier 3: Let's Connect (Up to 200 characters)
        $rawConnect = trim($professional->lets_connect ?? '');
        $letsConnectText = (!empty($rawConnect) && !str_starts_with($rawConnect, 'Aut labore'))
            ? $rawConnect
            : "Available for live tours, corporate galas, festivals, and broadcast sound engineering across East Africa. Let's connect and ensure your event sounds unforgettable!";
        $letsConnectText = \Illuminate\Support\Str::limit($letsConnectText, 200, '');

        // Where I Work cities list & flag resolution
        $citiesArr = is_array($professional->other_cities) ? $professional->other_cities : (is_string($professional->other_cities) ? array_map('trim', explode(',', $professional->other_cities)) : []);
        $prefLocsArr = is_array($professional->preferred_locations) ? $professional->preferred_locations : (is_string($professional->preferred_locations) ? array_map('trim', explode(',', $professional->preferred_locations)) : []);
        $allWorkingCities = array_values(array_unique(array_filter(array_merge([$professional->city ?: 'Nairobi'], $citiesArr, $prefLocsArr))));
        
        $workingCities = [];
        foreach($allWorkingCities as $wCity) {
            $cleaned = trim(preg_replace('/^and\s+/i', '', $wCity));
            if (!empty($cleaned) && !in_array(strtolower($cleaned), ['tanzania', 'uganda', 'south sudan', 'ethiopia', 'somalia'])) {
                $workingCities[] = $cleaned;
            }
        }
        $workingCities = array_values(array_unique($workingCities));
        if (empty($workingCities) || count($workingCities) < 4) {
            // Guarantee at least the standard 4 working cities from mockup: Nairobi, Mombasa, Kisumu, Nyeri
            $workingCities = array_values(array_unique(array_merge($workingCities, ['Nairobi', 'Mombasa', 'Kisumu', 'Nyeri'])));
        }

        $getFlagUrl = function($loc) {
            $clean = strtolower(trim($loc));
            if (str_contains($clean, 'nairobi')) {
                return asset('images/flags/nairobi.svg');
            } elseif (str_contains($clean, 'mombasa')) {
                return asset('images/flags/mombasa.png');
            } elseif (str_contains($clean, 'kisumu')) {
                return asset('images/flags/kisumu.png');
            } elseif (str_contains($clean, 'nyeri')) {
                return asset('images/flags/nyeri.gif');
            } elseif (str_contains($clean, 'nakuru')) {
                return asset('images/flags/nakuru.png');
            } elseif (str_contains($clean, 'machakos')) {
                return asset('images/flags/machakos.png');
            } elseif (str_contains($clean, 'kilifi')) {
                return asset('images/flags/kilifi.png');
            } elseif (str_contains($clean, 'uganda')) {
                return asset('images/flags/uganda.svg');
            } elseif (str_contains($clean, 'tanzania')) {
                return asset('images/flags/tanzania.svg');
            } elseif (str_contains($clean, 'rwanda')) {
                return asset('images/flags/rwanda.svg');
            }
            return asset('images/flags/kenya.svg');
        };

        // Languages & Assignment types
        $langsArr = is_array($professional->languages) ? $professional->languages : (is_string($professional->languages) ? array_map('trim', explode(',', $professional->languages)) : []);
        $langsStr = implode(' • ', array_filter($langsArr));

        $assignTypesArr = is_array($professional->assignment_types) ? $professional->assignment_types : (is_string($professional->assignment_types) ? array_map('trim', explode(',', $professional->assignment_types)) : []);
        $assignTypesStr = implode(' • ', array_filter($assignTypesArr));
    @endphp

    <style>
        .crew-side-padding {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        @media (min-width: 640px) {
            .crew-side-padding {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }
        @media (min-width: 1024px) {
            .crew-side-padding {
                padding-left: 8% !important;
                padding-right: 8% !important;
            }
        }

        /* Ultra-thin hairline borders for Key Skills & Expertise section (defeats Bootstrap !important override) */
        #skills-section {
            border: 0.5px solid rgba(255, 255, 255, 0.08) !important;
        }
        #skills-section .skills-thin-border-bottom {
            border-bottom: 0.5px solid rgba(255, 255, 255, 0.08) !important;
        }
        #skills-section .skills-thin-border-top {
            border-top: 0.5px solid rgba(255, 255, 255, 0.08) !important;
        }
        #skills-section .skills-card {
            border: 0.5px solid rgba(255, 255, 255, 0.12) !important;
        }
        #skills-section .skills-card:hover {
            border-color: rgba(245, 158, 11, 0.4) !important;
        }
        #skills-section .skills-pill {
            border: 0.5px solid rgba(255, 255, 255, 0.18) !important;
        }
        #skills-section .skills-pill:hover {
            border-color: rgba(245, 158, 11, 0.45) !important;
        }
        #skills-section .skills-core-pill {
            border: 0.5px solid rgba(255, 255, 255, 0.18) !important;
        }
        #skills-section .skills-core-pill:hover {
            border-color: rgba(245, 158, 11, 0.45) !important;
        }
        #skills-section .skills-icon-box {
            border: 0.5px solid rgba(255, 255, 255, 0.12) !important;
        }
    </style>

    <div class="bg-slate-50 text-slate-900 min-h-screen font-sans selection:bg-[#FF6B00] selection:text-white pb-28">

        <!-- Flash Error / Success Notifications -->
        @if(session('error'))
            <div class="w-full crew-side-padding pt-4">
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
            <div class="w-full crew-side-padding pt-4">
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs animate-fadeIn">
                    <div class="flex items-center gap-2">
                        <span class="text-base">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 font-black text-xs hover:text-emerald-800">✕</button>
                </div>
            </div>
        @endif

        <!-- 1. COVER PHOTO BANNER -->
        <div class="w-full crew-side-padding pt-4">
        <div class="relative h-60 sm:h-72 md:h-80 w-full overflow-hidden rounded-t-3xl bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950">
            <img src="{{ $coverPhoto }}" alt="{{ $displayName }} Cover Header" class="w-full h-full object-cover object-center opacity-85 transform hover:scale-105 transition-transform duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-[#090E1A] via-[#090E1A]/40 to-black/30"></div>

            <!-- Glowing Available Marker (Top-Left of Hero, end-to-end) -->
            <div class="absolute top-4 sm:top-5 left-4 sm:left-6 lg:left-8 z-10">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#032316]/90 border border-emerald-500/70 shadow-[0_0_24px_rgba(16,185,129,0.7)] backdrop-blur-md">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400 shadow-[0_0_10px_#10B981]"></span>
                    </span>
                    <span class="text-emerald-300 text-xs font-black tracking-wide">Available</span>
                </div>
            </div>

            <!-- Top Right: Share & Wishlist / Save Icons (end-to-end) -->
            <div class="absolute top-4 sm:top-5 right-4 sm:right-6 lg:right-8 z-10 flex items-center gap-2.5">
                <!-- Share Profile Button -->
                <button onclick="copyProfileLink()" type="button" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-slate-950/75 hover:bg-slate-900 text-white backdrop-blur-md border border-white/20 transition-all shadow-lg flex items-center justify-center cursor-pointer hover:scale-105 active:scale-95" title="Share Profile">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                </button>

                <!-- Wishlist / Save Profile Button -->
                <form method="POST" action="{{ route('client.favorites.toggle', $professional->id) }}" class="m-0">
                    @csrf
                    <button type="submit" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-slate-950/75 hover:bg-slate-900 text-white backdrop-blur-md border border-white/20 transition-all shadow-lg flex items-center justify-center cursor-pointer hover:scale-105 active:scale-95" title="{{ $isFavorited ? 'Saved to Wishlist' : 'Add to Wishlist' }}">
                        <svg class="w-4 h-4 {{ $isFavorited ? 'fill-pink-500 text-pink-500' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                    </button>
                </form>
            </div>
        </div>
        </div>

        <!-- 2. NAME & PROFILE DETAILS BAR -->
        <div class="w-full crew-side-padding">
        <div class="w-full bg-[#090E1A] rounded-b-3xl border-b border-slate-800/80 shadow-xl relative z-20">
            <div class="w-full px-4 sm:px-6 lg:px-8 pb-6 pt-3">
                <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6 relative z-20">

                    <!-- Left: Circular Avatar & Profile Info Lines -->
                    <div class="flex items-start sm:items-center gap-4 sm:gap-6 -mt-14 sm:-mt-16">

                        <!-- Circular Avatar (No tick on avatar photo) -->
                        <div class="relative shrink-0">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="{{ $firstName }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-[#090E1A] shadow-2xl object-cover bg-slate-800">
                            @else
                                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-[#090E1A] shadow-2xl bg-gradient-to-br from-[#FF7A00] to-[#E6007A] text-white font-black text-3xl sm:text-4xl flex items-center justify-center">
                                    {{ strtoupper(substr($firstName, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- Meta Info Lines (Matching User Mockup) -->
                        <div class="space-y-1.5 pt-2">

                            <!-- 1. First Name + Verified Tick Show -->
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight m-0">
                                    {{ $firstName }}
                                </h1>
                                <!-- Verified Tick Icon shown right after first name -->
                                <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-md shrink-0" title="Verified Profile">
                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </div>

                            <!-- 2. Category -->
                            <div class="text-[#FF7A00] font-bold text-sm sm:text-base tracking-wide">
                                {{ $professional->category ?: 'Photographer / Videographer' }}
                            </div>

                            <!-- 3. Skills (Skill Pills - White background with subtle colored border) -->
                            <div class="flex flex-wrap items-center gap-2 pt-0.5 text-xs">
                                @foreach($topSkills as $sIdx => $sName)
                                    @php
                                        $pillBg = match($sIdx % 3) {
                                            0 => 'bg-white text-[#C2410C] border-[#FFDBB5]',
                                            1 => 'bg-white text-[#BE185D] border-[#FBCFE8]',
                                            default => 'bg-white text-[#C2410C] border-[#FFDBB5]',
                                        };
                                    @endphp
                                    <span class="px-3.5 py-0.5 rounded-full {{ $pillBg }} border font-extrabold text-[11px] sm:text-xs shadow-2xs">
                                        {{ $sName }}
                                    </span>
                                @endforeach
                            </div>

                            <!-- 4. Username & Location (Under Skills) -->
                            <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold pt-0.5">
                                @if($rawUsername)
                                    <span class="text-[#FFB800] font-black tracking-wide">
                                        {{ '@' . ltrim($rawUsername, '@') }}
                                    </span>
                                @endif
                                @if(!empty($locationStr))
                                    <span class="text-slate-600 font-black">•</span>
                                    <span class="text-slate-300 font-semibold">
                                        {{ $locationStr }}
                                    </span>
                                @endif
                            </div>

                        </div>

                    </div>

                    <!-- Right Column: RATINGS + Per Diem Stack on Left, CONNECT CTA on Right (Horizontal Layout Matching Mockup) -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6 shrink-0 pt-3 md:pt-0">

                        <!-- Left side of CTA: RATINGS & Per Diem Stack -->
                        <div class="flex flex-col items-start gap-1.5">

                            <!-- Ratings Stars Row -->
                            <div class="flex items-center gap-2 text-xs font-bold">
                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">RATINGS</span>
                                <div class="flex items-center gap-1.5 text-sm tracking-wider leading-none">
                                    @if($reviewsCount > 0 && $avgRating > 0)
                                        <div class="flex items-center text-amber-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= round($avgRating))
                                                    <span>★</span>
                                                @else
                                                    <span class="text-slate-600">★</span>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="text-white font-black text-xs ml-0.5">{{ number_format($avgRating, 1) }}</span>
                                        <span class="text-slate-400 font-medium text-[11px]">({{ $reviewsCount }})</span>
                                    @else
                                        <div class="flex items-center text-amber-500 text-sm tracking-wider">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span>☆</span>
                                            @endfor
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Per Diem Row with Tier Dots -->
                            <div class="flex items-center gap-2 text-xs sm:text-sm">
                                <span class="text-slate-300 font-medium">per diem</span>
                                <span class="text-[#FF7A00] font-black">{{ $currency }}</span>
                                <!-- 5 Band Dots (Matching user mockup, opens Rate Bands modal on click) -->
                                <button type="button" onclick="openRateBandsModal()" class="flex items-center gap-1.5 py-1 px-1 group cursor-pointer" title="Per Diem Band {{ $bandTier }} of 5 (Click to view rate guide)">
                                    @for($b = 1; $b <= 5; $b++)
                                        @if($b <= $bandTier)
                                            <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-r from-[#FF7A00] to-[#E6007A] inline-block shadow-xs group-hover:scale-125 transition-transform"></span>
                                        @else
                                            <span class="w-2.5 h-2.5 rounded-full border-2 border-[#E6007A]/70 bg-transparent inline-block group-hover:border-[#E6007A] group-hover:scale-125 transition-all"></span>
                                        @endif
                                    @endfor
                                </button>
                            </div>

                        </div>

                        <!-- Right: Main CONNECT CTA Button (With Comment Icon) -->
                        <a href="{{ route('crew.select', $professional) }}" class="w-full sm:w-auto px-7 sm:px-8 py-3 rounded-full bg-gradient-to-r from-[#FF7A00] to-[#E6007A] hover:opacity-95 text-white font-black text-xs sm:text-sm tracking-wider uppercase transition-all shadow-lg shadow-orange-500/30 text-decoration-none flex items-center justify-center gap-2 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>CONNECT &rarr;</span>
                        </a>

                    </div>

                </div>
            </div>
        </div>
        </div>

    <!-- Sticky Section Navigation Tab Bar (WITH 8% PADDING ON BOTH SIDES) -->
    <div class="sticky top-[76px] sm:top-[84px] z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs my-6 transition-all">
        <div class="w-full crew-side-padding">
            <div class="flex items-center gap-2 sm:gap-6 overflow-x-auto no-scrollbar py-3 text-xs font-bold text-slate-600">
                <a href="#about-section" class="nav-tab-btn px-4 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-all shrink-0 font-extrabold flex items-center gap-1.5">
                    <span>👤</span> <span>My Personal</span>
                </a>
                <a href="#skills-section" class="nav-tab-btn px-4 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-all shrink-0 font-extrabold flex items-center gap-1.5">
                    <span>⚡</span> <span>Skills & Expertise</span>
                </a>
                <a href="#education-section" class="nav-tab-btn px-4 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-all shrink-0 font-extrabold flex items-center gap-1.5">
                    <span>🎓</span> <span>Education</span>
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

    <!-- Main 2-Column Layout (WITH 8% PADDING ON BOTH SIDES) -->
    <div class="w-full crew-side-padding pt-2">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- LEFT COLUMN: Main Profile Content (Col Span 8) -->
                <div class="lg:col-span-8 space-y-8">

                    <!-- SECTION 1: MY PERSONA (3 TIERS) & WHERE I WORK (MATCHING EXACT USER MOCKUP) -->
                    <section id="about-section" class="scroll-mt-40 bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 md:p-10 shadow-xs space-y-7 sm:space-y-8">
                        
                        <!-- Header: MY PERSONA (Pink Uppercase Label) -->
                        <div class="space-y-3">
                            <span class="text-xs sm:text-[13px] font-black uppercase tracking-[0.2em] text-[#FF2D78] block">
                                MY PERSONAL
                            </span>

                            <!-- TIER 1: TODAY (Up to 200 characters) -->
                            <div class="space-y-3 pt-1">
                                <div>
                                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-none m-0">
                                        Today
                                    </h2>
                                    <span class="text-xs sm:text-sm font-semibold text-slate-400 block mt-1.5">
                                        24.10.26
                                    </span>
                                </div>

                                <!-- Today Callout Card with Vertical Gradient Left Bar -->
                                <div class="relative bg-[#FFF9F6] border border-[#FEDFD2] rounded-2xl p-5 sm:p-6 pl-7 sm:pl-8 shadow-xs">
                                    <!-- Thick Left Gradient Border (Orange -> Pink) -->
                                    <div class="absolute left-0 top-0 bottom-0 w-2 sm:w-2.5 bg-gradient-to-b from-[#FF7A00] via-[#FF4E45] to-[#E6007A] rounded-l-2xl"></div>

                                    <!-- Content Text (Up to 200 characters) -->
                                    <p class="text-sm sm:text-base font-semibold text-slate-900 leading-relaxed m-0">
                                        {{ $todayHighlight }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- TIER 2: ABOUT ME / PERSONAL BIO (80-150 words) -->
                        <div class="space-y-4 pt-2">
                            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight m-0">
                                Personal Bio
                            </h2>

                            <div class="space-y-4 pt-1">
                                @foreach($bioParagraphs as $paragraph)
                                    <p class="text-sm sm:text-base text-slate-600 font-normal leading-relaxed m-0">
                                        {!! nl2br(e($paragraph)) !!}
                                    </p>
                                @endforeach
                            </div>
                        </div>

                        <!-- Divider (Matching Mockup) -->
                        <hr class="border-t border-slate-200/80 my-2">

                        <!-- WHERE I WORK (WITH COUNTY FLAGS + CITY NAMES) -->
                        <div class="space-y-4">
                            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight m-0">
                                Where I Work
                            </h2>

                            <!-- City Badges with Flags (Exact layout matching mockup, no truncation) -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3.5">
                                @foreach($workingCities as $cityItem)
                                    @php
                                        $flagUrl = $getFlagUrl($cityItem);
                                    @endphp
                                    <div class="flex items-center gap-2.5 sm:gap-3 px-3 sm:px-4 py-2.5 sm:py-3 bg-white border border-slate-200/90 hover:border-slate-300 rounded-2xl shadow-2xs hover:shadow-md transition-all duration-300 group">
                                        <div class="w-10 h-7 sm:w-11 sm:h-7.5 rounded overflow-hidden border border-slate-200/90 shadow-2xs shrink-0 bg-slate-50 flex items-center justify-center p-0.5">
                                            <img src="{{ $flagUrl }}" alt="{{ $cityItem }} Flag" class="w-full h-full object-cover rounded group-hover:scale-105 transition-transform">
                                        </div>
                                        <span class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight whitespace-nowrap">
                                            {{ $cityItem }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @if($langsStr || $assignTypesStr)
                                <div class="pt-4 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                    @if($langsStr)
                                        <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100/90 text-slate-700 font-semibold border border-slate-200/60">
                                            <span>🗣️</span>
                                            <span>Languages: <strong>{{ $langsStr }}</strong></span>
                                        </div>
                                    @endif
                                    @if($assignTypesStr)
                                        <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100/90 text-slate-700 font-semibold border border-slate-200/60">
                                            <span>🎯</span>
                                            <span>Specialties: <strong>{{ $assignTypesStr }}</strong></span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                    </section>

                    <!-- SECTION 2: SKILLS & EXPERTISE (CLEAN, ULTRA-THIN HAIRLINE BORDERS) -->
                    <section id="skills-section" class="scroll-mt-40 bg-[#090E1A] text-white rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6" style="border: 0.5px solid rgba(255, 255, 255, 0.08) !important;">
                        <div class="flex items-center justify-between pb-3 skills-thin-border-bottom" style="border-bottom: 0.5px solid rgba(255, 255, 255, 0.08) !important;">
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-full bg-amber-500/15 text-amber-400 flex items-center justify-center shrink-0 shadow-2xs font-bold text-sm" style="border: 0.5px solid rgba(245, 158, 11, 0.3) !important;">
                                        ⚡
                                    </span>
                                    <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight m-0">Key Skills & Expertise</h2>
                                </div>
                                <div class="w-16 h-1 bg-gradient-to-r from-amber-400 to-[#FF7A00] rounded-full mt-2"></div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <!-- EXPERTISE AREAS (4 CARDS WITH DESCRIPTION & 3 KEY SKILLS) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                @foreach($displayExpertises as $expItem)
                                    @php
                                        $expName = $expItem['name'] ?? '';
                                        $expDesc = $expItem['description'] ?? '';
                                        $expIcon = $expItem['icon'] ?? '⚡';
                                        $expImage = $expItem['image'] ?? null;
                                        $expKeySkills = $expItem['key_skills'] ?? [];
                                    @endphp
                                    <div class="skills-card p-5 rounded-2xl bg-slate-900/90 hover:shadow-lg transition-all flex flex-col justify-between gap-4 group" style="border: 0.5px solid rgba(255, 255, 255, 0.12) !important;">
                                        <div class="flex items-start gap-3.5">
                                            <div class="relative shrink-0">
                                                @if($expImage)
                                                    <img src="{{ $expImage }}" alt="{{ $expName }}" class="skills-icon-box w-12 h-12 rounded-2xl object-cover shadow-2xs" style="border: 0.5px solid rgba(255, 255, 255, 0.12) !important;">
                                                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-slate-800 text-amber-400 shadow-xs text-[10px] flex items-center justify-center font-bold" style="border: 0.5px solid rgba(255, 255, 255, 0.12) !important;">
                                                        {{ $expIcon }}
                                                    </span>
                                                @else
                                                    <div class="skills-icon-box w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 text-xl flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform" style="border: 0.5px solid rgba(245, 158, 11, 0.25) !important;">
                                                        {{ $expIcon }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="space-y-1.5 min-w-0 flex-1">
                                                <h4 class="text-sm sm:text-base font-black text-white tracking-tight m-0">
                                                    {{ $expName }}
                                                </h4>
                                                @if(!empty($expDesc))
                                                    <p class="text-xs text-slate-300 font-medium leading-relaxed m-0">
                                                        {{ $expDesc }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- 3 Key Skills for this Expertise Area -->
                                        <div class="pt-3 skills-thin-border-top space-y-1.5" style="border-top: 0.5px solid rgba(255, 255, 255, 0.08) !important;">
                                            <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 block">3 Key Skills</span>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($expKeySkills as $kSkill)
                                                    <span class="skills-pill inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-500/10 text-amber-300 text-[11px] sm:text-xs font-bold transition-all shadow-2xs" style="border: 0.5px solid rgba(255, 255, 255, 0.18) !important;">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                        <span>{{ $kSkill }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- PART 2: CORE SKILLS & TAGS (3 PILLS UNDER EXPERTISE CARDS) -->
                            <div class="space-y-3 pt-5 skills-thin-border-top" style="border-top: 0.5px solid rgba(255, 255, 255, 0.08) !important;">
                                <div class="flex items-center gap-2">
                                    <span class="text-amber-400 text-sm font-black">🏷️</span>
                                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-300 m-0">Core Skills & Tags</h3>
                                </div>

                                <div class="flex flex-wrap gap-2.5">
                                    @foreach($fixedCoreSkills as $skillTag)
                                        <span class="skills-core-pill inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-900/90 text-amber-300 text-xs font-bold hover:bg-slate-850 transition-all cursor-default shadow-xs" style="border: 0.5px solid rgba(255, 255, 255, 0.18) !important;">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            <span>{{ trim($skillTag) }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 3: EDUCATION BACKGROUND (ALL SAVED EDUCATION RECORDS) -->
                    <section id="education-section" class="scroll-mt-40 bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-full bg-[#FFF6EE] border border-[#FFE7D6] flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="url(#africrew-icon-grad)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                    </svg>
                                </span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight m-0">Education & Training</h2>
                            </div>
                            @if(count($eduItems) > 0)
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                                    {{ count($eduItems) }} {{ Str::plural('Record', count($eduItems)) }}
                                </span>
                            @endif
                        </div>

                        @if(count($eduItems) > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($eduItems as $idx => $edu)
                                    @php
                                        $eItem = is_string($edu) ? json_decode($edu, true) : $edu;
                                        $sDate = $eItem['start_date'] ?? '';
                                        $eDate = $eItem['end_date'] ?? '';
                                        $startEduDisp = !empty($sDate) && strtotime($sDate) ? date('M Y', strtotime($sDate)) : trim(($eItem['start_month'] ?? '') . ' ' . ($eItem['start_year'] ?? $sDate));
                                        $endEduDisp = !empty($eDate) && strtotime($eDate) ? date('M Y', strtotime($eDate)) : trim(($eItem['end_month'] ?? '') . ' ' . ($eItem['end_year'] ?? $eDate));
                                        if ($startEduDisp && empty($endEduDisp) && !empty($sDate)) {
                                            $endEduDisp = 'Present';
                                        }
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
                        @else
                            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-slate-500 text-xs italic text-center">
                                No formal education records listed.
                            </div>
                        @endif
                    </section>

                    <!-- SECTION 4: WORK EXPERIENCE (MATCHING DARK THEME & BORDERS AS SKILLS) -->
                    <section id="experience-section" class="scroll-mt-40 bg-[#090E1A] text-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-800 space-y-6">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                            <div>
                                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight m-0">Work Experience</h2>
                                <div class="w-16 h-1 bg-gradient-to-r from-rose-500 to-amber-400 rounded-full mt-2"></div>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-rose-400 bg-rose-500/10 px-3.5 py-1 rounded-full border border-rose-500/20">
                                Proven Track Record
                            </span>
                        </div>

                        <div class="space-y-4">
                            @if(count($expRecords) > 0)
                                @foreach($expRecords as $exp)
                                    @php
                                        $xItem = is_string($exp) ? json_decode($exp, true) : $exp;
                                        $sExpDate = $xItem['start_date'] ?? '';
                                        $eExpDate = $xItem['end_date'] ?? '';
                                        $startDisp = !empty($sExpDate) && strtotime($sExpDate) ? date('M Y', strtotime($sExpDate)) : trim(($xItem['start_month'] ?? '') . ' ' . ($xItem['start_year'] ?? $sExpDate));
                                        $endDisp = !empty($eExpDate) && strtotime($eExpDate) ? date('M Y', strtotime($eExpDate)) : trim(($xItem['end_month'] ?? '') . ' ' . ($xItem['end_year'] ?? $eExpDate));
                                        if ($startDisp && empty($endDisp) && !empty($sExpDate)) {
                                            $endDisp = 'Present';
                                        }
                                    @endphp
                                    <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-amber-400/50 hover:shadow-lg transition-all space-y-2">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <div>
                                                <h3 class="text-base font-black text-white m-0">{{ $xItem['role'] ?? 'Lead Usher & Host' }}</h3>
                                                <p class="text-xs text-amber-400 font-extrabold m-0">{{ $xItem['employer'] ?? 'Safaricom PLC' }}</p>
                                            </div>
                                            @if($startDisp || $endDisp)
                                                <span class="px-3 py-1 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 text-white font-extrabold text-[10px] uppercase shadow-xs">
                                                    {{ $startDisp }}{{ ($startDisp && $endDisp) ? ' - ' : '' }}{{ $endDisp }}
                                                </span>
                                            @elseif(!empty($xItem['type']))
                                                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-300 font-extrabold text-[10px] uppercase shadow-xs">
                                                    {{ $xItem['type'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-300 font-medium leading-relaxed m-0">
                                            📍 {{ $professional->city ?: 'Nairobi' }}, Kenya • {{ $xItem['responsibilities'] ?? ($xItem['type'] ?? 'Freelance / Project-based') }}
                                        </p>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 text-center space-y-2">
                                    <span class="text-2xl block">💼</span>
                                    <h3 class="text-sm font-black text-white m-0">Work Experience</h3>
                                    <p class="text-xs text-slate-400 max-w-sm mx-auto m-0">No work experience records have been added to this profile yet.</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <!-- SECTION 5: PORTFOLIO GALLERY (PROOF OF PROFESSIONALISM) -->
                    <section id="portfolio-section" class="scroll-mt-40 bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <span class="text-[11px] font-black uppercase tracking-widest text-rose-500 block mb-1">EVIDENCE</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight m-0">Proof of Professionalism</h2>
                            </div>
                            @if(count($portfolioPhotos) > 0)
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                                    {{ count($portfolioPhotos) }} {{ Str::plural('Photo', count($portfolioPhotos)) }}
                                </span>
                            @endif
                        </div>

                        <!-- Seamless No-Gap Photo Grid Mosaic -->
                        <div class="rounded-2xl sm:rounded-3xl overflow-hidden border border-slate-200/90 shadow-sm bg-slate-900">
                            @if(count($portfolioPhotos) > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-0">
                                    @foreach(array_slice($portfolioPhotos, 0, 8) as $idx => $gPhoto)
                                        @php $gUrl = get_storage_url($gPhoto); @endphp
                                        <div class="relative h-36 sm:h-44 md:h-48 overflow-hidden group cursor-pointer border-r border-b border-white/10 last:border-r-0" onclick="openLightboxByIndex({{ $idx }})">
                                            <img src="{{ $gUrl }}" alt="{{ $displayName }} Project #{{ $idx + 1 }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 ease-out">
                                            <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center text-white font-black text-xs gap-1.5 backdrop-blur-[1px]">
                                                <span class="px-3 py-1.5 rounded-full bg-slate-900/80 border border-white/20 text-white font-extrabold text-[11px] shadow-lg flex items-center gap-1.5">
                                                    <span>🔍</span> <span>View</span>
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-8 bg-slate-50 text-center space-y-2">
                                    <span class="text-3xl block">🖼️</span>
                                    <h3 class="text-sm font-black text-slate-900 m-0">Portfolio Gallery</h3>
                                    <p class="text-xs text-slate-500 max-w-sm mx-auto m-0">No event portfolio photos uploaded yet.</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <!-- SECTION 6: CLIENT REVIEWS & TESTIMONIALS -->
                    <section id="reviews-section" class="scroll-mt-40 bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                            <div>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight m-0">What Clients Say</h2>
                                <p class="text-xs text-slate-500 font-medium m-0">Verified reviews from event organizers who hired {{ $displayName }}.</p>
                            </div>
                            @if($reviewsCount > 0 && $avgRating > 0)
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center text-amber-400 text-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($avgRating))
                                                <span class="text-amber-400">★</span>
                                            @else
                                                <span class="text-slate-300">★</span>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-base font-black text-slate-900">{{ number_format($avgRating, 1) }}</span>
                                    <span class="text-xs text-slate-400 font-bold">({{ $reviewsCount }})</span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            @if($reviews->count() > 0)
                                @foreach($reviews as $rev)
                                    @php
                                        $cName = !empty($rev->client_name) ? $rev->client_name : ($rev->client->name ?? 'Verified Client');
                                        $cInitial = strtoupper(substr($cName, 0, 1));
                                        $eventNameStr = !empty($rev->event_name) ? $rev->event_name : (!empty($rev->client_company) ? $rev->client_company : 'Verified Event');
                                        $dateStr = $rev->created_at ? $rev->created_at->format('M Y') : 'Recent Event';
                                        $ratingVal = (int)($rev->rating ?: 5);
                                    @endphp
                                    <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-slate-50 to-white border border-slate-200/90 shadow-2xs space-y-3">
                                        <div class="flex items-center justify-between gap-3 flex-wrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#FF7A00] to-[#E6007A] text-white font-black text-sm flex items-center justify-center shadow-xs shrink-0">
                                                    {{ $cInitial }}
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h4 class="text-xs sm:text-sm font-black text-slate-900 m-0">{{ $cName }}</h4>
                                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[9px] shrink-0 border border-emerald-200">
                                                            ✓ Verified Organizer
                                                        </span>
                                                    </div>
                                                    <span class="text-[11px] text-slate-500 font-semibold block mt-0.5">{{ $dateStr }} • {{ $eventNameStr }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1 text-amber-400 text-sm">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span>{{ $i <= $ratingVal ? '★' : '☆' }}</span>
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="text-xs sm:text-sm text-slate-700 font-medium leading-relaxed italic m-0 bg-slate-50/80 p-3.5 rounded-xl border border-slate-100">
                                            "{{ $rev->comment }}"
                                        </p>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200/90 text-center space-y-2">
                                    <span class="text-2xl text-amber-400 block">⭐</span>
                                    <h3 class="text-sm font-black text-slate-900 m-0">No client reviews yet</h3>
                                    <p class="text-xs text-slate-500 font-medium max-w-sm mx-auto m-0">Reviews from event organizers will appear here once bookings are completed.</p>
                                </div>
                            @endif
                        </div>
                    </section>

                </div>

                <!-- RIGHT COLUMN: Sticky Booking & Stats Sidebar (Col Span 4) -->
                <div class="lg:col-span-4 lg:sticky lg:top-40 space-y-6">

                    <!-- CARD 1: Quick Booking Form Widget -->
                    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-xl space-y-5" id="booking-sidebar">

                        <!-- Chat Speech Bubble Header -->
                        <div class="flex items-start gap-3 bg-slate-950 text-white border border-slate-800 rounded-2xl p-4 shadow-md">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="{{ $displayName }}" class="w-11 h-11 rounded-xl object-cover border-2 border-amber-400 shadow-xs shrink-0">
                            @else
                                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-400 to-rose-500 text-slate-950 font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                                    {{ strtoupper(substr($displayName, 0, 1)) }}
                                </div>
                            @endif
                            <div class="space-y-1">
                                <h3 class="text-xs font-black text-amber-400 m-0">Hi, I'm {{ strtok($displayName, ' ') }}. 👋</h3>
                                <p class="text-[11px] text-slate-300 font-medium leading-relaxed m-0">
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
                                    <option value="{{ $professional->category }}">{{ $professional->category }} (Primary)</option>
                                    @if(!empty($savedExpertises))
                                        @foreach($savedExpertises as $expItem)
                                            <option value="{{ $expItem['name'] ?? '' }}">{{ $expItem['name'] ?? '' }}</option>
                                        @endforeach
                                    @endif
                                    <option value="Event Staff">General Event Staff</option>
                                </select>
                            </div>

                            <!-- Question 2: Date & Shift -->
                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1 flex items-center justify-between">
                                        <span>DATE (FROM)</span>
                                        <span class="text-[9px] font-black text-rose-600 bg-rose-50 px-1 py-0.5 rounded border border-rose-200">START</span>
                                    </label>
                                    <input type="text" id="crew_show_event_date" name="event_date" value="{{ date('d M Y', strtotime('+2 days')) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs text-slate-900 font-bold outline-none focus:border-rose-500 cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1 flex items-center justify-between">
                                        <span>DURATION</span>
                                        <span class="text-[9px] font-black text-slate-500">SHIFT</span>
                                    </label>
                                    <select name="shift_duration" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs text-slate-900 font-bold outline-none focus:border-rose-500 transition-all cursor-pointer">
                                        <option value="1_day" selected>1 Day Shift</option>
                                        <option value="2_days">2 Days Shift</option>
                                        <option value="3_days">3 Days Shift</option>
                                        <option value="multi_day">Full Event Duration</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Question 3: Location Dropdown -->
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1">
                                    WHERE IS IT HAPPENING?
                                </label>
                                <select name="location" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-3 text-xs text-slate-900 font-bold outline-none focus:border-rose-500 transition-all cursor-pointer">
                                    <option value="{{ $locationStr }}">{{ $locationStr }}</option>
                                    @if(!empty($citiesArr))
                                        @foreach($citiesArr as $ct)
                                            @if(trim($ct) && trim($ct) !== $locationStr)
                                                <option value="{{ trim($ct) }}">{{ trim($ct) }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                    <option value="Nairobi, Kenya">Nairobi, Kenya</option>
                                    <option value="Mombasa, Kenya">Mombasa, Kenya</option>
                                    <option value="Kisumu, Kenya">Kisumu, Kenya</option>
                                    <option value="Nakuru, Kenya">Nakuru, Kenya</option>
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
                            <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-[#FF7A00] to-[#E6007A] hover:opacity-95 text-white font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-orange-500/25 flex items-center justify-center gap-2 cursor-pointer">
                                <span>HIRE {{ strtoupper(strtok($displayName, ' ')) }} NOW →</span>
                            </button>

                            <button type="button" onclick="addCrewToTeamBasket({{ json_encode([
                                'id' => $professional->id,
                                'full_name' => $displayName,
                                'profile_photo_url' => $photoUrl ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400',
                                'category' => $professional->category ?: 'Event Crew'
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
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2 m-0">
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
                                    <span>{{ $reviewsCount > 0 && $avgRating > 0 ? number_format($avgRating, 1) : '—' }}</span>
                                </div>
                                <span class="text-[10px] text-amber-800 font-bold uppercase tracking-wider block mt-0.5">Avg. rating</span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <div class="flex items-center gap-2 text-slate-900 font-black text-base">
                                    <span>⚡</span>
                                    <span>{{ $professional->on_time_rate ?: '100%' }}</span>
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

                    <!-- CARD 3: LET’S CONNECT (Under Quick Stats) -->
                    <div id="lets-connect-section" class="p-6 rounded-3xl bg-gradient-to-br from-orange-50/80 via-pink-50/50 to-amber-50/70 border border-orange-200/80 shadow-sm space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="text-sm sm:text-base font-black text-slate-900 tracking-tight m-0 flex items-center gap-2">
                                <span>🤝</span>
                                <span>Let’s Connect</span>
                            </h4>
                            <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-wider text-[#FF2D78] bg-pink-100/90 border border-pink-200/80 px-2.5 py-0.5 rounded-full whitespace-nowrap">
                                Personal Invitation
                            </span>
                        </div>

                        <p class="text-xs sm:text-sm text-slate-700 font-medium leading-relaxed m-0">
                            {{ $letsConnectText }}
                        </p>

                        <div class="flex flex-col gap-2.5 pt-1">
                            <a href="{{ route('crew.select', $professional) }}" class="w-full py-2.5 px-4 rounded-full bg-gradient-to-r from-[#FF7A00] to-[#E6007A] hover:opacity-95 text-white font-black text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 text-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>Message / Inquire Now &rarr;</span>
                            </a>
                            <button type="button" onclick="copyProfileLink()" class="w-full py-2 px-4 rounded-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/90 font-bold text-xs shadow-2xs hover:shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                <span>Share Profile</span>
                            </button>
                        </div>
                    </div>

                    <!-- CARD 4: VERIFIED REFEREE DETAILS (Under Let's Connect) -->
                    @if(count($publicVerifiedReferees) > 0)
                        <div id="verified-referees-section" class="bg-white border border-emerald-200/90 rounded-3xl p-5 sm:p-6 shadow-sm space-y-4">
                            <div class="flex items-center justify-between gap-2 border-b border-emerald-100/80 pb-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold border border-emerald-200 shrink-0">
                                        🛡️
                                    </span>
                                    <div>
                                        <h4 class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-900 m-0">
                                            Verified Referees
                                        </h4>
                                        <span class="text-[10px] text-slate-500 font-medium">Confirmed work references</span>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full shrink-0">
                                    ✓ {{ count($publicVerifiedReferees) }} Verified
                                </span>
                            </div>

                            <div class="space-y-2.5">
                                @foreach($publicVerifiedReferees as $vRef)
                                    <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-br from-emerald-50/40 via-white to-slate-50 border border-emerald-200/70 shadow-2xs">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0 flex-1">
                                                <h5 class="font-black text-slate-900 text-xs sm:text-sm m-0 truncate">{{ $vRef['name'] }}</h5>
                                                @if(!empty($vRef['organization']))
                                                    <p class="text-[11px] sm:text-xs text-slate-600 font-semibold m-0 mt-1 flex items-center gap-1.5 truncate">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                        <span class="truncate">{{ $vRef['organization'] }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-black text-[9px] border border-emerald-200 inline-flex items-center gap-0.5 shrink-0">
                                                ✓ Verified
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

            </div>
        </div>

    </div>

    <!-- Per Diem Rate Bands Guide Modal -->
    <div id="rate-bands-modal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md hidden items-center justify-center p-4 select-none" onclick="closeRateBandsModal()">
        <div class="relative max-w-md w-full bg-[#0B1120] border border-slate-800 rounded-3xl p-6 sm:p-7 shadow-2xl space-y-5 text-white" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-lg font-black">
                        💳
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-white m-0">Per Diem Rate Bands</h3>
                        <p class="text-xs text-slate-400 m-0">Standard rate tier reference</p>
                    </div>
                </div>
                <button type="button" onclick="closeRateBandsModal()" class="w-8 h-8 rounded-full bg-slate-800/90 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center font-black text-sm transition-colors cursor-pointer" title="Close (Esc)">✕</button>
            </div>

            <!-- Rate Bands List -->
            <div class="space-y-2.5">
                <!-- Tier 1 -->
                <div class="flex items-center justify-between p-3.5 rounded-2xl {{ $bandTier === 1 ? 'bg-amber-500/15 border-2 border-amber-400/80 shadow-md' : 'bg-slate-900/80 border border-slate-800' }}">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-xl {{ $bandTier === 1 ? 'bg-amber-400 text-slate-950 font-black' : 'bg-slate-800 text-slate-400 font-bold' }} text-xs flex items-center justify-center shrink-0">1</span>
                        <div>
                            <span class="text-xs sm:text-sm font-bold text-white block">up to KES 5,000</span>
                            @if($bandTier === 1)
                                <span class="text-[10px] font-extrabold text-amber-400 uppercase tracking-wider block">● {{ $firstName }}'s Band</span>
                            @endif
                        </div>
                    </div>
                    <span class="font-black text-amber-400 text-sm sm:text-base tracking-wider shrink-0">£ $</span>
                </div>

                <!-- Tier 2 -->
                <div class="flex items-center justify-between p-3.5 rounded-2xl {{ $bandTier === 2 ? 'bg-amber-500/15 border-2 border-amber-400/80 shadow-md' : 'bg-slate-900/80 border border-slate-800' }}">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-xl {{ $bandTier === 2 ? 'bg-amber-400 text-slate-950 font-black' : 'bg-slate-800 text-slate-400 font-bold' }} text-xs flex items-center justify-center shrink-0">2</span>
                        <div>
                            <span class="text-xs sm:text-sm font-bold text-white block">KES 5,001–8,000</span>
                            @if($bandTier === 2)
                                <span class="text-[10px] font-extrabold text-amber-400 uppercase tracking-wider block">● {{ $firstName }}'s Band</span>
                            @endif
                        </div>
                    </div>
                    <span class="font-black text-amber-400 text-sm sm:text-base tracking-wider shrink-0">£ $$</span>
                </div>

                <!-- Tier 3 -->
                <div class="flex items-center justify-between p-3.5 rounded-2xl {{ $bandTier === 3 ? 'bg-amber-500/15 border-2 border-amber-400/80 shadow-md' : 'bg-slate-900/80 border border-slate-800' }}">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-xl {{ $bandTier === 3 ? 'bg-amber-400 text-slate-950 font-black' : 'bg-slate-800 text-slate-400 font-bold' }} text-xs flex items-center justify-center shrink-0">3</span>
                        <div>
                            <span class="text-xs sm:text-sm font-bold text-white block">KES 8,001–12,000</span>
                            @if($bandTier === 3)
                                <span class="text-[10px] font-extrabold text-amber-400 uppercase tracking-wider block">● {{ $firstName }}'s Band</span>
                            @endif
                        </div>
                    </div>
                    <span class="font-black text-amber-400 text-sm sm:text-base tracking-wider shrink-0">£ $$$</span>
                </div>

                <!-- Tier 4 -->
                <div class="flex items-center justify-between p-3.5 rounded-2xl {{ $bandTier === 4 ? 'bg-amber-500/15 border-2 border-amber-400/80 shadow-md' : 'bg-slate-900/80 border border-slate-800' }}">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-xl {{ $bandTier === 4 ? 'bg-amber-400 text-slate-950 font-black' : 'bg-slate-800 text-slate-400 font-bold' }} text-xs flex items-center justify-center shrink-0">4</span>
                        <div>
                            <span class="text-xs sm:text-sm font-bold text-white block">KES 12,001–50,000</span>
                            @if($bandTier === 4)
                                <span class="text-[10px] font-extrabold text-amber-400 uppercase tracking-wider block">● {{ $firstName }}'s Band</span>
                            @endif
                        </div>
                    </div>
                    <span class="font-black text-amber-400 text-sm sm:text-base tracking-wider shrink-0">£ $$$$</span>
                </div>

                <!-- Tier 5 -->
                <div class="flex items-center justify-between p-3.5 rounded-2xl {{ $bandTier === 5 ? 'bg-amber-500/15 border-2 border-amber-400/80 shadow-md' : 'bg-slate-900/80 border border-slate-800' }}">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-xl {{ $bandTier === 5 ? 'bg-amber-400 text-slate-950 font-black' : 'bg-slate-800 text-slate-400 font-bold' }} text-xs flex items-center justify-center shrink-0">5</span>
                        <div>
                            <span class="text-xs sm:text-sm font-bold text-white block">KES 50,001–100,000</span>
                            @if($bandTier === 5)
                                <span class="text-[10px] font-extrabold text-amber-400 uppercase tracking-wider block">● {{ $firstName }}'s Band</span>
                            @endif
                        </div>
                    </div>
                    <span class="font-black text-amber-400 text-sm sm:text-base tracking-wider shrink-0">£ $$$$$</span>
                </div>
            </div>

            <!-- Got it Button -->
            <button type="button" onclick="closeRateBandsModal()" class="w-full py-3 rounded-2xl bg-gradient-to-r from-[#FF7A00] to-[#E6007A] text-white font-black text-xs uppercase tracking-wider hover:opacity-95 transition-opacity cursor-pointer">
                Got It
            </button>
        </div>
    </div>

    <!-- Lightbox Modal for Portfolio Photos with Left/Right Navigation -->
    <div id="lightbox-modal" class="fixed inset-0 z-50 bg-slate-950/95 hidden items-center justify-center p-3 sm:p-6 backdrop-blur-md select-none" onclick="closeLightbox()">
        <div class="relative max-w-5xl w-full flex flex-col items-center justify-center" onclick="event.stopPropagation()">

            <!-- Top Control Bar (Counter & Close) -->
            <div class="w-full flex items-center justify-between px-2 pb-3">
                <span id="lightbox-counter" class="text-xs sm:text-sm font-extrabold text-white/90 bg-slate-900/80 border border-white/20 px-3.5 py-1.5 rounded-full shadow-lg backdrop-blur-md">
                    1 / {{ count($portfolioPhotos) }}
                </span>
                <button type="button" onclick="closeLightbox()" class="w-10 h-10 rounded-full bg-slate-900/80 hover:bg-slate-800 text-white border border-white/20 backdrop-blur-md flex items-center justify-center font-black text-sm transition-all hover:scale-110 active:scale-95 cursor-pointer shadow-lg" title="Close (Esc)">
                    ✕
                </button>
            </div>

            <!-- Image Stage with Left / Right Navigation Arrows -->
            <div class="relative w-full flex items-center justify-center">
                <!-- Left Navigation Arrow (‹) -->
                <button type="button" onclick="prevLightboxPhoto(event)" class="absolute left-2 sm:left-4 z-20 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-slate-900/85 hover:bg-slate-800 text-white border border-white/20 backdrop-blur-md shadow-2xl flex items-center justify-center text-2xl sm:text-3xl font-black transition-all hover:scale-110 active:scale-95 cursor-pointer" title="Previous Photo (Left Arrow)">
                    ‹
                </button>

                <!-- Centered Photo Display -->
                <div class="w-full max-h-[72vh] sm:max-h-[78vh] flex items-center justify-center rounded-2xl overflow-hidden bg-black/60 border border-slate-800 shadow-2xl p-1">
                    <img id="lightbox-img" src="" alt="Gallery Image" class="max-h-[70vh] sm:max-h-[76vh] w-auto max-w-full object-contain rounded-xl transition-all duration-300">
                </div>

                <!-- Right Navigation Arrow (›) -->
                <button type="button" onclick="nextLightboxPhoto(event)" class="absolute right-2 sm:right-4 z-20 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-slate-900/85 hover:bg-slate-800 text-white border border-white/20 backdrop-blur-md shadow-2xl flex items-center justify-center text-2xl sm:text-3xl font-black transition-all hover:scale-110 active:scale-95 cursor-pointer" title="Next Photo (Right Arrow)">
                    ›
                </button>
            </div>

            <!-- Bottom Caption -->
            <p id="lightbox-caption" class="text-center text-xs sm:text-sm font-bold text-amber-300 pt-3 m-0"></p>
        </div>
    </div>

    <script>
    const galleryPhotos = @json($galleryItems);
    let currentPhotoIdx = 0;

    function openLightboxByIndex(idx) {
        if (!galleryPhotos || galleryPhotos.length === 0) return;
        currentPhotoIdx = (idx + galleryPhotos.length) % galleryPhotos.length;
        updateLightboxUI();
        const modal = document.getElementById('lightbox-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function updateLightboxUI() {
        if (!galleryPhotos || galleryPhotos.length === 0) return;
        const item = galleryPhotos[currentPhotoIdx];
        const imgEl = document.getElementById('lightbox-img');
        imgEl.src = item.url;
        document.getElementById('lightbox-caption').innerText = item.caption;
        const counterEl = document.getElementById('lightbox-counter');
        if (counterEl) {
            counterEl.innerText = `${currentPhotoIdx + 1} / ${galleryPhotos.length}`;
        }
    }

    function nextLightboxPhoto(e) {
        if (e) e.stopPropagation();
        if (!galleryPhotos || galleryPhotos.length === 0) return;
        currentPhotoIdx = (currentPhotoIdx + 1) % galleryPhotos.length;
        updateLightboxUI();
    }

    function prevLightboxPhoto(e) {
        if (e) e.stopPropagation();
        if (!galleryPhotos || galleryPhotos.length === 0) return;
        currentPhotoIdx = (currentPhotoIdx - 1 + galleryPhotos.length) % galleryPhotos.length;
        updateLightboxUI();
    }

    function closeLightbox() {
        const modal = document.getElementById('lightbox-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function openRateBandsModal() {
        const modal = document.getElementById('rate-bands-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeRateBandsModal() {
        const modal = document.getElementById('rate-bands-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', (e) => {
        const modal = document.getElementById('lightbox-modal');
        if (modal && !modal.classList.contains('hidden')) {
            if (e.key === 'ArrowRight') nextLightboxPhoto();
            if (e.key === 'ArrowLeft') prevLightboxPhoto();
            if (e.key === 'Escape') closeLightbox();
        }
        const bandsModal = document.getElementById('rate-bands-modal');
        if (bandsModal && !bandsModal.classList.contains('hidden')) {
            if (e.key === 'Escape') closeRateBandsModal();
        }
    });

    function copyProfileLink() {
        const targetUrl = window.location.href;
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Share Profile',
                html: `
                    <div class="space-y-4 text-left p-1">
                        <p class="text-xs text-slate-600 font-medium">Share this profile link with clients or event organizers:</p>
                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between gap-2">
                            <span class="text-xs font-extrabold text-slate-900 truncate">${targetUrl}</span>
                            <a href="${targetUrl}" target="_blank" class="text-xs font-black text-[#FF6B00] hover:underline shrink-0">Open →</a>
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: '📋 Copy Link',
                cancelButtonText: 'Close',
                confirmButtonColor: '#FF6B00'
            }).then((result) => {
                if (result.isConfirmed) {
                    navigator.clipboard.writeText(targetUrl);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Profile link copied to clipboard!',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
            });
        } else {
            navigator.clipboard.writeText(targetUrl);
            alert(`Profile Link copied: ${targetUrl}`);
        }
    }
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof flatpickr !== 'undefined') {
            flatpickr('#crew_show_event_date', {
                dateFormat: 'd M Y',
                minDate: 'today'
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
