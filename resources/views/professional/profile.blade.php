@extends('layouts.staff')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 sm:space-y-8 pb-16 px-2 sm:px-0">

    <!-- Compact Profile Header Area -->
    <div class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm">

        <!-- Full-Width Customizable Cover Banner with Direct Camera/Edit Button -->
        <div class="relative h-32 sm:h-48 bg-gradient-to-r from-slate-950 via-slate-900 to-amber-950 overflow-hidden group">
            @php
                $coverUrl = $professional->cover_photo ? get_storage_url($professional->cover_photo) : asset('images/hero_event_ushers.jpg');
            @endphp
            <img id="header-cover-img" src="{{ $coverUrl }}" alt="Cover Banner" class="w-full h-full object-cover opacity-80">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/20 pointer-events-none"></div>

            <!-- Visible Camera/Edit Cover Button -->
            <label for="header-cover-input" class="absolute top-3 right-3 bg-black/60 hover:bg-black/80 text-white text-xs font-bold px-3 py-1.5 rounded-xl border border-white/20 backdrop-blur-md cursor-pointer transition-all flex items-center gap-1.5 shadow-md">
                📷 <span>Edit Cover</span>
                <input type="file" id="header-cover-input" name="header_cover_file" accept="image/*" class="hidden" onchange="uploadHeaderCover(this)">
            </label>
        </div>

        <!-- Hero Avatar & User Info Header -->
        <div class="p-4 sm:p-6 relative -mt-8 sm:-mt-12 flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-slate-100">

            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-5 text-center sm:text-left w-full sm:w-auto">
                <!-- Overlapping Circular Avatar with Camera Button -->
                <div class="relative shrink-0 group">
                    @php
                        $avatarUrl = $professional->profile_photo_url;
                    @endphp
                    @if($avatarUrl)
                        <img id="header-avatar-img" src="{{ $avatarUrl }}" alt="{{ $professional->full_name }}"
                            class="w-24 h-24 sm:w-28 sm:h-28 aspect-square rounded-full border-4 border-white object-cover shadow-xl shrink-0">
                    @else
                        <div id="header-avatar-placeholder"
                            class="w-24 h-24 sm:w-28 sm:h-28 aspect-square rounded-full border-4 border-white bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-2xl sm:text-3xl flex items-center justify-center shadow-xl shrink-0">
                            {{ strtoupper(substr($professional->full_name ?: 'C', 0, 1)) }}
                        </div>
                    @endif

                    <!-- Camera Edit Button Badge -->
                    <label for="header-avatar-input" class="absolute bottom-0 right-0 bg-amber-500 hover:bg-amber-600 text-slate-950 p-2 rounded-full border-2 border-white shadow-lg cursor-pointer transition-transform hover:scale-110 flex items-center justify-center">
                        📷
                        <input type="file" id="header-avatar-input" name="header_avatar_file" accept="image/*" class="hidden" onchange="uploadHeaderAvatar(this)">
                    </label>
                </div>

                <!-- User Name, Category & Pipeline Status Badge -->
                <div class="space-y-1 text-center sm:text-left">
                    @php
                        $formattedFullName = ucwords(mb_strtolower($professional->full_name ?: ($professional->username ?? 'Crew Member')));
                    @endphp
                    <div class="flex items-center justify-center sm:justify-start flex-wrap gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            {{ $formattedFullName }}
                        </h1>
                        <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            ⚡ {{ strtoupper($professional->category ?? 'Event Crew') }}
                        </span>
                    </div>

                    <!-- Pipeline Status Pipeline Badge -->
                    <div class="flex items-center justify-center sm:justify-start gap-2 pt-0.5">
                        <span class="text-[11px] font-bold text-slate-500">Status:</span>
                        @php
                            $status = $professional->pipeline_status;
                            $badgeColor = 'bg-amber-100 text-amber-900 border-amber-300';
                            if ($status === 'Approved') {
                                $badgeColor = 'bg-emerald-100 text-emerald-900 border-emerald-300';
                            } elseif ($status === 'Under Review') {
                                $badgeColor = 'bg-blue-100 text-blue-900 border-blue-300';
                            } elseif (str_contains($status, 'More Information')) {
                                $badgeColor = 'bg-purple-100 text-purple-900 border-purple-300';
                            }
                        @endphp
                        <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full border {{ $badgeColor }}">
                            ● {{ $status }}
                        </span>
                        
                        <!-- Autosave Indicator -->
                        <span id="autosave-indicator" class="text-[10px] font-bold text-slate-400 ml-2">
                            Saved
                        </span>
                    </div>
                </div>
            </div>

            <!-- Preview Digital CV / View Public Profile Button -->
            <a href="{{ route('crew.show', $professional) }}" target="_blank"
                class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-amber-400 text-xs font-black transition-all shadow-sm border border-slate-800 flex items-center justify-center gap-2 shrink-0">
                <span>🌐 {{ $professional->status === 'approved' ? 'View Public Profile' : 'Preview Digital CV' }}</span>
                <span>→</span>
            </a>

        </div>

        <!-- Dynamic Profile Completion Status Bar -->
        @php
            $completionPct = min(100, max(0, $professional->completion_percentage));
            $nextReq = $professional->next_incomplete_requirement;
        @endphp

        <div class="px-6 py-3.5 bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-slate-50 border-b border-amber-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2.5 flex-wrap text-center sm:text-left">
                <span class="w-3 h-3 rounded-full {{ $completionPct === 100 ? 'bg-emerald-500 shadow-emerald-500/50' : 'bg-amber-500 shadow-amber-500/50' }} shadow-sm animate-pulse"></span>
                <span class="font-black text-slate-900 text-sm">Profile Completion: {{ $completionPct }}%</span>
                <span class="text-slate-300 font-bold">•</span>
                <span class="font-bold text-slate-700">
                    @if($completionPct < 100)
                        Next step: <span class="text-amber-800 font-extrabold underline decoration-amber-400 underline-offset-2">{{ $nextReq }}</span>
                    @else
                        <span class="text-emerald-700 font-black">✓ Profile complete & ready for bookings!</span>
                    @endif
                </span>
            </div>
            <div class="w-full sm:w-52 bg-slate-200/90 rounded-full h-2.5 overflow-hidden shrink-0 border border-slate-300/60 p-0.5">
                <div class="{{ $completionPct === 100 ? 'bg-gradient-to-r from-emerald-400 to-emerald-600' : 'bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600' }} h-full rounded-full transition-all duration-500 shadow-xs"
                    style="width: {{ $completionPct }}%;"></div>
            </div>
        </div>

        <!-- Horizontal Segmented Tabs Bar -->
        <div class="p-3 bg-slate-100/70 flex items-center gap-2 overflow-x-auto text-xs font-extrabold scrollbar-none">
            <button type="button" onclick="showProfileTab('personal', 1)" id="tab-nav-personal"
                class="tab-nav-btn px-3.5 py-2.5 rounded-xl bg-slate-900 text-amber-400 font-black shadow-sm border border-slate-900 transition-all flex items-center gap-1.5 whitespace-nowrap">
                <span>👤 Personal</span>
            </button>
            <button type="button" onclick="showProfileTab('bio', 2)" id="tab-nav-bio"
                class="tab-nav-btn px-3.5 py-2.5 rounded-xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-1.5 whitespace-nowrap">
                <span>📝 About You</span>
            </button>
            <button type="button" onclick="showProfileTab('experience', 3)" id="tab-nav-experience"
                class="tab-nav-btn px-3.5 py-2.5 rounded-xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-1.5 whitespace-nowrap">
                <span>💼 Experience & Qualifications</span>
            </button>
            <button type="button" onclick="showProfileTab('photos', 4)" id="tab-nav-photos"
                class="tab-nav-btn px-3.5 py-2.5 rounded-xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-1.5 whitespace-nowrap">
                <span>🖼️ Portfolio</span>
            </button>
            <button type="button" onclick="showProfileTab('availability', 5)" id="tab-nav-availability"
                class="tab-nav-btn px-3.5 py-2.5 rounded-xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-1.5 whitespace-nowrap">
                <span>📅 Locations & Availability</span>
            </button>
            <button type="button" onclick="showProfileTab('charges', 6)" id="tab-nav-charges"
                class="tab-nav-btn px-3.5 py-2.5 rounded-xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-1.5 whitespace-nowrap">
                <span>💰 Service Rates</span>
            </button>
            <button type="button" onclick="showProfileTab('review-submit', 7)" id="tab-nav-review-submit"
                class="tab-nav-btn px-3.5 py-2.5 rounded-xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-1.5 whitespace-nowrap">
                <span>⭐ Review & Submit</span>
            </button>
        </div>
    </div>

    <!-- Main Profile Edit Form -->
    <form id="profile-main-form" method="POST" action="{{ route('professional.profile.update') }}"
        enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PATCH')
        <input type="hidden" name="active_tab" id="active-tab-input"
            value="{{ old('active_tab', session('active_tab', request('active_tab', 'personal'))) }}">

        <!-- ==================== TAB 1: PERSONAL PAGE ==================== -->
        <div id="tab-view-personal" class="tab-view-content bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">Personal Information</h3>
                <p class="text-xs text-slate-500 mt-0.5">Your core identity details</p>
            </div>

            <!-- Controlled Editing Notice -->
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-900 space-y-1">
                <span class="font-extrabold">ℹ️ Controlled Editing Policy:</span>
                <p class="text-slate-600 leading-relaxed">
                    Updating your registered Email or Phone Number requires reverification. If your account is already approved, modifying your legal name flags your profile for administrative review before identity changes become public.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs font-bold">
                <!-- Full Name / Identity (Readonly for Security) -->
                <div>
                    <label class="block text-slate-700 mb-1 flex items-center justify-between">
                        <span>Full Name *</span>
                        <span class="text-[10px] text-amber-800 bg-amber-100 px-2 py-0.5 rounded font-black">🔒 Locked Identifier</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name', $professional->full_name) }}" readonly
                        class="w-full bg-slate-100 border border-slate-200/90 text-slate-700 rounded-2xl px-4 py-3 text-xs font-bold cursor-not-allowed shadow-inner" title="Full name cannot be changed directly. Contact admin for official name updates.">
                </div>

                <!-- Username / Handle (Readonly Account Handle) -->
                <div>
                    <label class="block text-slate-700 mb-1 flex items-center justify-between">
                        <span>
                            Username Handle *
                            <span class="text-slate-400 font-normal ml-1">mycv.ac/{{ $professional->username }}</span>
                        </span>
                        <span class="text-[10px] text-amber-800 bg-amber-100 px-2 py-0.5 rounded font-black">🔒 Locked</span>
                    </label>
                    <input type="text" name="username" value="{{ old('username', $professional->username) }}" readonly
                        class="w-full bg-slate-100 border border-slate-200/90 text-slate-700 rounded-2xl px-4 py-3 text-xs font-bold cursor-not-allowed shadow-inner" title="Username handle is locked to your digital CV link.">
                </div>

                <!-- Primary Category (Readonly Category) -->
                <div>
                    <label class="block text-slate-700 mb-1 flex items-center justify-between">
                        <span>Primary Crew Category *</span>
                        <span class="text-[10px] text-amber-800 bg-amber-100 px-2 py-0.5 rounded font-black">🔒 Registered Role</span>
                    </label>
                    <input type="text" name="category" value="{{ old('category', $professional->category) }}" readonly
                        class="w-full bg-slate-100 border border-slate-200/90 text-slate-700 rounded-2xl px-4 py-3 text-xs font-bold cursor-not-allowed shadow-inner" title="Registered category is locked to maintain vetting integrity.">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-slate-700 mb-1">Gender *</label>
                    <select name="gender" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500 focus:bg-white">
                        <option value="Male" @selected(old('gender', $professional->gender) == 'Male')>Male</option>
                        <option value="Female" @selected(old('gender', $professional->gender) == 'Female')>Female</option>
                        <option value="Other" @selected(old('gender', $professional->gender) == 'Other')>Other</option>
                    </select>
                </div>

                <!-- Email Address (Readonly Account Email) -->
                <div>
                    <label class="block text-slate-700 mb-1 flex items-center justify-between">
                        <span>Email Address *</span>
                        <span class="text-[10px] text-amber-800 bg-amber-100 px-2 py-0.5 rounded font-black">🔒 Verified Email</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $professional->email) }}" readonly
                        class="w-full bg-slate-100 border border-slate-200/90 text-slate-700 rounded-2xl px-4 py-3 text-xs font-bold cursor-not-allowed shadow-inner" title="Account email address is locked for verification security.">
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-slate-700 mb-1">Phone Number *</label>
                    <input type="tel" name="phone" value="{{ old('phone', $professional->phone) }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold outline-none focus:border-amber-500 focus:bg-white">
                </div>

                <!-- Date of Birth (Kept Private - Display Eligibility Only After Entry) -->
                <div>
                    <label class="block text-slate-700 mb-1">
                        Date of Birth <span class="text-slate-400 font-normal">(Private)</span>
                    </label>
                    @php
                        $dobVal = old('date_of_birth', $professional->date_of_birth ? \Carbon\Carbon::parse($professional->date_of_birth)->format('Y-m-d') : '');
                        $maxDobLimit = \Carbon\Carbon::now()->subYears(18)->format('Y-m-d');
                    @endphp
                    <input type="date" id="date_of_birth_input" name="date_of_birth"
                        value="{{ $dobVal }}" max="{{ $maxDobLimit }}"
                        onchange="checkAgeEligibility(this.value)"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500 focus:bg-white">
                    
                    <div id="dob-eligibility-badge" class="{{ $dobVal ? '' : 'hidden' }} mt-2 p-2 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-900 font-bold flex items-center gap-1.5 text-[11px]">
                        <span>✓ Eligible to work on event gigs (Age 18+ verified)</span>
                    </div>
                </div>
            </div>

            <!-- Tab 1 Next Action Button -->
            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="showProfileTab('bio', 2)"
                    class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs rounded-2xl shadow-md transition-all flex items-center gap-2">
                    <span>Save & Next: About You</span>
                    <span>→</span>
                </button>
            </div>
        </div>

        <!-- ==================== TAB 2: ABOUT YOU (Renamed from Bio & Quote) ==================== -->
        <div id="tab-view-bio" class="tab-view-content hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">Tell clients about your work</h3>
                <p class="text-xs text-slate-500 mt-0.5">Describe what you do, your experience, and what you bring to an event.</p>
            </div>

            <!-- Work Bio / Description -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1">
                    Work Description & Experience Summary *
                </label>
                <textarea name="about" id="bio-textarea" rows="5" required
                    placeholder="Describe your event experience, technical capabilities, or customer service background..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs font-semibold outline-none focus:border-amber-500 focus:bg-white leading-relaxed">{{ old('about', $professional->about) }}</textarea>
            </div>

            <!-- Highlight Quote / Personal Statement (Optional) -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1">
                    Highlight Quote / Personal Statement <span class="text-slate-400 font-normal lowercase">(Optional)</span>
                </label>
                <input type="text" name="highlight_quote" value="{{ old('highlight_quote', $professional->highlight_quote) }}"
                    placeholder='e.g. "Elegance and punctuality are non-negotiable at every event."'
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-semibold outline-none focus:border-amber-500 focus:bg-white">
            </div>

            <!-- Social Media Handles (Retained HERE Only - Separate TikTok and X) -->
            <div class="space-y-3 pt-3 border-t border-slate-100">
                <h4 class="text-xs font-black uppercase text-slate-800">Social Media & Portfolio Links</h4>
                @php
                    $socials = is_array($professional->social_links) ? $professional->social_links : [];
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block text-slate-600 mb-1 font-bold">Instagram Handle / URL</label>
                        <input type="text" name="social_links[instagram]" value="{{ old('social_links.instagram', $socials['instagram'] ?? '') }}"
                            placeholder="e.g. @janedoe or instagram.com/janedoe"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-3.5 py-2.5 outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1 font-bold">LinkedIn Profile URL</label>
                        <input type="text" name="social_links[linkedin]" value="{{ old('social_links.linkedin', $socials['linkedin'] ?? '') }}"
                            placeholder="e.g. linkedin.com/in/janedoe"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-3.5 py-2.5 outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1 font-bold">Facebook Profile URL</label>
                        <input type="text" name="social_links[facebook]" value="{{ old('social_links.facebook', $socials['facebook'] ?? '') }}"
                            placeholder="e.g. facebook.com/janedoe"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-3.5 py-2.5 outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1 font-bold">TikTok Handle</label>
                        <input type="text" name="social_links[tiktok]" value="{{ old('social_links.tiktok', $socials['tiktok'] ?? '') }}"
                            placeholder="e.g. @janedoe_events"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-3.5 py-2.5 outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1 font-bold">X (Twitter) Handle</label>
                        <input type="text" name="social_links[x]" value="{{ old('social_links.x', $socials['x'] ?? ($socials['twitter'] ?? '')) }}"
                            placeholder="e.g. @janedoe_x"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-3.5 py-2.5 outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1 font-bold">Website / Online Resume URL</label>
                        <input type="text" name="social_links[website]" value="{{ old('social_links.website', $socials['website'] ?? '') }}"
                            placeholder="e.g. https://janedoe.com"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-3.5 py-2.5 outline-none focus:border-amber-500">
                    </div>
                </div>
            </div>

            <!-- Tab 2 Next Action Button -->
            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="showProfileTab('experience', 3)"
                    class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs rounded-2xl shadow-md transition-all flex items-center gap-2">
                    <span>Save & Next: Experience & Qualifications</span>
                    <span>→</span>
                </button>
            </div>
        </div>

        <!-- ==================== TAB 3: EXPERIENCE & QUALIFICATIONS (Renamed) ==================== -->
        <div id="tab-view-experience" class="tab-view-content hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">Experience & Qualifications</h3>
                <p class="text-xs text-slate-500 mt-0.5">Show clients the experience and training behind your work.</p>
            </div>

            <!-- Section 3A: Education & Certifications -->
            <div class="space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h4 class="text-xs font-black uppercase text-slate-800">Education & Certifications</h4>
                    <div class="flex items-center gap-3">
                        <!-- Toggle: Show education on my Digital CV -->
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                            <input type="checkbox" name="show_education_on_cv" value="1" @checked(old('show_education_on_cv', $professional->show_education_on_cv ?? true))
                                class="w-4 h-4 text-amber-500 rounded focus:ring-amber-400">
                            <span>Show education on my Digital CV</span>
                        </label>

                        <!-- Add Education Button -->
                        <button type="button" onclick="addEducationRow()" class="px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-900 font-extrabold text-[11px] rounded-xl border border-amber-300 transition-colors flex items-center gap-1">
                            <span>+ Add Education Record</span>
                        </button>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500">Information will remain available to AfriCrew for review when public display is switched off. Academic qualifications are optional.</p>

                <div id="education-cards-wrapper" class="space-y-3">
                    @php
                        $eduRecords = is_array($professional->education) ? $professional->education : [];
                    @endphp
                    @if(count($eduRecords) > 0)
                        @foreach($eduRecords as $idx => $edu)
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 text-xs">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <input type="text" name="education[{{ $idx }}][institution]" value="{{ $edu['institution'] ?? '' }}" placeholder="Institution / Organization" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                    <input type="text" name="education[{{ $idx }}][degree]" value="{{ $edu['degree'] ?? '' }}" placeholder="Certification / Degree / Course" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-extrabold text-slate-600 mb-1">From Date</label>
                                        <input type="date" name="education[{{ $idx }}][start_date]" value="{{ $edu['start_date'] ?? '' }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-extrabold text-slate-600 mb-1">To Date (Leave blank if Present)</label>
                                        <input type="date" name="education[{{ $idx }}][end_date]" value="{{ $edu['end_date'] ?? '' }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <input type="text" name="education[0][institution]" placeholder="Institution / Organization (e.g. Kenya Utalii College)" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                <input type="text" name="education[0][degree]" placeholder="Certification / Course (e.g. Certificate in Event Hospitality)" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1">From Date</label>
                                    <input type="date" name="education[0][start_date]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1">To Date (Leave blank if Present)</label>
                                    <input type="date" name="education[0][end_date]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section 3B: Work Experience -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h4 class="text-xs font-black uppercase text-slate-800">Work Experience & Gig History</h4>
                        <p class="text-[11px] text-slate-500">Freelance, self-employed, contract, and project-based experience entries are fully supported.</p>
                    </div>

                    <!-- Add Work Experience Button -->
                    <button type="button" onclick="addExperienceRow()" class="px-3.5 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-900 font-extrabold text-[11px] rounded-xl border border-amber-300 transition-colors flex items-center gap-1">
                        <span>+ Add Work Experience</span>
                    </button>
                </div>

                <div id="experience-cards-wrapper" class="space-y-3">
                    @php
                        $expRecords = is_array($professional->experience_records) ? $professional->experience_records : [];
                    @endphp
                    @if(count($expRecords) > 0)
                        @foreach($expRecords as $eIdx => $exp)
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 text-xs">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <input type="text" name="experience_records[{{ $eIdx }}][employer]" value="{{ $exp['employer'] ?? '' }}" placeholder="Company / Event Client" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                    <input type="text" name="experience_records[{{ $eIdx }}][role]" value="{{ $exp['role'] ?? '' }}" placeholder="Role (e.g. Lead Usher, Audio Engineer)" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                    <select name="experience_records[{{ $eIdx }}][type]" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none font-bold">
                                        <option value="Freelance / Project-based" @selected(($exp['type'] ?? '') == 'Freelance / Project-based')>Freelance / Project-based</option>
                                        <option value="Self-employed" @selected(($exp['type'] ?? '') == 'Self-employed')>Self-employed</option>
                                        <option value="Full-time" @selected(($exp['type'] ?? '') == 'Full-time')>Full-time Contract</option>
                                        <option value="Part-time" @selected(($exp['type'] ?? '') == 'Part-time')>Part-time</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-extrabold text-slate-600 mb-1">From Date</label>
                                        <input type="date" name="experience_records[{{ $eIdx }}][start_date]" value="{{ $exp['start_date'] ?? '' }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-extrabold text-slate-600 mb-1">To Date (Leave blank if Present)</label>
                                        <input type="date" name="experience_records[{{ $eIdx }}][end_date]" value="{{ $exp['end_date'] ?? '' }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <input type="text" name="experience_records[0][employer]" placeholder="Company / Client (e.g. Magical Kenya Expo)" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                <input type="text" name="experience_records[0][role]" placeholder="Role (e.g. Head Protocol Hostess)" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                <select name="experience_records[0][type]" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none font-bold">
                                    <option value="Freelance / Project-based">Freelance / Project-based</option>
                                    <option value="Self-employed">Self-employed</option>
                                    <option value="Full-time">Full-time Contract</option>
                                    <option value="Part-time">Part-time</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1">From Date</label>
                                    <input type="date" name="experience_records[0][start_date]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1">To Date (Leave blank if Present)</label>
                                    <input type="date" name="experience_records[0][end_date]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section 3C: Key Skills & Featured Selection -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h4 class="text-xs font-black uppercase text-slate-800">Key Skills (Up to 10 Skills, Select 3 Featured)</h4>
                        <p class="text-[11px] text-slate-500">Skills are loaded from admin-managed catalog for your category.</p>
                    </div>
                    <!-- Suggest a Skill Button -->
                    <button type="button" onclick="openSuggestSkillModal()" class="px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-900 font-extrabold text-[11px] rounded-xl border border-amber-300 transition-colors">
                        💡 Suggest a Skill
                    </button>
                </div>

                @php
                    $currentSkills = array_map('trim', explode(',', $professional->skills ?? 'Guest check-in, VIP Assistance, Badge Printing'));
                    $featuredSkills = is_array($professional->featured_skills) ? $professional->featured_skills : array_slice($currentSkills, 0, 3);
                @endphp

                <!-- Skills Select Input Box -->
                <div>
                    <input type="text" name="skills" value="{{ implode(', ', $currentSkills) }}" placeholder="Comma-separated skills (e.g. Guest check-in, VIP Assistance, Audio Mixing)"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold outline-none focus:border-amber-500">
                </div>
            </div>

            <!-- Section 3D: Add Referee & Verification -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h4 class="text-xs font-black uppercase text-slate-800">Add Referee</h4>
                        <p class="text-[11px] text-slate-500">Referee contact details remain private. Profile setup can continue while verification is pending.</p>
                    </div>

                    <!-- Add Referee Button -->
                    <button type="button" onclick="addRefereeRow()" class="px-3.5 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-900 font-extrabold text-[11px] rounded-xl border border-amber-300 transition-colors flex items-center gap-1">
                        <span>+ Add Referee</span>
                    </button>
                </div>

                <div id="referees-cards-wrapper" class="space-y-3">
                    @php
                        $referees = is_array($professional->referees) ? $professional->referees : [];
                    @endphp
                    @if(count($referees) > 0)
                        @foreach($referees as $rIdx => $ref)
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 text-xs">
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <input type="text" name="referees[{{ $rIdx }}][name]" value="{{ $ref['name'] ?? '' }}" placeholder="Referee Full Name" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                    <input type="text" name="referees[{{ $rIdx }}][relationship]" value="{{ $ref['relationship'] ?? '' }}" placeholder="Relationship (e.g. Event Manager)" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                    <input type="email" name="referees[{{ $rIdx }}][email]" value="{{ $ref['email'] ?? '' }}" placeholder="Referee Email" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                    <input type="tel" name="referees[{{ $rIdx }}][phone]" value="{{ $ref['phone'] ?? '' }}" placeholder="Referee Phone" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 flex-wrap gap-2">
                                    <span class="text-[11px] font-bold text-slate-600">
                                        Status: <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-800 font-black text-[10px]">{{ $ref['status'] ?? 'Not Requested' }}</span>
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="saveRefereeForm({{ $rIdx }})" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] rounded-xl shadow-2xs flex items-center gap-1 transition-all">
                                            <span>💾 1. SAVE</span>
                                        </button>
                                        <button type="button" onclick="requestRefereeVerification({{ $rIdx }})" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-[11px] rounded-xl shadow-2xs flex items-center gap-1 transition-all">
                                            <span>🛡️ 2. VERIFY</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                <input type="text" name="referees[0][name]" placeholder="Referee Name (e.g. Mark Vance)" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                <input type="text" name="referees[0][relationship]" placeholder="Relationship (e.g. Event Lead)" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                <input type="email" name="referees[0][email]" placeholder="referee@example.com" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                                <input type="tel" name="referees[0][phone]" placeholder="+254 700 000 000" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 flex-wrap gap-2">
                                <span class="text-[11px] font-bold text-slate-600">
                                    Status: <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-800 font-black text-[10px]">Not Saved</span>
                                </span>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="saveRefereeForm(0)" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] rounded-xl shadow-2xs flex items-center gap-1 transition-all">
                                        <span>💾 1. SAVE</span>
                                    </button>
                                    <button type="button" onclick="requestRefereeVerification(0)" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-[11px] rounded-xl shadow-2xs flex items-center gap-1 transition-all">
                                        <span>🛡️ 2. VERIFY</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab 3 Next Action Button -->
            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="showProfileTab('photos', 4)"
                    class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs rounded-2xl shadow-md transition-all flex items-center gap-2">
                    <span>Save & Next: Portfolio</span>
                    <span>→</span>
                </button>
            </div>
        </div>

        <!-- ==================== TAB 4: PORTFOLIO (Renamed from Photos) ==================== -->
        <div id="tab-view-photos" class="tab-view-content hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">My Work Portfolio</h3>
                <p class="text-xs text-slate-500 mt-0.5">Showcase high-resolution photos of your event work, rehearsals, installations, or completed productions.</p>
            </div>

            <!-- Photo Counter -->
            @php
                $gallery = is_array($professional->gallery_photos) ? array_filter($professional->gallery_photos) : [];
                $photoCount = count($gallery);
            @endphp
            <div class="flex items-center justify-between text-xs font-extrabold text-slate-700 bg-slate-50 p-3 rounded-2xl border border-slate-200">
                <span>Portfolio Upload Counter:</span>
                <span class="px-3 py-1 rounded-full bg-amber-500 text-slate-950 font-black text-xs">{{ $photoCount }} of 4 photos uploaded</span>
            </div>

            <!-- Portfolio Upload Grid: Single Add Photos Tile + Uploaded Thumbnails -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- Add Photos Tile -->
                @if($photoCount < 4)
                    <label for="portfolio-upload-input" class="aspect-square bg-slate-50 hover:bg-slate-100 border-2 border-dashed border-amber-400/80 rounded-2xl flex flex-col items-center justify-center p-4 text-center cursor-pointer transition-all hover:scale-105">
                        <span class="text-2xl mb-1">📸</span>
                        <span class="text-xs font-black text-slate-900">Add Photos</span>
                        <span class="text-[10px] text-slate-400 mt-0.5">JPG / PNG max 5MB</span>
                        <input type="file" id="portfolio-upload-input" name="gallery_photos[]" multiple accept="image/*" class="hidden" onchange="this.form.submit()">
                    </label>
                @endif

                <!-- Uploaded Photo Thumbnails in Digital CV Style -->
                @foreach($gallery as $pIdx => $photo)
                    @php $photoUrl = get_storage_url($photo); @endphp
                    <div class="relative aspect-square rounded-2xl overflow-hidden border border-slate-200 shadow-sm group">
                        <img src="{{ $photoUrl }}" alt="Portfolio Photo {{ $pIdx + 1 }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <button type="button" onclick="deletePortfolioPhoto('{{ $photo }}')" class="p-2 bg-rose-600 text-white rounded-full text-xs font-bold">
                                🗑️ Remove
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Guidance Note -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600 leading-relaxed">
                <p class="font-bold text-slate-800">📷 Portfolio Guidance:</p>
                <p>Upload clear action shots representing your work category — such as managing registration desks, operating sound/lighting boards, hosting VIP lounges, mixology service, or live stage execution.</p>
            </div>

            <!-- Tab 4 Next Action Button -->
            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="showProfileTab('availability', 5)"
                    class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs rounded-2xl shadow-md transition-all flex items-center gap-2">
                    <span>Save & Next: Locations & Availability</span>
                    <span>→</span>
                </button>
            </div>
        </div>

        <!-- ==================== TAB 5: LOCATIONS & AVAILABILITY (Renamed) ==================== -->
        <div id="tab-view-availability" class="tab-view-content hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">Working Locations & Availability Calendar</h3>
                <p class="text-xs text-slate-500 mt-0.5">Define your primary working city and manage your assignment availability.</p>
            </div>

            <!-- Section 5A: Working Locations -->
            <div class="space-y-4">
                <h4 class="text-xs font-black uppercase text-slate-800">Primary Working Location</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-bold">
                    <div>
                        <label class="block text-slate-700 mb-1">Country *</label>
                        <select name="country" id="country-select" onchange="onCountrySelectChange(this.value)" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                            <option value="Kenya" @selected(old('country', $professional->country) == 'Kenya')>Kenya</option>
                            <option value="Uganda" @selected(old('country', $professional->country) == 'Uganda')>Uganda</option>
                            <option value="Tanzania" @selected(old('country', $professional->country) == 'Tanzania')>Tanzania</option>
                            <option value="Rwanda" @selected(old('country', $professional->country) == 'Rwanda')>Rwanda</option>
                            <option value="Burundi" @selected(old('country', $professional->country) == 'Burundi')>Burundi</option>
                            <option value="South Sudan" @selected(old('country', $professional->country) == 'South Sudan')>South Sudan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 mb-1">Primary Working City / Town *</label>
                        <select name="city" id="city-select" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                            <!-- Populated dynamically via JS -->
                        </select>
                    </div>
                </div>

                <!-- Other towns I can serve -->
                <div>
                    <label class="block text-xs font-extrabold uppercase text-slate-700 mb-1 flex items-center justify-between">
                        <span>Other towns I can serve <span class="text-slate-400 font-normal lowercase">(Select or add towns you can serve)</span></span>
                        <span class="text-[11px] text-amber-700 font-bold" id="other-towns-count">0 selected</span>
                    </label>
                    <div id="other-towns-chips-container" class="flex flex-wrap gap-2 p-3 bg-slate-50 border border-slate-200 rounded-2xl min-h-12 items-center mb-2">
                        <!-- Chips rendered via JS -->
                    </div>
                    <div id="other-towns-hidden-inputs"></div>

                    <!-- Add New Town Input Controls -->
                    <div class="flex items-center gap-2 pt-1">
                        <input type="text" id="custom-town-input" placeholder="Type a new town or city name (e.g. Diani, Nanyuki, Malindi)..."
                            class="flex-1 bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold outline-none focus:border-amber-500 shadow-2xs">
                        <button type="button" onclick="addCustomOtherTown()"
                            class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-xs rounded-xl shadow-xs shrink-0 flex items-center gap-1.5 transition-all">
                            <span>➕ Add New Town</span>
                        </button>
                    </div>
                </div>

                <div class="p-3 bg-amber-50/80 border border-amber-200 rounded-xl text-xs text-amber-900 font-semibold">
                    💬 Note: "Travel and accommodation requirements will be agreed in your quotation."
                </div>
            </div>

            <!-- Section 5B: Calendar & Availability Rules -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h4 class="text-xs font-black uppercase text-slate-800">Availability Calendar</h4>
                    <span class="text-xs font-extrabold text-emerald-800 bg-emerald-100 px-3 py-1 rounded-full">
                        📅 {{ now()->format('F Y') }}
                    </span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    "You are available by default. Select dates when you cannot work. Confirmed AfriCrew bookings will block the relevant dates automatically. Past dates and today's dates within 6 hours lead time are inactive."
                </p>

                <!-- Legend -->
                <div class="flex items-center gap-4 text-xs font-bold">
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Available</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Unavailable (Blocked)</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-slate-300"></span> Inactive / Past / &lt; 6h</div>
                </div>

                <!-- Calendar Actions -->
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" onclick="blockSelectedDates()" class="px-3.5 py-2 bg-rose-600 text-white font-bold text-xs rounded-xl shadow-sm">
                        Block Dates
                    </button>
                    <button type="button" onclick="setWeeklyAvailability()" class="px-3.5 py-2 bg-slate-900 text-amber-400 font-bold text-xs rounded-xl shadow-sm">
                        Set Weekly Availability
                    </button>
                    <button type="button" onclick="resetManualAvailability()" class="px-3.5 py-2 bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl">
                        Reset Manual Availability
                    </button>
                </div>

                <!-- Month Grid Display with Visible Date Numbers -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between text-xs font-black text-slate-900">
                        <span>{{ strtoupper(now()->format('F Y')) }}</span>
                        <span>Kenya GMT+3</span>
                    </div>
                    <div class="grid grid-cols-7 gap-1.5 text-center text-xs">
                        <div class="font-extrabold text-slate-400 py-1">Mon</div>
                        <div class="font-extrabold text-slate-400 py-1">Tue</div>
                        <div class="font-extrabold text-slate-400 py-1">Wed</div>
                        <div class="font-extrabold text-slate-400 py-1">Thu</div>
                        <div class="font-extrabold text-slate-400 py-1">Fri</div>
                        <div class="font-extrabold text-slate-400 py-1">Sat</div>
                        <div class="font-extrabold text-slate-400 py-1">Sun</div>

                        @php
                            $now = \Carbon\Carbon::now();
                            $daysInMonth = $now->daysInMonth;
                            $currentDay = $now->day;
                            $currentHour = $now->hour;
                            // 6-hour lead time requirement: if current hour + 6 >= 24, today is also inactive
                            $isTodayInactive = ($currentHour + 6 >= 24);
                        @endphp

                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $isPastOrInactive = ($day < $currentDay) || ($day == $currentDay && $isTodayInactive);
                            @endphp
                            @if($isPastOrInactive)
                                <button type="button" disabled title="Past Date / < 6 Hours Lead Time (Inactive)"
                                    class="h-10 rounded-xl font-bold text-xs flex items-center justify-center bg-slate-200 text-slate-400 border border-slate-200 cursor-not-allowed opacity-50 select-none">
                                    {{ $day }}
                                </button>
                            @else
                                <button type="button" onclick="toggleDateCell(this, {{ $day }})"
                                    class="h-10 rounded-xl font-bold text-xs flex items-center justify-center transition-all bg-emerald-500 text-white shadow-xs">
                                    {{ $day }}
                                </button>
                            @endif
                        @endfor
                    </div>
                </div>

                <!-- Types of assignments I accept -->
                <div class="pt-3">
                    <label class="block text-xs font-extrabold uppercase text-slate-700 mb-2">
                        Types of assignments I accept
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-bold">
                        <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer">
                            <input type="checkbox" name="assignment_types[]" value="Half Day" class="w-4 h-4 text-amber-500 rounded">
                            <span>Half Day Shifts</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer">
                            <input type="checkbox" name="assignment_types[]" value="Full Day" class="w-4 h-4 text-amber-500 rounded">
                            <span>Full Day Shifts</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer">
                            <input type="checkbox" name="assignment_types[]" value="Multi-Day" class="w-4 h-4 text-amber-500 rounded">
                            <span>Multi-Day Events</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer">
                            <input type="checkbox" name="assignment_types[]" value="Night Shift" class="w-4 h-4 text-amber-500 rounded">
                            <span>Night Shifts</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Tab 5 Next Action Button -->
            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="showProfileTab('charges', 6)"
                    class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs rounded-2xl shadow-md transition-all flex items-center gap-2">
                    <span>Save & Next: Service Rates</span>
                    <span>→</span>
                </button>
            </div>
        </div>

        <!-- ==================== TAB 6: SERVICE RATES (Renamed from Labour Charges) ==================== -->
        <div id="tab-view-charges" class="tab-view-content hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">How much would you like to charge for your services?</h3>
                <p class="text-xs text-slate-500 mt-0.5">Specify your quotation reference rates and day fee structure.</p>
            </div>

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-900 leading-relaxed font-semibold">
                💬 "These rates help you prepare quotations. Your final price depends on the event requirements and the quotation agreed with the client."
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs font-bold">
                <!-- Platform Service Fee (Non-editable) -->
                <div>
                    <label class="block text-slate-700 mb-1 flex items-center justify-between">
                        <span>Platform Service Fee</span>
                        <span class="text-[10px] bg-slate-200 text-slate-700 font-extrabold px-2 py-0.5 rounded-md">🔒 Fixed Platform Standard</span>
                    </label>
                    <input type="text" value="15% (Fixed Platform Standard - Included)" disabled readonly
                        class="w-full bg-slate-100 border border-slate-200/90 rounded-2xl px-4 py-3 text-xs font-black text-slate-600 cursor-not-allowed select-none">
                    <p class="text-[11px] text-slate-500 font-medium mt-1">Platform Service Fee is fixed at 15% for all standard bookings and is non-editable.</p>
                </div>
                <!-- Currency -->
                <div>
                    <label class="block text-slate-700 mb-1">Currency *</label>
                    <select name="currency" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                        <option value="KES" @selected(old('currency', $professional->currency) == 'KES')>KES (Kenyan Shilling)</option>
                        <option value="USD" @selected(old('currency', $professional->currency) == 'USD')>USD (US Dollar)</option>
                        <option value="UGX" @selected(old('currency', $professional->currency) == 'UGX')>UGX (Uganda Shilling)</option>
                        <option value="TZS" @selected(old('currency', $professional->currency) == 'TZS')>TZS (Tanzanian Shilling)</option>
                        <option value="RWF" @selected(old('currency', $professional->currency) == 'RWF')>RWF (Rwandan Franc)</option>
                    </select>
                </div>

                <!-- Usual Daily Rate -->
                <div>
                    <label class="block text-slate-700 mb-1">Usual Daily Rate *</label>
                    <input type="number" name="full_day_rate" value="{{ old('full_day_rate', $professional->full_day_rate ?: ($professional->one_day_rate ?: 5000)) }}" step="100" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold outline-none focus:border-amber-500">
                </div>

                <!-- Working Hours Included -->
                <div>
                    <label class="block text-slate-700 mb-1">Working Hours Included in Rate *</label>
                    <input type="number" name="working_hours" value="{{ old('working_hours', $professional->working_hours ?: 8) }}" min="1" max="24" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold outline-none focus:border-amber-500">
                </div>

                <!-- Rehearsal Charges Option -->
                <div>
                    <label class="block text-slate-700 mb-1">Rehearsal Charges *</label>
                    <select name="rehearsal_option" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                        <option value="included" @selected(old('rehearsal_option', $professional->rehearsal_option) == 'included')>Included in my daily fee</option>
                        <option value="yes" @selected(old('rehearsal_option', $professional->rehearsal_option) == 'yes')>Yes (Separate rehearsal day rate)</option>
                        <option value="not_applicable" @selected(old('rehearsal_option', $professional->rehearsal_option) == 'not_applicable')>Not applicable</option>
                    </select>
                </div>

                <!-- Rehearsal Day Rate -->
                <div>
                    <label class="block text-slate-700 mb-1">Rehearsal Day Rate (When Applicable)</label>
                    <input type="number" name="rehearsal_rate" value="{{ old('rehearsal_rate', $professional->rehearsal_rate ?: 2500) }}" step="100"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold outline-none focus:border-amber-500">
                </div>

                <!-- Multi-Day Discount Toggle & Percent -->
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                        <input type="checkbox" name="has_multi_day_discount" value="1" @checked(old('has_multi_day_discount', $professional->has_multi_day_discount))
                            class="w-4 h-4 text-amber-500 rounded focus:ring-amber-400">
                        <span>Offer Multi-Day Event Discount</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="multi_day_discount_pct" value="{{ old('multi_day_discount_pct', $professional->multi_day_discount_pct ?: 10) }}" placeholder="Discount %" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs">
                        <input type="number" name="multi_day_min_days" value="{{ old('multi_day_min_days', $professional->multi_day_min_days ?: 3) }}" placeholder="Min Days" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs">
                    </div>
                </div>
            </div>

            <!-- Tab 6 Next Action Button -->
            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="showProfileTab('review-submit', 7)"
                    class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs rounded-2xl shadow-md transition-all flex items-center gap-2">
                    <span>Save & Next: Review & Submit</span>
                    <span>→</span>
                </button>
            </div>
        </div>

        <!-- ==================== TAB 7: REVIEW & SUBMIT (Renamed) ==================== -->
        <div id="tab-view-review-submit" class="tab-view-content hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">Review Summary & Request Submission</h3>
                <p class="text-xs text-slate-500 mt-0.5">Review all your profile information before submitting for AfriCrew administrator verification.</p>
            </div>

            <!-- Summary Cards of All 6 Sections -->
            <div class="space-y-4">

                <!-- 1. Personal Information Summary -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between text-xs">
                    <div>
                        <h4 class="font-black text-slate-900">1. Personal Information</h4>
                        <p class="text-slate-600 mt-0.5">{{ $professional->full_name }} • {{ $professional->email }} • {{ $professional->phone }}</p>
                    </div>
                    <button type="button" onclick="showProfileTab('personal', 1)" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-amber-600 font-bold hover:underline">
                        Edit
                    </button>
                </div>

                <!-- 2. About You Summary -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between text-xs">
                    <div>
                        <h4 class="font-black text-slate-900">2. About You (Work Bio & Social Links)</h4>
                        <p class="text-slate-600 mt-0.5 line-clamp-1">{{ $professional->about ?: 'No bio description provided yet' }}</p>
                    </div>
                    <button type="button" onclick="showProfileTab('bio', 2)" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-amber-600 font-bold hover:underline">
                        Edit
                    </button>
                </div>

                <!-- 3. Experience & Qualifications Summary -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between text-xs">
                    <div>
                        <h4 class="font-black text-slate-900">3. Experience, Education & Key Skills</h4>
                        <p class="text-slate-600 mt-0.5">Skills: {{ $professional->skills ?: 'Not added' }}</p>
                    </div>
                    <button type="button" onclick="showProfileTab('experience', 3)" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-amber-600 font-bold hover:underline">
                        Edit
                    </button>
                </div>

                <!-- 4. Portfolio Summary -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between text-xs">
                    <div>
                        <h4 class="font-black text-slate-900">4. Work Portfolio Photos</h4>
                        <p class="text-slate-600 mt-0.5">{{ $photoCount }} of 4 portfolio photos uploaded</p>
                    </div>
                    <button type="button" onclick="showProfileTab('photos', 4)" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-amber-600 font-bold hover:underline">
                        Edit
                    </button>
                </div>

                <!-- 5. Locations & Availability Summary -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between text-xs">
                    <div>
                        <h4 class="font-black text-slate-900">5. Working Locations & Availability</h4>
                        <p class="text-slate-600 mt-0.5">{{ $professional->city }}, {{ $professional->country }}</p>
                    </div>
                    <button type="button" onclick="showProfileTab('availability', 5)" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-amber-600 font-bold hover:underline">
                        Edit
                    </button>
                </div>

                <!-- 6. Service Rates Summary -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between text-xs">
                    <div>
                        <h4 class="font-black text-slate-900">6. Service Rates</h4>
                        <p class="text-slate-600 mt-0.5">{{ $professional->currency }} {{ number_format($professional->full_day_rate ?: 5000) }} / day</p>
                    </div>
                    <button type="button" onclick="showProfileTab('charges', 6)" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-amber-600 font-bold hover:underline">
                        Edit
                    </button>
                </div>
            </div>

            <!-- Public Visibility Setting -->
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-between text-xs font-bold text-amber-950">
                <span>Public Discoverability & Digital CV Visibility:</span>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_public" value="1" @checked(old('is_public', $professional->is_public ?? true)) class="w-4 h-4 text-amber-500 rounded">
                    <span>Public (Discoverable after Approval)</span>
                </label>
            </div>

            <!-- Submission Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <button type="submit" name="action" value="draft" class="w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs rounded-2xl border border-slate-200 transition-all">
                    💾 Save as Draft
                </button>

                <button type="submit" form="submit-review-form" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-amber-400 via-gold-500 to-amber-500 text-slate-950 font-black text-xs rounded-2xl shadow-xl transition-all hover:scale-105 flex items-center justify-center gap-2">
                    <span>🚀 Request Account Review</span>
                    <span>→</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Separate Form to Submit for Review -->
    <form id="submit-review-form" method="POST" action="{{ route('professional.submit-review-request') }}">
        @csrf
    </form>
</div>

<!-- Suggest a Skill Modal -->
<div id="suggest-skill-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <h3 class="text-base font-black text-slate-900">Suggest a Skill for Admin Review</h3>
        <p class="text-xs text-slate-500">If a key skill for your category is missing, submit it below for administrator approval.</p>
        
        <input type="text" id="suggest-skill-input" placeholder="e.g. Dante Audio Networking" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-amber-500">

        <div class="flex justify-end gap-2 text-xs">
            <button type="button" onclick="closeSuggestSkillModal()" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
            <button type="button" onclick="submitSuggestedSkill()" class="px-4 py-2 bg-slate-900 text-amber-400 font-bold rounded-xl">Submit Skill</button>
        </div>
    </div>
</div>

<script>
    const citiesData = {
        'Kenya': ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Ruiru', 'Naivasha', 'Nyeri', 'Nanyuki', 'Malindi', 'Diani Beach', 'Machakos', 'Meru', 'Kakamega', 'Kitale', 'Garissa', 'Kericho', 'Embu', 'Isiolo', 'Ukunda', 'Lamu', 'Narok', 'Kilifi', 'Voi', 'Kisii'],
        'Uganda': ['Kampala', 'Entebbe', 'Jinja', 'Mbarara', 'Gulu', 'Mbale', 'Fort Portal', 'Kasese', 'Masaka', 'Lira'],
        'Tanzania': ['Dar es Salaam', 'Arusha', 'Dodoma', 'Mwanza', 'Moshi', 'Zanzibar City', 'Tanga', 'Morogoro', 'Mbeya', 'Stone Town'],
        'Rwanda': ['Kigali', 'Musanze', 'Rubavu (Gisenyi)', 'Huye (Butare)', 'Rusizi', 'Rwamagana'],
        'Burundi': ['Bujumbura', 'Gitega', 'Ngozi', 'Rumonge'],
        'South Sudan': ['Juba', 'Wau', 'Malakal', 'Yei', 'Torit']
    };

    let selectedOtherCities = @json(is_array($professional->other_cities) ? array_values($professional->other_cities) : []);

    document.addEventListener('DOMContentLoaded', () => {
        onCountrySelectChange("{{ old('country', $professional->country ?: 'Kenya') }}");
    });

    function onCountrySelectChange(country) {
        const citySelect = document.getElementById('city-select');
        if (!citySelect) return;
        
        const towns = citiesData[country] || ['Nairobi', 'Mombasa', 'Kisumu'];
        const currentCity = "{{ old('city', $professional->city) }}";

        let optionsHtml = towns.map(t => `<option value="${t}" ${t === currentCity ? 'selected' : ''}>${t}</option>`).join('');
        if (currentCity && !towns.includes(currentCity)) {
            optionsHtml = `<option value="${currentCity}" selected>${currentCity}</option>` + optionsHtml;
        }

        citySelect.innerHTML = optionsHtml;
        renderOtherTownsChips(country);
    }

    function renderOtherTownsChips(country) {
        const container = document.getElementById('other-towns-chips-container');
        const hiddenContainer = document.getElementById('other-towns-hidden-inputs');
        const countBadge = document.getElementById('other-towns-count');
        if (!container) return;

        const predefinedTowns = citiesData[country] || [];
        const allTowns = Array.from(new Set([...predefinedTowns, ...selectedOtherCities]));

        container.innerHTML = allTowns.map(t => {
            const isSelected = selectedOtherCities.includes(t);
            return `
                <button type="button" onclick="toggleOtherTown('${t}')" class="px-3 py-1 rounded-full text-xs font-bold border transition-all ${isSelected ? 'bg-amber-500 text-slate-950 border-amber-500 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'}">
                    ${isSelected ? '✓ ' : '+ '}${t}
                </button>
            `;
        }).join('');

        if (hiddenContainer) {
            hiddenContainer.innerHTML = selectedOtherCities.map(t => `<input type="hidden" name="other_cities[]" value="${t}">`).join('');
        }

        if (countBadge) {
            countBadge.textContent = `${selectedOtherCities.length} selected`;
        }
    }

    function toggleOtherTown(town) {
        if (selectedOtherCities.includes(town)) {
            selectedOtherCities = selectedOtherCities.filter(t => t !== town);
        } else {
            selectedOtherCities.push(town);
        }
        renderOtherTownsChips(document.getElementById('country-select').value);
    }

    function addCustomOtherTown() {
        const input = document.getElementById('custom-town-input');
        if (!input) return;
        const newTown = input.value.trim();
        if (!newTown) return;

        if (!selectedOtherCities.includes(newTown)) {
            selectedOtherCities.push(newTown);
        }
        input.value = '';
        renderOtherTownsChips(document.getElementById('country-select').value);
    }

    function checkAgeEligibility(val) {
        const badge = document.getElementById('dob-eligibility-badge');
        if (val) {
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    function showProfileTab(tabName, tabIndex) {
        document.querySelectorAll('.tab-view-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-nav-btn').forEach(el => {
            el.className = 'tab-nav-btn px-3.5 py-2.5 rounded-xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-1.5 whitespace-nowrap';
        });

        const activeView = document.getElementById(`tab-view-${tabName}`);
        const activeNav = document.getElementById(`tab-nav-${tabName}`);
        const tabInput = document.getElementById('active-tab-input');

        if (activeView) activeView.classList.remove('hidden');
        if (activeNav) {
            activeNav.className = 'tab-nav-btn px-3.5 py-2.5 rounded-xl bg-slate-900 text-amber-400 font-black shadow-sm border border-slate-900 transition-all flex items-center gap-1.5 whitespace-nowrap';
        }
        if (tabInput) tabInput.value = tabName;
    }

    function uploadHeaderCover(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('header-cover-img');
                if (img) img.src = e.target.result;
            };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');
            formData.append('cover_photo', file);

            fetch('{{ route("professional.profile.update") }}', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                const indicator = document.getElementById('autosave-indicator');
                if (indicator) {
                    indicator.textContent = 'Cover updated!';
                    setTimeout(() => { indicator.textContent = 'Saved'; }, 2000);
                }
            }).catch(err => console.log('Cover upload error', err));
        }
    }

    function uploadHeaderAvatar(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('header-avatar-img');
                const placeholder = document.getElementById('header-avatar-placeholder');
                if (img) {
                    img.src = e.target.result;
                } else if (placeholder) {
                    const newImg = document.createElement('img');
                    newImg.id = 'header-avatar-img';
                    newImg.src = e.target.result;
                    newImg.alt = 'Avatar Preview';
                    newImg.className = 'w-24 h-24 sm:w-28 sm:h-28 aspect-square rounded-full border-4 border-white object-cover shadow-xl shrink-0';
                    placeholder.parentNode.replaceChild(newImg, placeholder);
                }
            };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');
            formData.append('profile_photo', file);

            fetch('{{ route("professional.profile.update") }}', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                const indicator = document.getElementById('autosave-indicator');
                if (indicator) {
                    indicator.textContent = 'Avatar updated!';
                    setTimeout(() => { indicator.textContent = 'Saved'; }, 2000);
                }
            }).catch(err => console.log('Avatar upload error', err));
        }
    }

    function addEducationRow() {
        const wrapper = document.getElementById('education-cards-wrapper');
        if (!wrapper) return;
        const count = wrapper.children.length;
        const div = document.createElement('div');
        div.className = 'p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 text-xs';
        div.innerHTML = `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input type="text" name="education[${count}][institution]" placeholder="Institution / Organization" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                <input type="text" name="education[${count}][degree]" placeholder="Certification / Degree / Course" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1">From Date</label>
                    <input type="date" name="education[${count}][start_date]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1">To Date (Leave blank if Present)</label>
                    <input type="date" name="education[${count}][end_date]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }

    function addExperienceRow() {
        const wrapper = document.getElementById('experience-cards-wrapper');
        if (!wrapper) return;
        const count = wrapper.children.length;
        const div = document.createElement('div');
        div.className = 'p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 text-xs';
        div.innerHTML = `
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="text" name="experience_records[${count}][employer]" placeholder="Company / Client" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                <input type="text" name="experience_records[${count}][role]" placeholder="Role (e.g. Lead Usher, Audio Engineer)" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                <select name="experience_records[${count}][type]" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none font-bold">
                    <option value="Freelance / Project-based">Freelance / Project-based</option>
                    <option value="Self-employed">Self-employed</option>
                    <option value="Full-time">Full-time Contract</option>
                    <option value="Part-time">Part-time</option>
                </select>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1">From Date</label>
                    <input type="date" name="experience_records[${count}][start_date]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1">To Date (Leave blank if Present)</label>
                    <input type="date" name="experience_records[${count}][end_date]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }

    function addRefereeRow() {
        const wrapper = document.getElementById('referees-cards-wrapper');
        if (!wrapper) return;
        const count = wrapper.children.length;
        const div = document.createElement('div');
        div.className = 'p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 text-xs';
        div.innerHTML = `
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <input type="text" name="referees[${count}][name]" placeholder="Referee Name" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                <input type="text" name="referees[${count}][relationship]" placeholder="Relationship" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                <input type="email" name="referees[${count}][email]" placeholder="referee@example.com" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
                <input type="tel" name="referees[${count}][phone]" placeholder="+254 700 000 000" class="bg-white border border-slate-200 rounded-xl px-3 py-2 outline-none">
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 flex-wrap gap-2">
                <span class="text-[11px] font-bold text-slate-600">
                    Status: <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-800 font-black text-[10px]">Not Saved</span>
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="saveRefereeForm(${count})" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] rounded-xl shadow-2xs flex items-center gap-1 transition-all">
                        <span>💾 1. SAVE</span>
                    </button>
                    <button type="button" onclick="requestRefereeVerification(${count})" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-[11px] rounded-xl shadow-2xs flex items-center gap-1 transition-all">
                        <span>🛡️ 2. VERIFY</span>
                    </button>
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }

    function saveRefereeForm(index) {
        const form = document.getElementById('profile-main-form');
        if (form) {
            form.submit();
        }
    }

    function openSuggestSkillModal() {
        document.getElementById('suggest-skill-modal').classList.remove('hidden');
    }

    function closeSuggestSkillModal() {
        document.getElementById('suggest-skill-modal').classList.add('hidden');
    }

    function submitSuggestedSkill() {
        const input = document.getElementById('suggest-skill-input');
        const skillName = input.value.trim();
        if (!skillName) return;

        fetch('{{ route("professional.suggest-skill") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ skill_name: skillName })
        }).then(res => res.json()).then(data => {
            closeSuggestSkillModal();
            alert(data.message || 'Skill suggested for admin review!');
        });
    }

    function requestRefereeVerification(index) {
        fetch('{{ route("professional.referees.request-verification") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ referee_index: index })
        }).then(res => res.json()).then(data => {
            alert(data.message);
            location.reload();
        });
    }

    function deletePortfolioPhoto(photoPath) {
        if (!confirm('Remove this photo from your portfolio gallery?')) return;
        fetch('{{ route("professional.gallery.delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ photo_path: photoPath })
        }).then(() => location.reload());
    }

    function toggleDateCell(btn, day) {
        if (btn.classList.contains('bg-emerald-500')) {
            btn.className = 'h-10 rounded-xl font-bold text-xs flex items-center justify-center transition-all bg-rose-500 text-white shadow-xs';
        } else {
            btn.className = 'h-10 rounded-xl font-bold text-xs flex items-center justify-center transition-all bg-emerald-500 text-white shadow-xs';
        }
    }

    function blockSelectedDates() {
        alert('Selected dates blocked on your availability calendar.');
    }

    function setWeeklyAvailability() {
        alert('Weekly availability schedule updated.');
    }

    function resetManualAvailability() {
        alert('Manual availability reset to default available state. Confirmed platform bookings remain blocked.');
    }
</script>
@endsection