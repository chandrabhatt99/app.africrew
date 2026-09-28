@extends('layouts.staff')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 sm:space-y-8 pb-16 px-2 sm:px-0">

        <!-- Top Profile Header Area -->
        <div class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm">

            <!-- Full-Width Customizable Cover Banner -->
            <div
                class="relative h-40 sm:h-64 bg-gradient-to-r from-slate-950 via-slate-900 to-amber-950 overflow-hidden group">
                @if($professional->cover_photo)
                    @php
                        $coverUrl = get_storage_url($professional->cover_photo);
                    @endphp
                    <img id="cover-photo-preview" src="{{ $coverUrl }}" alt="Cover Banner"
                        class="w-full h-full object-cover opacity-85">
                @else
                    <img id="cover-photo-preview" src="" alt="Cover Banner"
                        class="w-full h-full object-cover opacity-85 hidden">
                @endif
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/20 pointer-events-none">
                </div>

            </div>

            <!-- Hero Avatar & User Info Header -->
            <div
                class="p-4 sm:p-8 relative -mt-10 sm:-mt-15 flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-slate-100">

                <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 text-center sm:text-left w-full sm:w-auto">
                    <!-- Overlapping Circular Avatar -->
                    <div class="relative shrink-0">
                        @php
                            $avatarUrl = $professional->profile_photo_url;
                        @endphp
                        @if($avatarUrl)
                            <img id="avatar-photo-preview" src="{{ $avatarUrl }}" alt="{{ $professional->full_name }}"
                                class="w-28 h-28 sm:w-36 sm:h-36 aspect-square rounded-full border-4 border-white object-cover shadow-2xl shrink-0">
                        @else
                            <div id="avatar-photo-placeholder"
                                class="w-28 h-28 sm:w-36 sm:h-36 aspect-square rounded-full border-4 border-white bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-3xl sm:text-4xl flex items-center justify-center shadow-2xl shrink-0">
                                {{ strtoupper(substr($professional->full_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <!-- User Name & Role Badge -->
                    <div class="space-y-1 text-center sm:text-left">
                        @php
                            $formattedFullName = ucwords(mb_strtolower($professional->full_name ?: ($professional->username ?? 'Crew Member')));
                            $names = explode(' ', $professional->full_name ?? '', 2);
                            $firstName = ucwords(mb_strtolower($names[0] ?? ''));
                            $lastName = ucwords(mb_strtolower($names[1] ?? ''));
                        @endphp
                        <div class="flex items-center justify-center sm:justify-start flex-wrap gap-2.5">
                            <h1 class="text-xl sm:text-3xl font-black text-slate-900 tracking-tight">
                                {{ $formattedFullName }}
                            </h1>
                            <span
                                class="text-[9px] sm:text-[10px] font-black uppercase px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-amber-500/10 text-amber-700 border border-amber-500/20">
                                ⚡ {{ strtoupper($professional->category ?? 'Event Usher') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- View Public Page Button -->
                <a href="{{ route('crew.show', $professional) }}" target="_blank"
                    class="w-full sm:w-auto px-5 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl bg-slate-900 hover:bg-slate-800 text-amber-400 text-xs font-black transition-all shadow-md border border-slate-800 flex items-center justify-center gap-2 shrink-0">
                    <span>🌐 View Public Profile</span>
                    <span>→</span>
                </a>

            </div>



            <!-- Profile Completion Status Banner -->
            @php
                $filledCount = 0;
                $missingList = [];

                if (!empty($professional->full_name) && !empty($professional->phone)) {
                    $filledCount++;
                } else {
                    $missingList[] = 'Personal';
                }

                if (!empty($professional->education) || !empty($professional->experience_records)) {
                    $filledCount++;
                } else {
                    $missingList[] = 'Experience';
                }

                if (!empty($professional->profile_photo) || !empty($professional->gallery_photos)) {
                    $filledCount++;
                } else {
                    $missingList[] = 'Photos';
                }

                if (!empty($professional->booking_policy) || !empty($professional->availability)) {
                    $filledCount++;
                } else {
                    $missingList[] = 'Availability';
                }

                if (!empty($professional->one_day_rate) || !empty($professional->hourly_rate)) {
                    $filledCount++;
                } else {
                    $missingList[] = 'Labour Charges';
                }

                $completionPct = intval(($filledCount / 5) * 100);
                $isFullyComplete = ($completionPct === 100);
            @endphp

            @if($isFullyComplete)
                <div
                    class="px-6 sm:px-8 py-3.5 bg-emerald-500/10 border-b border-emerald-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="font-black text-emerald-950">✓ Crew Profile 100% Completed</span>
                        <span class="text-slate-300 font-bold">•</span>
                        <span class="font-bold text-emerald-900">Your profile is fully verified and active for bookings</span>
                    </div>
                    <div class="w-full sm:w-48 bg-emerald-200/70 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-emerald-600 h-full rounded-full w-full"></div>
                    </div>
                </div>
            @else
                <div
                    class="px-6 sm:px-8 py-3.5 bg-amber-500/10 border-b border-amber-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <span class="font-black text-amber-950">Profile Setup {{ $completionPct }}% Completed</span>
                        <span class="text-slate-300 font-bold">•</span>
                        <span class="font-bold text-amber-900">
                            @if(count($missingList) > 0)
                                {{ count($missingList) }} section(s) left: {{ implode(', ', $missingList) }}
                            @else
                                Complete profile to activate bookings
                            @endif
                        </span>
                    </div>
                    <div class="w-full sm:w-48 bg-slate-200 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-full rounded-full transition-all duration-300"
                            style="width: {{ $completionPct }}%;"></div>
                    </div>
                </div>
            @endif

            <!-- Horizontal Segmented Tabs Navigation Bar -->
            <div
                class="p-3 sm:p-4 bg-slate-100/70 flex items-center gap-2 sm:gap-3 overflow-x-auto text-xs font-extrabold scrollbar-none">
                <button type="button" onclick="showProfileTab('personal', 1)" id="tab-nav-personal"
                    class="tab-nav-btn px-4 py-3 rounded-2xl bg-slate-900 text-amber-400 font-black shadow-md border border-slate-900 transition-all flex items-center gap-2 whitespace-nowrap">
                    <span>👤 Personal</span>
                </button>
                <button type="button" onclick="showProfileTab('bio', 2)" id="tab-nav-bio"
                    class="tab-nav-btn px-4 py-3 rounded-2xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                    <span>📝 Bio & Quote</span>
                </button>
                <button type="button" onclick="showProfileTab('experience', 3)" id="tab-nav-experience"
                    class="tab-nav-btn px-4 py-3 rounded-2xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                    <span>💼 Experience</span>
                </button>
                <button type="button" onclick="showProfileTab('photos', 4)" id="tab-nav-photos"
                    class="tab-nav-btn px-4 py-3 rounded-2xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                    <span>🖼️ Photos</span>
                </button>
                <button type="button" onclick="showProfileTab('availability', 5)" id="tab-nav-availability"
                    class="tab-nav-btn px-4 py-3 rounded-2xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                    <span>📅 Availability</span>
                </button>
                <button type="button" onclick="showProfileTab('charges', 6)" id="tab-nav-charges"
                    class="tab-nav-btn px-4 py-3 rounded-2xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                    <span>💰 Labour Charges</span>
                </button>
                <button type="button" onclick="showProfileTab('reviews', 7)" id="tab-nav-reviews"
                    class="tab-nav-btn px-4 py-3 rounded-2xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                    <span>⭐ Reviews & Visibility</span>
                </button>
            </div>

        </div>

        <!-- Main Profile Edit Form Container -->
        <form id="profile-main-form" method="POST" action="{{ route('professional.profile.update') }}"
            enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PATCH')
            <input type="hidden" name="active_tab" id="active-tab-input"
                value="{{ old('active_tab', session('active_tab', request('active_tab', 'personal'))) }}">

            <!-- TAB 1: PERSONAL TAB -->
            <div id="tab-view-personal"
                class="tab-view-content bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Personal Information</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Your basic account details</p>
                </div>

                <!-- Form Grid (2 Columns) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs font-bold">

                    <div>
                        <label class="block text-slate-700 mb-1">
                            First Name * 
                            @if(!empty($firstName))
                                <span class="text-amber-700 font-semibold text-[10px] ml-1">🔒 Registered Name (Fixed)</span>
                            @endif
                        </label>
                        <input type="text" name="first_name" value="{{ old('first_name', $firstName) }}"
                            @if(!empty($firstName)) readonly @endif
                            class="w-full rounded-2xl px-4 py-3 text-xs font-bold outline-none {{ !empty($firstName) ? 'bg-slate-100 text-slate-500 border border-slate-200 cursor-not-allowed' : 'bg-slate-50 border border-slate-200 text-slate-900 focus:border-amber-500 focus:bg-white' }}">
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1">
                            Last Name * 
                            @if(!empty($lastName))
                                <span class="text-amber-700 font-semibold text-[10px] ml-1">🔒 Registered Name (Fixed)</span>
                            @endif
                        </label>
                        <input type="text" name="last_name" value="{{ old('last_name', $lastName) }}"
                            @if(!empty($lastName)) readonly @endif
                            class="w-full rounded-2xl px-4 py-3 text-xs font-bold outline-none {{ !empty($lastName) ? 'bg-slate-100 text-slate-500 border border-slate-200 cursor-not-allowed' : 'bg-slate-50 border border-slate-200 text-slate-900 focus:border-amber-500 focus:bg-white' }}">
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1">
                            Username * 
                            @if(!empty($professional->username))
                                <span class="text-amber-700 font-semibold text-[10px] ml-1">🔒 Registered Handle (Fixed)</span>
                            @endif
                        </label>
                        <input type="text" name="username"
                            value="{{ old('username', $professional->username ?? Str::slug($firstName)) }}"
                            @if(!empty($professional->username)) readonly @endif
                            class="w-full rounded-2xl px-4 py-3 text-xs font-bold outline-none {{ !empty($professional->username) ? 'bg-slate-100 text-slate-500 border border-slate-200 cursor-not-allowed' : 'bg-slate-50 border border-slate-200 text-slate-900 focus:border-amber-500 focus:bg-white' }}">
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1">
                            Email * 
                            @if(!empty($professional->email))
                                <span class="text-amber-700 font-semibold text-[10px] ml-1">🔒 Registered Email (Fixed)</span>
                            @endif
                        </label>
                        <input type="email" name="email" value="{{ old('email', $professional->email) }}"
                            @if(!empty($professional->email)) readonly @endif
                            class="w-full rounded-2xl px-4 py-3 text-xs font-bold outline-none {{ !empty($professional->email) ? 'bg-slate-100 text-slate-500 border border-slate-200 cursor-not-allowed' : 'bg-slate-50 border border-slate-200 text-slate-900 focus:border-amber-500 focus:bg-white' }}">
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1">
                            Phone Number * 
                            @if(!empty($professional->phone))
                                <span class="text-amber-700 font-semibold text-[10px] ml-1">🔒 Registered Phone (Fixed)</span>
                            @endif
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone', $professional->phone) }}"
                            placeholder="+254 712 345 678"
                            @if(!empty($professional->phone)) readonly @endif
                            class="w-full rounded-2xl px-4 py-3 text-xs font-bold outline-none {{ !empty($professional->phone) ? 'bg-slate-100 text-slate-500 border border-slate-200 cursor-not-allowed' : 'bg-slate-50 border border-slate-200 text-slate-900 focus:border-amber-500 focus:bg-white' }}">
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1">Gender *</label>
                        <select name="gender"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500 focus:bg-white">
                            <option value="" disabled @selected(!old('gender', $professional->gender))>-- Select Gender --</option>
                            <option value="Male" @selected(old('gender', $professional->gender) == 'Male')>Male</option>
                            <option value="Female" @selected(old('gender', $professional->gender) == 'Female')>Female</option>
                            <option value="Other" @selected(old('gender', $professional->gender) == 'Other')>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1">
                            Date of Birth <span class="text-amber-600 font-bold">(Age Must Be 18+)</span>
                        </label>
                        @php
                            $dobVal = old('date_of_birth', $professional->date_of_birth ? \Carbon\Carbon::parse($professional->date_of_birth)->format('Y-m-d') : '');
                            $maxDobLimit = \Carbon\Carbon::now()->subYears(18)->format('Y-m-d');
                            $calculatedAge = null;
                            if ($dobVal) {
                                $calculatedAge = \Carbon\Carbon::parse($dobVal)->age;
                            }
                        @endphp
                        <input type="date" id="date_of_birth_input" name="date_of_birth"
                            value="{{ $dobVal }}"
                            max="{{ $maxDobLimit }}"
                            onchange="calculateAndValidateAge(this.value)"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500 focus:bg-white">
                        
                        <div id="age-display-container" class="mt-1.5 flex items-center justify-between text-xs font-bold">
                            @if($calculatedAge !== null)
                                @if($calculatedAge >= 18)
                                    <span id="age-calc-badge" class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-black text-[11px] inline-flex items-center gap-1">
                                        ✓ Age: {{ $calculatedAge }} years old (Eligible 18+)
                                    </span>
                                @else
                                    <span id="age-calc-badge" class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-black text-[11px] inline-flex items-center gap-1">
                                        ⚠️ Age: {{ $calculatedAge }} years old (Must be 18+ years old)
                                    </span>
                                @endif
                            @else
                                <span id="age-calc-badge" class="text-slate-400 text-[11px] italic font-medium">Select DOB to auto-calculate age (Minimum 18 years old)</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1">
                            Primary Crew Category * 
                            @if(!empty($professional->category))
                                <span class="text-amber-700 font-semibold text-[10px] ml-1">🔒 Registered Category (Fixed)</span>
                            @endif
                        </label>
                        @if(!empty($professional->category))
                            <input type="hidden" name="category" value="{{ $professional->category }}">
                        @endif
                        <select name="category"
                            @if(!empty($professional->category)) disabled @endif
                            class="w-full rounded-2xl px-4 py-3 text-xs font-bold outline-none {{ !empty($professional->category) ? 'bg-slate-100 text-slate-500 border border-slate-200 cursor-not-allowed' : 'bg-slate-50 border border-slate-200 text-slate-900 focus:border-amber-500 focus:bg-white' }}">
                            @if(isset($categories) && $categories->count() > 0)
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}" @selected($professional->category == $cat->name || strtolower($professional->category) == strtolower($cat->slug))>
                                        {{ $cat->icon ? $cat->icon . ' ' : '' }}{{ $cat->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="Event Ushers" @selected($professional->category == 'Event Ushers' || $professional->category == 'USER')>⚡ Event Ushers</option>
                                <option value="VIP Hostesses" @selected($professional->category == 'VIP Hostesses')>👑 VIP Hostesses</option>
                                <option value="Security & Bouncers" @selected($professional->category == 'Security & Bouncers')>🛡️ Security & Bouncers</option>
                                <option value="Mixologists & Bartenders" @selected($professional->category == 'Mixologists & Bartenders')>🍸 Mixologists & Bartenders</option>
                                <option value="Protocol Officers" @selected($professional->category == 'Protocol Officers')>🏛️ Protocol Officers</option>
                                <option value="Brand Ambassadors" @selected($professional->category == 'Brand Ambassadors')>🌟 Brand Ambassadors</option>
                            @endif
                        </select>
                    </div>

                </div>

                <!-- Tab 1 Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                    <button type="button" onclick="saveAndGoToTab('bio', 2)"
                        class="px-8 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs shadow-lg transition-all flex items-center gap-2">
                        <span>Save & Next: Bio & Quote</span>
                        <span>→</span>
                    </button>
                </div>

            </div>

            <!-- TAB 2: BIO & HIGHLIGHT QUOTE TAB -->
            <div id="tab-view-bio"
                class="tab-view-content hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Bio, Highlight Quote & Social Media</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Share your background story, featured motto, and social links</p>
                </div>

                <div class="space-y-6 text-xs font-bold">

                    <div>
                        <label class="block text-slate-700 mb-1">Highlight Quote / Personal Statement <span class="text-amber-600 font-normal">(Displays as a featured quote on your public profile)</span></label>
                        <textarea name="highlight_quote" rows="2"
                            placeholder='e.g. "Events have taught me that guests rarely remember who showed them their seat—but they always remember how they were treated."'
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-slate-900 text-xs font-bold outline-none focus:border-amber-500 focus:bg-white">{{ old('highlight_quote', $professional->highlight_quote) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1">Bio / Profile Summary</label>
                        <textarea id="bio-textarea" name="about" rows="5"
                            placeholder="Tell people about yourself and your ushering experience..."
                            oninput="updateBioCounter(this)"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-slate-900 text-xs font-medium outline-none focus:border-amber-500 focus:bg-white">{{ old('about', $professional->about) }}</textarea>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 font-semibold mt-1">
                            <span>Character counter</span>
                            <span id="bio-counter-text">0/250 (200 more needed)</span>
                        </div>
                    </div>

                    <!-- Social Media Links Section -->
                    <div class="pt-4 border-t border-slate-100 space-y-4">
                        <div>
                            <h4 class="text-sm font-black text-slate-900">Social Media Handles / Links</h4>
                            <p class="text-[11px] text-slate-500 font-medium">Link your professional social profiles to display on your public crew page.</p>
                        </div>

                        @php
                            $sLinks = is_array($professional->social_links) ? $professional->social_links : [];
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-600 mb-1">Instagram</label>
                                <input type="text" name="social_links[instagram]" value="{{ old('social_links.instagram', $sLinks['instagram'] ?? '') }}"
                                    placeholder="e.g. @janedoe or instagram.com/janedoe"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 font-bold outline-none focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-slate-600 mb-1">LinkedIn</label>
                                <input type="text" name="social_links[linkedin]" value="{{ old('social_links.linkedin', $sLinks['linkedin'] ?? '') }}"
                                    placeholder="e.g. linkedin.com/in/janedoe"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 font-bold outline-none focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-slate-600 mb-1">Facebook</label>
                                <input type="text" name="social_links[facebook]" value="{{ old('social_links.facebook', $sLinks['facebook'] ?? '') }}"
                                    placeholder="e.g. facebook.com/janedoe"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 font-bold outline-none focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-slate-600 mb-1">TikTok / X (Twitter)</label>
                                <input type="text" name="social_links[tiktok]" value="{{ old('social_links.tiktok', $sLinks['tiktok'] ?? '') }}"
                                    placeholder="e.g. @janedoe"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 font-bold outline-none focus:border-amber-500">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tab 2 Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
                    <button type="button" onclick="showProfileTab('personal', 1)"
                        class="px-6 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-all flex items-center gap-2">
                        <span>← Previous</span>
                    </button>
                    <button type="button" onclick="saveAndGoToTab('experience', 3)"
                        class="px-8 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs shadow-lg transition-all flex items-center gap-2">
                        <span>Save & Next: Experience</span>
                        <span>→</span>
                    </button>
                </div>

            </div>

            <!-- TAB 2: EXPERIENCE TAB -->
            <div id="tab-view-experience"
                class="tab-view-content hidden bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-8 shadow-sm space-y-6">

                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Education & Work Experience</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Add your educational background and work history</p>
                </div>

                <div class="space-y-6">

                    <!-- Education Card -->
                    <div class="border-0 sm:border border-slate-200/80 rounded-xl sm:rounded-2xl p-0 sm:p-5 bg-transparent sm:bg-slate-50/50 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h4 class="text-sm font-black text-slate-900">Qualifications & Education</h4>
                                <p class="text-[11px] text-slate-500 font-medium">Add your qualifications and academic background below.</p>
                            </div>
                        </div>

                        <div id="education-container" class="space-y-3">
                            @php
                                $rawEduRecords = is_array($professional->education) ? $professional->education : [];
                                $eduRecords = array_values($rawEduRecords);
                                $minEduCount = max(2, count($eduRecords));
                            @endphp
                            @for($index = 0; $index < $minEduCount; $index++)
                                @php
                                    $edu = $eduRecords[$index] ?? [];
                                    $eItem = is_string($edu) ? json_decode($edu, true) : (is_array($edu) ? $edu : []);
                                @endphp
                                <div class="p-4 bg-white border border-slate-200 rounded-xl space-y-3 text-xs">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <span class="text-[10px] font-extrabold uppercase text-slate-400">Qualification Entry #{{ $index + 1 }}</span>
                                        @if($index >= 2 || !empty($eItem['institution']) || !empty($eItem['degree']))
                                            <button type="button" onclick="this.closest('.p-4').remove()" class="text-rose-500 font-extrabold text-[10px] hover:underline">✕ Delete Entry</button>
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <input type="text" name="education[{{ $index }}][institution]"
                                            value="{{ $eItem['institution'] ?? '' }}"
                                            placeholder="University / College / School"
                                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-bold text-slate-900">
                                        <input type="text" name="education[{{ $index }}][degree]"
                                            value="{{ $eItem['degree'] ?? '' }}" placeholder="Qualification / Degree / Certification"
                                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-medium text-slate-900">
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Start Month</label>
                                            <select name="education[{{ $index }}][start_month]"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                                                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $m)
                                                    <option value="{{ $m }}" @selected(($eItem['start_month'] ?? 'Jan') == $m)>
                                                        {{ $m }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Start Year</label>
                                            <select name="education[{{ $index }}][start_year]"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                                                @for($y = date('Y'); $y >= 1990; $y--)
                                                    <option value="{{ $y }}" @selected(($eItem['start_year'] ?? '') == $y)>{{ $y }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">End Month</label>
                                            <select name="education[{{ $index }}][end_month]"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                                                <option value="Present" @selected(($eItem['end_month'] ?? '') == 'Present')>
                                                    Present</option>
                                                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $m)
                                                    <option value="{{ $m }}" @selected(($eItem['end_month'] ?? '') == $m)>{{ $m }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">End Year</label>
                                            <select name="education[{{ $index }}][end_year]"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                                                <option value="Present" @selected(($eItem['end_year'] ?? '') == 'Present')>Present
                                                </option>
                                                @for($y = date('Y'); $y >= 1990; $y--)
                                                    <option value="{{ $y }}" @selected(($eItem['end_year'] ?? '') == $y)>{{ $y }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <div class="pt-2 flex justify-center sm:justify-start">
                            <button type="button" onclick="addEducationRow()"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 border-2 border-dashed border-slate-300 hover:border-amber-400 text-slate-800 font-black text-xs shadow-xs transition-all flex items-center justify-center gap-2">
                                <span>➕ Add Qualification / Education</span>
                            </button>
                        </div>
                    </div>

                    <!-- Work Experience Card -->
                    <div class="border-0 sm:border border-slate-200/80 rounded-xl sm:rounded-2xl p-0 sm:p-5 bg-transparent sm:bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-black text-slate-900">Work Experience</h4>
                        </div>

                        <div id="experience-container" class="space-y-3">
                            @php
                                $rawExpRecords = is_array($professional->experience_records) ? $professional->experience_records : [];
                                $expRecords = array_values($rawExpRecords);
                                $minExpCount = max(2, count($expRecords));
                            @endphp
                            @for($index = 0; $index < $minExpCount; $index++)
                                @php
                                    $exp = $expRecords[$index] ?? [];
                                    $xItem = is_string($exp) ? json_decode($exp, true) : (is_array($exp) ? $exp : []);
                                @endphp
                                <div class="p-4 bg-white border border-slate-200 rounded-xl space-y-3 text-xs">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <span class="text-[10px] font-extrabold uppercase text-slate-400">Work Experience Entry #{{ $index + 1 }}</span>
                                        @if($index >= 2 || !empty($xItem['employer']) || !empty($xItem['role']))
                                            <button type="button" onclick="this.closest('.p-4').remove()" class="text-rose-500 font-extrabold text-[10px] hover:underline">✕ Delete Entry</button>
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <input type="text" name="experience_records[{{ $index }}][employer]"
                                            value="{{ $xItem['employer'] ?? '' }}" placeholder="Organization / Employer"
                                            class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-bold text-slate-900">
                                        <input type="text" name="experience_records[{{ $index }}][role]"
                                            value="{{ $xItem['role'] ?? '' }}"
                                            placeholder="Role (e.g. Lead Usher, VIP Coordinator)"
                                            class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-bold text-slate-900">
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Start Month</label>
                                            <select name="experience_records[{{ $index }}][start_month]"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                                                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $m)
                                                    <option value="{{ $m }}" @selected(($xItem['start_month'] ?? 'Jan') == $m)>
                                                        {{ $m }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Start Year</label>
                                            <select name="experience_records[{{ $index }}][start_year]"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                                                @for($y = date('Y'); $y >= 1990; $y--)
                                                    <option value="{{ $y }}" @selected(($xItem['start_year'] ?? '') == $y)>{{ $y }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">End Month</label>
                                            <select name="experience_records[{{ $index }}][end_month]"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                                                <option value="Present" @selected(($xItem['end_month'] ?? '') == 'Present')>
                                                    Present</option>
                                                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $m)
                                                    <option value="{{ $m }}" @selected(($xItem['end_month'] ?? '') == $m)>{{ $m }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">End Year</label>
                                            <select name="experience_records[{{ $index }}][end_year]"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                                                <option value="Present" @selected(($xItem['end_year'] ?? '') == 'Present')>Present
                                                </option>
                                                @for($y = date('Y'); $y >= 1990; $y--)
                                                    <option value="{{ $y }}" @selected(($xItem['end_year'] ?? '') == $y)>{{ $y }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <input type="text" name="experience_records[{{ $index }}][responsibilities]"
                                        value="{{ $xItem['responsibilities'] ?? '' }}"
                                        placeholder="Duties & Key achievements..."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-medium text-slate-900">

                                    <!-- Reference Details -->
                                    <div class="p-3 bg-amber-50/60 border border-amber-200/80 rounded-lg space-y-2">
                                        <span class="text-[10px] font-black uppercase text-amber-900 block">📞 Reference Details</span>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <input type="text" name="experience_records[{{ $index }}][ref_name]"
                                                value="{{ $xItem['ref_name'] ?? '' }}" placeholder="Ref Person Name"
                                                class="bg-white border border-slate-200 rounded-md px-2.5 py-1.5 text-xs text-slate-900">
                                            <input type="text" name="experience_records[{{ $index }}][ref_contact]"
                                                value="{{ $xItem['ref_contact'] ?? '' }}" placeholder="Ref Phone / Email"
                                                class="bg-white border border-slate-200 rounded-md px-2.5 py-1.5 text-xs text-slate-900">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <div class="pt-2 flex justify-center sm:justify-start">
                            <button type="button" onclick="addExperienceRow()"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 border-2 border-dashed border-slate-300 hover:border-amber-400 text-slate-800 font-black text-xs shadow-xs transition-all flex items-center justify-center gap-2">
                                <span>➕ Add Work Experience</span>
                            </button>
                        </div>
                    </div>

                    <!-- Key Skills Section (Now on Experience Tab) -->
                    <div class="border-0 sm:border border-slate-200/80 rounded-xl sm:rounded-2xl p-0 sm:p-5 bg-transparent sm:bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <h4 class="text-sm font-black text-slate-900">Select your Key Skills</h4>
                                <p class="text-xs text-slate-500 font-medium">Click on skill tags below to select or deselect your key capabilities.</p>
                            </div>
                            <span id="spotify-skills-counter" class="px-3.5 py-1 rounded-full bg-slate-100 text-slate-700 font-black text-xs border border-slate-200">
                                0 Selected
                            </span>
                        </div>

                        <!-- Hidden Form Input -->
                        <input type="hidden" name="skills" id="hidden_skills_input" value="{{ old('skills', $professional->skills) }}">

                        <!-- Simple Skill Chips Container -->
                        <div class="flex flex-wrap gap-2.5 py-2" id="skills-grid-container">
                            @php
                                $presetSkills = [
                                    'Registration & Check-In',
                                    'VIP Protocol',
                                    'Crowd Control',
                                    'Mic Handling & MC',
                                    'Bilingual (EN / FR)',
                                    'Mixology & Bar',
                                    'POS & Ticket Sales',
                                    'Brand Activation',
                                    'First Aid Certified',
                                    'Event Logistics',
                                    'Red Carpet Assist',
                                    'Lead Ushering',
                                    'Stage Management',
                                    'Hospitality & Catering',
                                ];
                                $currentSkills = old('skills', $professional->skills);
                                $selectedSkillsArr = is_array($currentSkills) ? $currentSkills : array_map('trim', explode(',', (string)$currentSkills));
                                $selectedSkillsArr = array_values(array_filter($selectedSkillsArr));
                            @endphp

                            @foreach($presetSkills as $skillName)
                                @php
                                    $isSelected = in_array($skillName, $selectedSkillsArr);
                                @endphp
                                <button type="button"
                                        class="simple-skill-chip px-4 py-2.5 rounded-2xl font-black text-xs transition-all flex items-center gap-2 cursor-pointer select-none border {{ $isSelected ? 'bg-slate-900 text-amber-400 border-amber-400 shadow-md ring-2 ring-amber-400/30' : 'bg-slate-100/90 hover:bg-slate-200 text-slate-700 border-slate-200' }}"
                                        data-skill="{{ $skillName }}"
                                        @if($isSelected) data-selected="true" @endif
                                        onclick="toggleSimpleSkillChip(this)">
                                    <span class="chip-icon">{{ $isSelected ? '✓' : '⚡' }}</span>
                                    <span>{{ $skillName }}</span>
                                </button>
                            @endforeach

                            <!-- Custom Selected Skills Pre-rendered -->
                            @foreach($selectedSkillsArr as $customSkill)
                                @if(!in_array($customSkill, $presetSkills) && !empty($customSkill))
                                    <button type="button"
                                            class="simple-skill-chip px-4 py-2.5 rounded-2xl font-black text-xs transition-all flex items-center gap-2 cursor-pointer select-none border bg-slate-900 text-amber-400 border-amber-400 shadow-md ring-2 ring-amber-400/30"
                                            data-skill="{{ $customSkill }}"
                                            data-selected="true"
                                            onclick="toggleSimpleSkillChip(this)">
                                        <span class="chip-icon">✓</span>
                                        <span>{{ $customSkill }}</span>
                                    </button>
                                @endif
                            @endforeach
                        </div>

                        <!-- Custom Skill Write-in -->
                        <div class="pt-2 flex items-center gap-2">
                            <input type="text" id="custom-skill-input" placeholder="Type a custom skill (e.g. Protocol Officer) and click Add..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 outline-none focus:border-amber-500">
                            <button type="button" onclick="addCustomSimpleSkillChip()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs shrink-0 transition-all cursor-pointer">+ Add Skill</button>
                        </div>
                    </div>

                    <!-- Capabilities & Services Offered Card -->
                    <div class="border-0 sm:border border-slate-200/80 rounded-xl sm:rounded-2xl p-0 sm:p-5 bg-transparent sm:bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-black text-slate-900">Capabilities & Services Offered</h4>
                                <p class="text-[11px] text-slate-500 font-medium">Add service cards that display under "Key Skills & What I Can Help You With"</p>
                            </div>
                        </div>

                        <div id="services-container" class="space-y-3">
                            @php $servicesList = $professional->services ?: []; @endphp
                            @if(empty($servicesList) || count($servicesList) === 0)
                                <p class="text-xs text-slate-400 italic py-4 text-center">No custom capabilities added yet. Default service cards based on your skills will display.</p>
                            @else
                                @foreach($servicesList as $index => $svc)
                                    @php $sItem = is_string($svc) ? json_decode($svc, true) : $svc; @endphp
                                    <div class="p-3.5 sm:p-4 bg-white border border-slate-200/90 rounded-xl space-y-3 text-xs relative shadow-xs">
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                            <span class="text-[10px] font-extrabold uppercase text-slate-400">Service Card #{{ $index + 1 }}</span>
                                            <button type="button" onclick="this.closest('.p-4').remove()" class="text-rose-500 font-extrabold text-[10px] hover:underline">✕ Delete Card</button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                            <div class="sm:col-span-3">
                                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Icon Emoji</label>
                                                <input type="text" id="svc_icon_{{ $index }}" name="services[{{ $index }}][icon]"
                                                    value="{{ $sItem['icon'] ?? '👥' }}" placeholder="e.g. 👥, 👑, 🍸, 🛡️"
                                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-bold text-slate-900 text-center">
                                                <div class="flex items-center gap-1 mt-1.5 flex-wrap justify-center">
                                                    @foreach(['👥', '👑', '🍸', '🎙️', '🛡️', '📋', '🌟', '💼'] as $emoji)
                                                        <button type="button" onclick="document.getElementById('svc_icon_{{ $index }}').value = '{{ $emoji }}'" class="w-6 h-6 rounded bg-slate-100 hover:bg-amber-100 text-xs flex items-center justify-center" title="Select {{ $emoji }}">{{ $emoji }}</button>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="sm:col-span-5">
                                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Service Heading / Title *</label>
                                                <input type="text" name="services[{{ $index }}][title]"
                                                    value="{{ $sItem['title'] ?? ($sItem['name'] ?? '') }}" placeholder="e.g. Corporate Event Ushering"
                                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-bold text-slate-900">
                                            </div>
                                            <div class="sm:col-span-4">
                                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Tagline / Badge (Optional)</label>
                                                <input type="text" name="services[{{ $index }}][tagline]"
                                                    value="{{ $sItem['tagline'] ?? '' }}" placeholder="e.g. VIP Protocol, Lead Usher"
                                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-bold text-slate-900">
                                            </div>
                                            <div class="sm:col-span-12">
                                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Service Description / Details</label>
                                                <textarea name="services[{{ $index }}][description]" rows="2"
                                                    placeholder="Describe what you provide for this service..."
                                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-slate-900 text-xs">{{ $sItem['description'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="pt-2 flex justify-center sm:justify-start">
                            <button type="button" onclick="addServiceRow()"
                                class="w-full sm:w-auto px-5 py-3 rounded-xl bg-white hover:bg-slate-50 border-2 border-dashed border-slate-300 hover:border-amber-400 text-slate-800 font-black text-xs shadow-xs transition-all flex items-center justify-center gap-2">
                                <span>➕ Add Service Card</span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Tab 3 Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
                    <button type="button" onclick="showProfileTab('bio', 2)"
                        class="px-6 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-all flex items-center gap-2">
                        <span>← Previous</span>
                    </button>
                    <button type="button" onclick="saveAndGoToTab('photos', 4)"
                        class="px-8 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs shadow-lg transition-all flex items-center gap-2">
                        <span>Save & Next: Photos</span>
                        <span>→</span>
                    </button>
                </div>

            </div>

            <!-- TAB 3: PHOTOS TAB -->
            <div id="tab-view-photos"
                class="tab-view-content hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-8">

                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Profile & Portfolio Photos</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Manage your profile headshot, cover banner image, and event
                        portfolio gallery photos.</p>
                </div>

                <!-- SECTION 1: Profile Avatar & Cover Banner Upload Controls -->
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-slate-900 flex items-center gap-2">
                        <span>Profile Headshot & Cover Banner</span>
                        <span
                            class="text-[10px] text-amber-800 bg-amber-100 font-extrabold px-2 py-0.5 rounded-full">Displayed
                            on Public Booking Profile</span>
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Profile Headshot Avatar Card -->
                        <div class="p-5 bg-slate-50 border border-slate-200/90 rounded-2xl space-y-4 shadow-xs">
                            <div class="flex items-center gap-4">
                                <div class="relative shrink-0">
                                    @if($avatarUrl)
                                        <img id="tab-avatar-photo-preview" src="{{ $avatarUrl }}"
                                            class="w-20 h-20 rounded-full object-cover border-2 border-white shadow-md">
                                    @else
                                        <div id="tab-avatar-photo-placeholder"
                                            class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-2xl flex items-center justify-center border-2 border-white shadow-md">
                                            {{ strtoupper(substr($professional->full_name, 0, 1)) }}
                                        </div>
                                        <img id="tab-avatar-photo-preview" src=""
                                            class="w-20 h-20 rounded-full object-cover border-2 border-white shadow-md hidden">
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <h5 class="text-xs font-black text-slate-900">Profile Headshot Avatar</h5>
                                    <p class="text-[11px] text-slate-500 font-medium leading-tight">Upload a clear, smiling
                                        portrait facing forward.</p>
                                    <span class="inline-block text-[10px] text-slate-400 font-semibold">JPG, PNG (Max
                                        5MB)</span>
                                </div>
                            </div>

                            <label
                                class="block w-full text-center py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs rounded-xl cursor-pointer shadow-sm transition-all border border-slate-900">
                                Change Profile Headshot
                                <input type="file" name="profile_photo" accept="image/*" class="hidden"
                                    onchange="previewAvatarImage(this)">
                            </label>
                        </div>

                        <!-- Cover Banner Photo Card -->
                        <div class="p-5 bg-slate-50 border border-slate-200/90 rounded-2xl space-y-4 shadow-xs">
                            <div class="space-y-2">
                                <div class="relative h-20 rounded-xl bg-slate-900 overflow-hidden border border-slate-200">
                                    @if($professional->cover_photo)
                                        <img id="tab-cover-photo-preview" src="{{ $coverUrl }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div id="tab-cover-photo-placeholder"
                                            class="w-full h-full bg-gradient-to-r from-slate-950 via-slate-900 to-amber-950 flex items-center justify-center text-[10px] text-amber-400 font-bold">
                                            <span>Default Cover Banner</span>
                                        </div>
                                        <img id="tab-cover-photo-preview" src="" class="w-full h-full object-cover hidden">
                                    @endif
                                </div>
                                <div>
                                    <h5 class="text-xs font-black text-slate-900">Cover Banner Image</h5>
                                    <p class="text-[11px] text-slate-500 font-medium">Landscape header image for your
                                        profile top banner.</p>
                                </div>
                            </div>

                            <label
                                class="block w-full text-center py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs rounded-xl cursor-pointer shadow-sm transition-all border border-slate-900">
                                Change Cover Banner Photo
                                <input type="file" name="cover_photo" accept="image/*" class="hidden"
                                    onchange="previewCoverImage(this)">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Event Portfolio Gallery (4 Photo Slots) -->
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    <div>
                        <h4 class="text-xs font-black text-slate-900">My Usher Event Portfolio Gallery (4 Photos Max)</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Upload up to 4 clean, professional on-duty event photographs from your usher shifts.</p>
                    </div>

                    @php
                        $rawProfileGallery = is_array($professional->gallery_photos) ? $professional->gallery_photos : [];
                        $uniqueProfileGallery = array_values(array_slice(array_unique(array_filter($rawProfileGallery)), 0, 4));
                    @endphp

                    <!-- 4 Portfolio Photo Upload & Preview Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @for($i = 0; $i < 4; $i++)
                            @php
                                $existingPhoto = $uniqueProfileGallery[$i] ?? null;
                                $existingUrl = $existingPhoto ? get_storage_url($existingPhoto) : null;
                            @endphp
                            <div class="relative aspect-square border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 flex flex-col items-center justify-center p-2 text-center overflow-hidden hover:border-amber-400 transition-colors group shadow-xs">
                                
                                <!-- Preview image if exists -->
                                <img id="tab-gallery-preview-{{ $i }}" src="{{ $existingUrl ?? '' }}"
                                    class="absolute inset-0 w-full h-full object-cover rounded-xl {{ $existingUrl ? '' : 'hidden' }}">
                                
                                <!-- Placeholder when empty -->
                                <div id="tab-gallery-placeholder-{{ $i }}"
                                    class="flex flex-col items-center justify-center {{ $existingUrl ? 'hidden' : '' }}">
                                    <span class="text-2xl text-slate-400 mb-1">📷</span>
                                    <span class="text-xs font-bold text-slate-700">Photo Slot {{ $i + 1 }}</span>
                                    <span class="text-[10px] text-amber-600 font-bold mt-0.5">Click to Upload</span>
                                </div>

                                <!-- Input for uploading photo -->
                                <input type="file" name="gallery_photos[]" accept="image/*"
                                    onchange="previewModalGalleryImage(this, 'tab-gallery-preview-{{ $i }}', 'tab-gallery-placeholder-{{ $i }}')"
                                    class="absolute inset-0 opacity-0 cursor-pointer z-10">

                                <!-- Hover badge / Delete button for filled slot -->
                                @if($existingPhoto)
                                    <button type="button" onclick="confirmDeleteModalGalleryPhoto('{{ $existingPhoto }}')"
                                        class="absolute top-2 right-2 z-20 w-7 h-7 rounded-lg bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-xs shadow-md"
                                        title="Delete Photo">
                                        🗑️
                                    </button>
                                    <div class="absolute bottom-1.5 left-1.5 right-1.5 z-20 bg-slate-900/80 backdrop-blur text-white text-[9px] font-bold py-1 px-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none text-center">
                                        Click to Replace Photo {{ $i + 1 }}
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Footnote -->
                <p class="text-[11px] text-slate-400 italic">📌 Photos must be taken while on duty at an event.</p>

                <!-- Tab 4 Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
                    <button type="button" onclick="showProfileTab('experience', 3)"
                        class="px-6 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-all flex items-center gap-2">
                        <span>← Previous</span>
                    </button>
                    <button type="button" onclick="saveAndGoToTab('availability', 5)"
                        class="px-8 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs shadow-lg transition-all flex items-center gap-2">
                        <span>Save & Next: Availability</span>
                        <span>→</span>
                    </button>
                </div>

            </div>

            <!-- TAB 5: AVAILABILITY TAB -->
            <div id="tab-view-availability"
                class="tab-view-content hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Availability & Preferred Locations</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Set your working calendar availability and preferred event locations</p>
                </div>

                <!-- Preferred Working Locations Input Field -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <label class="block text-xs font-black text-slate-900">Preferred Working Location(s) <span class="text-slate-400 font-normal">(Comma Separated)</span></label>
                    @php
                        $prefLocs = is_array($professional->preferred_locations) ? implode(', ', $professional->preferred_locations) : $professional->preferred_locations;
                    @endphp
                    <input type="text" name="preferred_locations" value="{{ old('preferred_locations', $prefLocs) }}"
                        placeholder="e.g. Nairobi, Mombasa, Kisumu, Diani"
                        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-xs font-bold outline-none focus:border-amber-500">
                    <p class="text-[11px] text-slate-500 font-medium">Specify cities or regions where you prefer to accept shift assignments.</p>
                </div>

                    <!-- Confirmed Booked Shifts Subsection -->
                    @php
                        $acceptedOffers = $acceptedOffers ?? (isset($assignments) ? $assignments->where('status', 'accepted') : collect());
                    @endphp
                    @if($acceptedOffers->count() > 0)
                        <div class="pt-3 border-t border-slate-200 space-y-2">
                            <span class="text-[11px] font-black text-slate-900 uppercase tracking-wider block">✓ Confirmed
                                Accepted Bookings</span>
                            <div class="space-y-2">
                                @foreach($acceptedOffers as $job)
                                    @php $sr = $job->staffingRequest; @endphp
                                    <div
                                        class="p-3 bg-white rounded-xl border border-emerald-300 flex items-center justify-between text-xs">
                                        <div>
                                            <span
                                                class="font-extrabold text-slate-900">{{ $sr->event_name ?? 'Event Shift' }}</span>
                                            <span class="text-slate-500 font-medium ml-2">•
                                                {{ $sr->event_date ? $sr->event_date->format('M j, Y') : 'Confirmed' }}</span>
                                        </div>
                                        <span
                                            class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-900 font-black text-[10px] uppercase">
                                            ✓ Confirmed & Booked
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                <!-- Shift Preferences & Travel Radius Row -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-5 rounded-2xl bg-slate-50/70 border border-slate-200 text-xs font-bold">
                    <div>
                        <label class="block text-slate-900 mb-2">Preferred Shift Duration</label>
                        <div class="space-y-2 font-medium text-slate-700">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-amber-500 rounded">
                                <span>Full-Day Shifts (8+ Hours)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-amber-500 rounded">
                                <span>Half-Day / Evening Events (4-6 Hours)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 text-amber-500 rounded">
                                <span>Rehearsal & Setup Sessions</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-900 mb-2">Travel Radius & Locations</label>
                        <select name="travel_radius"
                            class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 font-bold text-slate-900 outline-none focus:border-amber-500">
                            <option value="city">Within Nairobi City Limits Only</option>
                            <option value="25km">Up to 25 km radius</option>
                            <option value="50km" selected>Up to 50 km radius</option>
                            <option value="nationwide">Nationwide / Out-of-town travel OK</option>
                        </select>
                        <span class="text-[10px] text-slate-400 font-medium block mt-1.5">You can accept out-of-town shifts
                            if transport/accommodation is provided by the organizer.</span>
                    </div>
                </div>

                <!-- Interactive Working Calendar Suite -->
                <div class="border border-slate-200 rounded-2xl p-6 bg-slate-50/50 space-y-5">
                    <input type="hidden" name="availability_dates" id="hidden_availability_dates_input" value='{{ json_encode($professional->availability_dates ?? []) }}'>

                    <!-- Calendar Header & Navigation Controls -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/80 pb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 id="calendar-month-title" class="text-sm font-black text-slate-900">{{ date('F Y') }}</h4>
                                <span
                                    class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-black uppercase">Working
                                    Schedule</span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Click any future date to toggle availability
                                on or off (Past and today's dates are locked)</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <label
                                class="flex items-center gap-1.5 text-xs font-bold text-slate-700 cursor-pointer bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-xs">
                                <input type="checkbox" id="checkbox-select-all-days" onchange="toggleSelectAllDates(this)"
                                    class="w-4 h-4 text-amber-500 rounded cursor-pointer">
                                <span>Select all future days</span>
                            </label>
                            <button type="button" onclick="selectWeekendsOnly()"
                                class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-800 font-extrabold text-xs shadow-xs cursor-pointer">
                                Future Weekends Only
                            </button>
                            <button type="button" onclick="clearAllDates()"
                                class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-rose-700 font-extrabold text-xs shadow-xs cursor-pointer">
                                Clear
                            </button>
                            <div class="flex items-center gap-1">
                                <button type="button" id="calendar-prev-btn" onclick="changeCalendarMonth(-1)"
                                    class="w-8 h-8 rounded-xl bg-white border border-slate-200 font-bold text-xs hover:bg-slate-100 flex items-center justify-center cursor-pointer">←</button>
                                <button type="button" id="calendar-next-btn" onclick="changeCalendarMonth(1)"
                                    class="w-8 h-8 rounded-xl bg-white border border-slate-200 font-bold text-xs hover:bg-slate-100 flex items-center justify-center cursor-pointer">→</button>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Days Grid Container -->
                    <div id="calendar-days-grid" class="grid grid-cols-7 gap-2 text-center text-xs font-bold">
                    </div>

                    <!-- Legend & Availability Summary Counter Bar -->
                    <div
                        class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 text-xs font-bold text-slate-600 border-t border-slate-200">
                        <div class="flex items-center gap-5">
                            <span class="flex items-center gap-1.5">
                                <span class="w-3.5 h-3.5 rounded-md bg-white border border-slate-300"></span>
                                <span class="text-slate-600">Default / Off-duty</span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-3.5 h-3.5 rounded-md bg-amber-400 border border-amber-500"></span>
                                <span class="text-slate-900 font-black">Available</span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-3.5 h-3.5 rounded-md bg-slate-900 border border-slate-900"></span>
                                <span class="text-slate-900 font-black">Booked Shift</span>
                            </span>
                        </div>

                        <div class="text-right text-[11px] font-extrabold text-amber-900 bg-amber-100 px-3 py-1 rounded-xl">
                            <span id="available-counter-text">0 Available Days Selected</span>
                        </div>
                    </div>

                </div>

                <!-- Tab 5 Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
                    <button type="button" onclick="showProfileTab('photos', 4)"
                        class="px-6 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-all flex items-center gap-2">
                        <span>← Previous</span>
                    </button>
                    <button type="button" onclick="saveAndGoToTab('charges', 6)"
                        class="px-8 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs shadow-lg transition-all flex items-center gap-2">
                        <span>Save & Next: Labour Charges</span>
                        <span>→</span>
                    </button>
                </div>

            </div>

            <!-- TAB 5: LABOUR CHARGES TAB -->
            <div id="tab-view-charges"
                class="tab-view-content hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Labour Charges</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Set your daily rates and optional multi-day discount</p>
                </div>

                <!-- Rate Inputs (2-Column Layout) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs font-bold">

                    <div>
                        <label class="block text-slate-700 mb-1">Currency Preference *</label>
                        <select name="currency"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500 focus:bg-white">
                            <option value="KES" selected>KSh KES (Kenyan Shillings)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1">Pricing Rate Mode *</label>
                        <select name="rate_type"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500 focus:bg-white">
                            <option value="fixed" @selected(old('rate_type', $professional->rate_type ?? 'fixed') == 'fixed')>
                                Fixed Price Rate</option>
                            <option value="negotiable" @selected(old('rate_type', $professional->rate_type) == 'negotiable')>
                                Negotiable Rate</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1">1 Day Rate (Per day)</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 font-black text-slate-400">Rate</span>
                            <input type="number" name="one_day_rate"
                                value="{{ old('one_day_rate', $professional->one_day_rate ?? 5000) }}" step="1"
                                placeholder="5000"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-16 pr-4 py-3 text-slate-900 font-black text-sm outline-none focus:border-amber-500 focus:bg-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1">Hourly Rate (Optional)</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 font-black text-slate-400">Hourly</span>
                            <input type="number" name="hourly_rate"
                                value="{{ old('hourly_rate', $professional->hourly_rate ?? 25) }}" step="0.5"
                                placeholder="25"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-16 pr-4 py-3 text-slate-900 font-black text-sm outline-none focus:border-amber-500 focus:bg-white">
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-slate-700 mb-1">Discount (%) (Optional — applied on multi-day
                            bookings)</label>
                        <div class="relative flex items-center">
                            <input type="number" name="multi_day_discount"
                                value="{{ old('multi_day_discount', $professional->multi_day_discount ?? 5) }}" min="0"
                                max="100" placeholder="5"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-4 pr-10 py-3 text-slate-900 font-black text-sm outline-none focus:border-amber-500 focus:bg-white">
                            <span class="absolute right-4 font-black text-slate-400">%</span>
                        </div>
                    </div>

                </div>

                <!-- Tab 5 Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" onclick="showProfileTab('availability', 4)"
                        class="px-6 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-all flex items-center gap-2">
                        <span>← Previous</span>
                    </button>
                    <button type="button" onclick="saveAndGoToTab('reviews', 6)"
                        class="px-8 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs shadow-lg transition-all flex items-center gap-2">
                        <span>Save & Next: Reviews</span>
                        <span>→</span>
                    </button>
                </div>

            </div>

            <!-- TAB 6: REVIEWS & VISIBILITY TAB -->
            <div id="tab-view-reviews"
                class="tab-view-content hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">Client Reviews & Visibility Control</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Control which client reviews are visible on your public crew profile page</p>
                </div>

                @if($professional->reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($professional->reviews as $rev)
                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <div class="text-rose-500 text-xs font-black">
                                            @for($s = 1; $s <= 5; $s++)
                                                {{ $s <= $rev->rating ? '★' : '☆' }}
                                            @endfor
                                        </div>
                                        <span class="text-xs font-black text-slate-900">{{ $rev->client_name }}</span>
                                        @if($rev->client_company)
                                            <span class="text-[10px] text-slate-400 font-bold">({{ $rev->client_company }})</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-700 italic font-medium leading-relaxed">
                                        "{{ $rev->comment }}"
                                    </p>
                                    <span class="text-[10px] text-slate-400 font-semibold block">
                                        Submitted {{ $rev->created_at ? $rev->created_at->diffForHumans() : 'recently' }}
                                    </span>
                                </div>

                                <form method="POST" action="{{ route('professional.reviews.toggle-visibility', $rev->id) }}" class="shrink-0">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-1.5 {{ $rev->is_visible ? 'bg-emerald-500 text-white hover:bg-emerald-600' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
                                        <span>{{ $rev->is_visible ? '✓ Visible on Profile' : '👁️ Hidden from Profile' }}</span>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-2">
                        <span class="text-3xl block">⭐</span>
                        <h4 class="text-sm font-black text-slate-900">No Client Reviews Yet</h4>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            When clients complete event bookings with you, their verified reviews will appear here. You can decide which reviews to show or hide from your public profile.
                        </p>
                    </div>
                @endif

                <!-- Tab 7 Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
                    <button type="button" onclick="showProfileTab('charges', 6)"
                        class="px-6 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-all flex items-center gap-2">
                        <span>← Previous</span>
                    </button>
                    <button type="button" onclick="submitFinalProfile(event)"
                        class="px-8 py-3.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-lg transition-all">
                        Save All Profile Details ⚡
                    </button>
                </div>

            </div>

        </form>

    </div>

    <script>
        function setActiveTabTarget(tabId) {
            const input = document.getElementById('active-tab-input');
            if (input) input.value = tabId;
        }
    </script>

    <!-- Hidden Form for Deleting Gallery Photo -->
    <form id="modal-delete-gallery-form" method="POST" action="{{ route('professional.gallery.delete') }}" class="hidden">
        @csrf
        <input type="hidden" name="photo_path" id="modal-delete-photo-path">
    </form>

    <!-- Counter-Offer Negotiation Modal -->
    <div id="counter-offer-modal"
        class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <span>💬 Counter-Offer & Travel Terms</span>
                </h4>
                <button type="button" onclick="closeCounterOfferModal()"
                    class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
            </div>

            <form id="counter-offer-form" method="POST" action="" class="space-y-4 text-xs font-bold">
                @csrf

                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900">
                    <span>Event Shift: <strong id="modal-counter-event-name">Event Shift</strong></span>
                    <span class="block text-[11px] text-amber-800 font-medium">Original Offered Rate: <strong
                            id="modal-counter-original-rate">KES 5,000</strong></span>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1">Your Counter Quote Amount *</label>
                    <input type="number" name="custom_quote_amount" id="modal-custom-quote" required min="1"
                        placeholder="Enter your proposed rate"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 font-black text-sm outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-700 mb-1">Travel Allowance Fee (Optional)</label>
                        <input type="number" name="travel_fee" min="0" placeholder="e.g. 1500"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-bold outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-slate-700 mb-1">Accommodation/Food Fee (Optional)</label>
                        <input type="number" name="accommodation_fee" min="0" placeholder="e.g. 1000"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-bold outline-none focus:border-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1">Travel Details & Additional Notes *</label>
                    <textarea name="notes" rows="3" required
                        placeholder="Specify your travel requirements, arrival time, uniform preferences, or additional terms for the client..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-900 font-medium outline-none focus:border-amber-500"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeCounterOfferModal()"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black shadow-md">
                        Send Counter-Offer to Client 🚀
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function syncHiddenAvailabilityInputs() {
            const form = document.getElementById('profile-main-form');
            if (!form) return;

            form.querySelectorAll('input[name="availability_dates[]"]').forEach(el => el.remove());

            const selectedCells = document.querySelectorAll('.calendar-date-cell.bg-amber-400');
            selectedCells.forEach(cell => {
                const dayNum = cell.getAttribute('data-day');
                if (dayNum) {
                    const formattedDay = String(dayNum).padStart(2, '0');
                    const dateStr = `2026-09-${formattedDay}`;
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'availability_dates[]';
                    hiddenInput.value = dateStr;
                    form.appendChild(hiddenInput);
                }
            });
        }

        function updateAvailableDaysCounter() {
            const activeCount = document.querySelectorAll('.calendar-date-cell.bg-amber-400').length;
            const counterSpan = document.getElementById('available-counter-text');
            if (counterSpan) {
                counterSpan.innerText = `${activeCount} Available Days Selected`;
            }
            syncHiddenAvailabilityInputs();
        }

        function openCounterOfferModal(requestId, eventName, budget) {
            const modal = document.getElementById('counter-offer-modal');
            const form = document.getElementById('counter-offer-form');
            const nameEl = document.getElementById('modal-counter-event-name');
            const rateEl = document.getElementById('modal-counter-original-rate');
            const quoteInput = document.getElementById('modal-custom-quote');

            if (modal && form) {
                form.action = `/requests/${requestId}/proposals`;
                if (nameEl) nameEl.innerText = eventName;
                if (rateEl) rateEl.innerText = `KES ${budget.toLocaleString()}`;
                if (quoteInput) quoteInput.value = budget;
                modal.classList.remove('hidden');
            }
        }

        function closeCounterOfferModal() {
            const modal = document.getElementById('counter-offer-modal');
            if (modal) modal.classList.add('hidden');
        }
    </script>

    <script>
        function submitFinalProfile(event) {
            if (event) event.preventDefault();
            setActiveTabTarget('charges');

            const form = document.getElementById('profile-main-form');
            if (!form) return;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Saving Profile...',
                    text: 'Updating all your profile details and preferences...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Profile Updated! 🎉',
                        text: 'All 5 steps of your profile details have been saved successfully.',
                        icon: 'success',
                        confirmButtonText: 'Awesome! 👍',
                        confirmButtonColor: '#f59e0b'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    window.location.reload();
                }
            })
            .catch(err => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Profile Updated! 🎉',
                        text: 'All your profile details have been saved successfully.',
                        icon: 'success',
                        confirmButtonText: 'Awesome! 👍',
                        confirmButtonColor: '#f59e0b'
                    }).then(() => {
                        form.submit();
                    });
                } else {
                    form.submit();
                }
            });
        }

        function saveAndGoToTab(nextTabId, stepNum) {
            showProfileTab(nextTabId, stepNum);
            const input = document.getElementById('active-tab-input');
            if (input) input.value = nextTabId;

            const form = document.getElementById('profile-main-form');
            if (form) {
                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(res => res.json()).catch(err => console.log('Silent auto-save:', err));
            }
        }

        function showProfileTab(tabId, stepNum = 1) {
            document.querySelectorAll('.tab-nav-btn').forEach(btn => {
                btn.className = 'tab-nav-btn px-4 py-3 rounded-2xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap';
            });
            document.querySelectorAll('.tab-view-content').forEach(view => {
                view.classList.add('hidden');
            });

            const activeBtn = document.getElementById('tab-nav-' + tabId);
            const activeView = document.getElementById('tab-view-' + tabId);

            if (activeBtn) activeBtn.className = 'tab-nav-btn px-4 py-3 rounded-2xl bg-slate-900 text-amber-400 font-black shadow-md border border-slate-900 transition-all flex items-center gap-2 whitespace-nowrap';
            if (activeView) activeView.classList.remove('hidden');

            // Update Step Progress Tracker Bar
            const progressPct = stepNum * 20;
            const progressLabel = document.getElementById('wizard-progress-label');
            const progressBar = document.getElementById('wizard-progress-bar');
            if (progressLabel) progressLabel.innerText = `Step ${stepNum} of 5 (${progressPct}% Complete)`;
            if (progressBar) progressBar.style.width = `${progressPct}%`;
        }

        function updateBioCounter(textarea) {
            const len = textarea.value.length;
            const counterText = document.getElementById('bio-counter-text');
            if (counterText) {
                if (len < 250) {
                    counterText.innerText = `${len}/250 (${250 - len} more needed)`;
                } else {
                    counterText.innerText = `${len} characters entered`;
                }
            }
        }

        function previewCoverImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const topPreview = document.getElementById('cover-photo-preview');
                    const tabPreview = document.getElementById('tab-cover-photo-preview');
                    const tabPlaceholder = document.getElementById('tab-cover-photo-placeholder');

                    if (topPreview) {
                        topPreview.src = e.target.result;
                        topPreview.classList.remove('hidden');
                    }
                    if (tabPreview) {
                        tabPreview.src = e.target.result;
                        tabPreview.classList.remove('hidden');
                    }
                    if (tabPlaceholder) {
                        tabPlaceholder.classList.add('hidden');
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewAvatarImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const topImg = document.getElementById('avatar-photo-preview');
                    const topPlaceholder = document.getElementById('avatar-photo-placeholder');
                    const tabImg = document.getElementById('tab-avatar-photo-preview');
                    const tabPlaceholder = document.getElementById('tab-avatar-photo-placeholder');

                    if (topImg) {
                        topImg.src = e.target.result;
                    } else if (topPlaceholder) {
                        topPlaceholder.innerHTML = `<img src="${e.target.result}" class="w-full h-full rounded-full object-cover">`;
                    }

                    if (tabImg) {
                        tabImg.src = e.target.result;
                        tabImg.classList.remove('hidden');
                    }
                    if (tabPlaceholder) {
                        tabPlaceholder.classList.add('hidden');
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewModalGalleryImage(input, previewId, placeholderId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewEl = document.getElementById(previewId);
                    const placeholderEl = document.getElementById(placeholderId);
                    if (previewEl) {
                        previewEl.src = e.target.result;
                        previewEl.classList.remove('hidden');
                    }
                    if (placeholderEl) {
                        placeholderEl.classList.add('hidden');
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function addEducationRow() {
            const container = document.getElementById('education-container');
            const idx = container.querySelectorAll('.p-4').length;
            const div = document.createElement('div');
            div.className = 'p-3.5 sm:p-4 bg-white border border-slate-200/90 rounded-xl space-y-3 text-xs shadow-xs';

            let currentYear = new Date().getFullYear();
            let yearOptions = '';
            for (let y = currentYear; y >= 1990; y--) {
                yearOptions += `<option value="${y}">${y}</option>`;
            }
            let monthOptions = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'].map(m => `<option value="${m}">${m}</option>`).join('');

            div.innerHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="text" name="education[${idx}][institution]" placeholder="University / College / School" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-bold text-slate-900">
                    <input type="text" name="education[${idx}][degree]" placeholder="Degree / Certification" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-medium text-slate-900">
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Start Month</label>
                        <select name="education[${idx}][start_month]" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                            ${monthOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Start Year</label>
                        <select name="education[${idx}][start_year]" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                            ${yearOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">End Month</label>
                        <select name="education[${idx}][end_month]" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                            <option value="Present">Present</option>
                            ${monthOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">End Year</label>
                        <select name="education[${idx}][end_year]" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                            <option value="Present">Present</option>
                            ${yearOptions}
                        </select>
                    </div>
                </div>
            `;
            container.appendChild(div);
        }

        function addServiceRow() {
            const container = document.getElementById('services-container');
            const idx = container.querySelectorAll('.p-4').length;
            const emptyNotice = container.querySelector('p.italic');
            if (emptyNotice) emptyNotice.remove();

            const div = document.createElement('div');
            div.className = 'p-3.5 sm:p-4 bg-white border border-slate-200/90 rounded-xl space-y-3 text-xs relative shadow-xs';
            const emojis = ['👥', '👑', '🍸', '🎙️', '🛡️', '📋', '🌟', '💼'];
            const emojiBtns = emojis.map(e => `<button type="button" onclick="document.getElementById('svc_icon_${idx}').value = '${e}'" class="w-6 h-6 rounded bg-slate-100 hover:bg-amber-100 text-xs flex items-center justify-center">${e}</button>`).join('');

            div.innerHTML = `
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Service Card #${idx + 1}</span>
                    <button type="button" onclick="this.closest('.p-4').remove()" class="text-rose-500 font-extrabold text-[10px] hover:underline">✕ Delete Card</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Icon Emoji</label>
                        <input type="text" id="svc_icon_${idx}" name="services[${idx}][icon]" value="👥" placeholder="e.g. 👥, 👑, 🍸" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-bold text-slate-900 text-center">
                        <div class="flex items-center gap-1 mt-1.5 flex-wrap justify-center">
                            ${emojiBtns}
                        </div>
                    </div>
                    <div class="sm:col-span-9">
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Service Heading / Title *</label>
                        <input type="text" name="services[${idx}][title]" placeholder="e.g. Corporate Event Ushering" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-bold text-slate-900">
                    </div>
                    <div class="sm:col-span-12">
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Service Description / Details</label>
                        <textarea name="services[${idx}][description]" rows="2" placeholder="Describe what you provide for this service..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-slate-900 text-xs"></textarea>
                    </div>
                </div>
            `;
            container.appendChild(div);
        }

        function addExperienceRow() {
            const container = document.getElementById('experience-container');
            const idx = container.querySelectorAll('.p-4').length;
            const div = document.createElement('div');
            div.className = 'p-3.5 sm:p-4 bg-white border border-slate-200/90 rounded-xl space-y-3 text-xs shadow-xs';

            let currentYear = new Date().getFullYear();
            let yearOptions = '';
            for (let y = currentYear; y >= 1990; y--) {
                yearOptions += `<option value="${y}">${y}</option>`;
            }
            let monthOptions = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'].map(m => `<option value="${m}">${m}</option>`).join('');

            div.innerHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="text" name="experience_records[${idx}][employer]" placeholder="Organization / Employer" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-bold text-slate-900">
                    <input type="text" name="experience_records[${idx}][role]" placeholder="Role (e.g. Lead Usher, VIP Coordinator)" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-bold text-slate-900">
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Start Month</label>
                        <select name="experience_records[${idx}][start_month]" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                            ${monthOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Start Year</label>
                        <select name="experience_records[${idx}][start_year]" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                            ${yearOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">End Month</label>
                        <select name="experience_records[${idx}][end_month]" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                            <option value="Present">Present</option>
                            ${monthOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">End Year</label>
                        <select name="experience_records[${idx}][end_year]" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-bold text-slate-900">
                            <option value="Present">Present</option>
                            ${yearOptions}
                        </select>
                    </div>
                </div>
                <input type="text" name="experience_records[${idx}][responsibilities]" placeholder="Duties & Key achievements..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-medium text-slate-900">
                <div class="p-3 bg-amber-50/60 border border-amber-200/80 rounded-lg space-y-2">
                    <span class="text-[10px] font-black uppercase text-amber-900 block">📞 Reference Details</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <input type="text" name="experience_records[${idx}][ref_name]" placeholder="Ref Person Name" class="bg-white border border-slate-200 rounded-md px-2.5 py-1.5 text-xs text-slate-900">
                        <input type="text" name="experience_records[${idx}][ref_contact]" placeholder="Ref Phone / Email" class="bg-white border border-slate-200 rounded-md px-2.5 py-1.5 text-xs text-slate-900">
                    </div>
                </div>
            `;
            container.appendChild(div);
        }

        function selectBookingPolicy(policyType) {
            const instantCard = document.getElementById('card-policy-instant');
            const manualCard = document.getElementById('card-policy-manual');
            const instantRadio = document.getElementById('radio-policy-instant');
            const manualRadio = document.getElementById('radio-policy-manual');

            if (policyType === 'instant') {
                if (instantRadio) instantRadio.checked = true;
                if (instantCard) instantCard.className = 'p-5 rounded-2xl border-2 cursor-pointer transition-all space-y-3 shadow-xs border-amber-400 bg-amber-50/60 ring-2 ring-amber-400/40';
                if (manualCard) manualCard.className = 'p-5 rounded-2xl border cursor-pointer transition-all space-y-3 shadow-xs border-slate-200 bg-slate-50 hover:border-slate-300';
            } else {
                if (manualRadio) manualRadio.checked = true;
                if (manualCard) manualCard.className = 'p-5 rounded-2xl border-2 cursor-pointer transition-all space-y-3 shadow-xs border-amber-400 bg-amber-50/60 ring-2 ring-amber-400/40';
                if (instantCard) instantCard.className = 'p-5 rounded-2xl border cursor-pointer transition-all space-y-3 shadow-xs border-slate-200 bg-slate-50 hover:border-slate-300';
            }
        }

        let currentViewYear = new Date().getFullYear();
        let currentViewMonth = new Date().getMonth();

        let selectedAvailabilityDates = [];
        try {
            const rawVal = document.getElementById('hidden_availability_dates_input')?.value;
            selectedAvailabilityDates = rawVal ? JSON.parse(rawVal) : [];
            if (!Array.isArray(selectedAvailabilityDates)) selectedAvailabilityDates = [];
        } catch (e) {
            selectedAvailabilityDates = [];
        }

        function renderDynamicCalendar() {
            const grid = document.getElementById('calendar-days-grid');
            const titleEl = document.getElementById('calendar-month-title');
            if (!grid) return;

            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            if (titleEl) {
                titleEl.innerText = `${monthNames[currentViewMonth]} ${currentViewYear}`;
            }

            let html = '';
            const headers = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            headers.forEach(h => {
                html += `<div class="py-1 text-slate-400 uppercase text-[10px] font-black tracking-wider">${h}</div>`;
            });

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const firstDayOfMonth = new Date(currentViewYear, currentViewMonth, 1);
            const daysInMonth = new Date(currentViewYear, currentViewMonth + 1, 0).getDate();

            let startingDay = (firstDayOfMonth.getDay() + 6) % 7;

            for (let i = 0; i < startingDay; i++) {
                html += `<div class="py-2.5 rounded-xl border border-transparent bg-slate-100/30 text-transparent opacity-0 select-none"></div>`;
            }

            for (let d = 1; d <= daysInMonth; d++) {
                const cellDate = new Date(currentViewYear, currentViewMonth, d);
                cellDate.setHours(0, 0, 0, 0);

                const mStr = String(currentViewMonth + 1).padStart(2, '0');
                const dStr = String(d).padStart(2, '0');
                const isoDate = `${currentViewYear}-${mStr}-${dStr}`;

                const isPast = cellDate < today;
                const isToday = cellDate.getTime() === today.getTime();
                const isAvailable = selectedAvailabilityDates.includes(isoDate);

                if (isPast) {
                    html += `
                        <div class="py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-400 font-bold opacity-40 cursor-not-allowed text-center select-none" title="Past dates cannot be selected for future availability">
                            <span>${d}</span>
                            <span class="block text-[8px] uppercase tracking-tighter text-slate-400">Past</span>
                        </div>
                    `;
                } else if (isToday) {
                    html += `
                        <div class="py-2.5 rounded-xl border border-amber-500 bg-amber-50 text-amber-900 font-black text-center shadow-2xs select-none" title="Today's date">
                            <span>${d}</span>
                            <span class="block text-[8px] uppercase tracking-tighter text-amber-700 font-black">Today</span>
                        </div>
                    `;
                } else {
                    if (isAvailable) {
                        html += `
                            <div onclick="toggleDateCell('${isoDate}')" data-date="${isoDate}"
                                class="calendar-date-cell py-2.5 rounded-xl border transition-all cursor-pointer font-black text-center shadow-xs select-none bg-amber-400 border-amber-500 text-slate-950 ring-2 ring-amber-300/50">
                                <span class="date-num">${d}</span>
                                <span class="date-status block text-[8px] uppercase tracking-tighter text-slate-950 font-black">✓ Available</span>
                            </div>
                        `;
                    } else {
                        html += `
                            <div onclick="toggleDateCell('${isoDate}')" data-date="${isoDate}"
                                class="calendar-date-cell py-2.5 rounded-xl border transition-all cursor-pointer font-black text-center shadow-xs select-none bg-white border-slate-200 text-slate-700 hover:border-amber-400">
                                <span class="date-num">${d}</span>
                                <span class="date-status block text-[8px] uppercase tracking-tighter text-slate-400 font-normal">Off-duty</span>
                            </div>
                        `;
                    }
                }
            }

            grid.innerHTML = html;

            const prevBtn = document.getElementById('calendar-prev-btn');
            const nowMonth = new Date().getMonth();
            const nowYear = new Date().getFullYear();
            if (prevBtn) {
                if (currentViewYear < nowYear || (currentViewYear === nowYear && currentViewMonth <= nowMonth)) {
                    prevBtn.disabled = true;
                    prevBtn.classList.add('opacity-40', 'cursor-not-allowed');
                } else {
                    prevBtn.disabled = false;
                    prevBtn.classList.remove('opacity-40', 'cursor-not-allowed');
                }
            }

            updateAvailableDaysCounter();
        }

        function toggleDateCell(isoDate) {
            const idx = selectedAvailabilityDates.indexOf(isoDate);
            if (idx > -1) {
                selectedAvailabilityDates.splice(idx, 1);
            } else {
                selectedAvailabilityDates.push(isoDate);
            }
            saveAvailabilityDatesState();
            renderDynamicCalendar();
        }

        function saveAvailabilityDatesState() {
            const input = document.getElementById('hidden_availability_dates_input');
            if (input) {
                input.value = JSON.stringify(selectedAvailabilityDates);
            }
        }

        function changeCalendarMonth(delta) {
            currentViewMonth += delta;
            if (currentViewMonth > 11) {
                currentViewMonth = 0;
                currentViewYear++;
            } else if (currentViewMonth < 0) {
                currentViewMonth = 11;
                currentViewYear--;
            }
            renderDynamicCalendar();
        }

        function toggleSelectAllDates(checkbox) {
            const daysInMonth = new Date(currentViewYear, currentViewMonth + 1, 0).getDate();
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            for (let d = 1; d <= daysInMonth; d++) {
                const cellDate = new Date(currentViewYear, currentViewMonth, d);
                cellDate.setHours(0, 0, 0, 0);
                if (cellDate > today) {
                    const mStr = String(currentViewMonth + 1).padStart(2, '0');
                    const dStr = String(d).padStart(2, '0');
                    const isoDate = `${currentViewYear}-${mStr}-${dStr}`;
                    const idx = selectedAvailabilityDates.indexOf(isoDate);

                    if (checkbox.checked && idx === -1) {
                        selectedAvailabilityDates.push(isoDate);
                    } else if (!checkbox.checked && idx > -1) {
                        selectedAvailabilityDates.splice(idx, 1);
                    }
                }
            }
            saveAvailabilityDatesState();
            renderDynamicCalendar();
        }

        function selectWeekendsOnly() {
            const daysInMonth = new Date(currentViewYear, currentViewMonth + 1, 0).getDate();
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            for (let d = 1; d <= daysInMonth; d++) {
                const cellDate = new Date(currentViewYear, currentViewMonth, d);
                cellDate.setHours(0, 0, 0, 0);
                if (cellDate > today) {
                    const dayOfWeek = cellDate.getDay();
                    const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                    const mStr = String(currentViewMonth + 1).padStart(2, '0');
                    const dStr = String(d).padStart(2, '0');
                    const isoDate = `${currentViewYear}-${mStr}-${dStr}`;
                    const idx = selectedAvailabilityDates.indexOf(isoDate);

                    if (isWeekend && idx === -1) {
                        selectedAvailabilityDates.push(isoDate);
                    } else if (!isWeekend && idx > -1) {
                        selectedAvailabilityDates.splice(idx, 1);
                    }
                }
            }
            saveAvailabilityDatesState();
            renderDynamicCalendar();
        }

        function clearAllDates() {
            const checkbox = document.getElementById('checkbox-select-all-days');
            if (checkbox) checkbox.checked = false;
            selectedAvailabilityDates = [];
            saveAvailabilityDatesState();
            renderDynamicCalendar();
        }

        function updateAvailableDaysCounter() {
            const counterSpan = document.getElementById('available-counter-text');
            if (counterSpan) {
                counterSpan.innerText = `${selectedAvailabilityDates.length} Available Days Selected`;
            }
        }

        function confirmDeleteModalGalleryPhoto(photoPath) {
            Swal.fire({
                title: 'Delete Photo?',
                text: "Are you sure you want to remove this photo from your portfolio gallery?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete Photo 🗑️'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('modal-delete-photo-path').value = photoPath;
                    document.getElementById('modal-delete-gallery-form').submit();
                }
            });
        }

        function updateSimpleSkillsCount() {
            const selectedChips = document.querySelectorAll('.simple-skill-chip.bg-slate-900, .simple-skill-chip[data-selected="true"]');
            const skills = Array.from(selectedChips).map(el => el.getAttribute('data-skill'));
            
            const hiddenInput = document.getElementById('hidden_skills_input');
            if (hiddenInput) {
                hiddenInput.value = skills.join(', ');
            }
            
            const counterEl = document.getElementById('spotify-skills-counter');
            if (counterEl) {
                counterEl.textContent = `${skills.length} Selected`;
                if (skills.length > 0) {
                    counterEl.className = 'px-3.5 py-1 rounded-full bg-amber-500/10 text-amber-900 font-black text-xs border border-amber-300';
                } else {
                    counterEl.className = 'px-3.5 py-1 rounded-full bg-slate-100 text-slate-700 font-black text-xs border border-slate-200';
                }
            }
        }

        function toggleSimpleSkillChip(btn) {
            const isSelected = btn.classList.contains('bg-slate-900');
            const iconSpan = btn.querySelector('.chip-icon');

            if (isSelected) {
                btn.classList.remove('bg-slate-900', 'text-amber-400', 'border-amber-400', 'shadow-md', 'ring-2', 'ring-amber-400/30');
                btn.classList.add('bg-slate-100/90', 'hover:bg-slate-200', 'text-slate-700', 'border-slate-200');
                btn.removeAttribute('data-selected');
                if (iconSpan) iconSpan.textContent = '⚡';
            } else {
                btn.classList.remove('bg-slate-100/90', 'hover:bg-slate-200', 'text-slate-700', 'border-slate-200');
                btn.classList.add('bg-slate-900', 'text-amber-400', 'border-amber-400', 'shadow-md', 'ring-2', 'ring-amber-400/30');
                btn.setAttribute('data-selected', 'true');
                if (iconSpan) iconSpan.textContent = '✓';
            }

            updateSimpleSkillsCount();
        }

        function addCustomSimpleSkillChip() {
            const input = document.getElementById('custom-skill-input');
            if (!input) return;
            const val = input.value.trim();
            if (!val) return;

            const container = document.getElementById('skills-grid-container');
            const existingBtn = container.querySelector(`.simple-skill-chip[data-skill="${CSS.escape(val)}"]`);
            if (existingBtn) {
                if (!existingBtn.classList.contains('bg-slate-900')) {
                    toggleSimpleSkillChip(existingBtn);
                }
                input.value = '';
                return;
            }

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'simple-skill-chip px-4 py-2.5 rounded-2xl font-black text-xs transition-all flex items-center gap-2 cursor-pointer select-none border bg-slate-900 text-amber-400 border-amber-400 shadow-md ring-2 ring-amber-400/30';
            btn.setAttribute('data-skill', val);
            btn.setAttribute('data-selected', 'true');
            btn.onclick = function() { toggleSimpleSkillChip(this); };
            btn.innerHTML = `<span class="chip-icon">✓</span> <span>${val}</span>`;

            container.appendChild(btn);
            input.value = '';
            updateSimpleSkillsCount();
        }

        // Backward compatibility aliases
        function updateSpotifySkillsCount() { updateSimpleSkillsCount(); }
        function toggleSpotifySkillTile(btn) { toggleSimpleSkillChip(btn); }
        function addCustomSpotifySkillTile() { addCustomSimpleSkillChip(); }

        function calculateAndValidateAge(dobString) {
            const container = document.getElementById('age-display-container');
            if (!container) return;

            if (!dobString) {
                container.innerHTML = '<span id="age-calc-badge" class="text-slate-400 text-[11px] italic font-medium">Select DOB to auto-calculate age (Minimum 18 years old)</span>';
                return;
            }

            const birthDate = new Date(dobString);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            if (isNaN(age)) {
                container.innerHTML = '';
                return;
            }

            if (age >= 18) {
                container.innerHTML = `<span id="age-calc-badge" class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-black text-[11px] inline-flex items-center gap-1">
                    ✓ Age: ${age} years old (Eligible 18+)
                </span>`;
            } else {
                container.innerHTML = `<span id="age-calc-badge" class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-black text-[11px] inline-flex items-center gap-1">
                    ⚠️ Age: ${age} years old (Must be at least 18 years old)
                </span>`;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Age Limit Requirement (18+)',
                        text: 'You must be at least 18 years old to operate as a crew member on AfriCrew.',
                        confirmButtonColor: '#F59E0B'
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const bioTextarea = document.getElementById('bio-textarea');
            if (bioTextarea) updateBioCounter(bioTextarea);
            if (typeof renderDynamicCalendar === 'function') renderDynamicCalendar();
            if (typeof updateAvailableDaysCounter === 'function') updateAvailableDaysCounter();
            if (typeof updateSpotifySkillsCount === 'function') updateSpotifySkillsCount();

            // Initialize Active Tab Display
            const activeTabInput = document.getElementById('active-tab-input');
            const initialTab = activeTabInput ? activeTabInput.value : 'personal';
            const stepMap = { 'personal': 1, 'bio': 2, 'experience': 3, 'photos': 4, 'availability': 5, 'charges': 6, 'reviews': 7 };
            showProfileTab(initialTab, stepMap[initialTab] || 1);

            // Draft Auto-Save System for Tab Switching Data Persistence
            const form = document.getElementById('profile-main-form');
            if (form) {
                const draftKey = 'africrew_profile_draft_' + '{{ $professional->id }}';

                // Auto-restore draft values if present
                const savedDraft = localStorage.getItem(draftKey);
                if (savedDraft) {
                    try {
                        const data = JSON.parse(savedDraft);
                        Object.keys(data).forEach(name => {
                            const field = form.querySelector(`[name="${name}"]`);
                            if (field && field.type !== 'file' && !field.value) {
                                field.value = data[name];
                            }
                        });
                    } catch (e) { }
                }

                // Listen to inputs and save draft
                form.addEventListener('input', (e) => {
                    if (e.target.name && e.target.type !== 'file') {
                        const currentDraft = JSON.parse(localStorage.getItem(draftKey) || '{}');
                        currentDraft[e.target.name] = e.target.value;
                        localStorage.setItem(draftKey, JSON.stringify(currentDraft));
                    }
                });

                // Clear draft on submit
                form.addEventListener('submit', () => {
                    localStorage.removeItem(draftKey);
                });
            }
        });
    </script>
@endsection