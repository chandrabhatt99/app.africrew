@extends('layouts.staff')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-16">

    <!-- Page Header & Title -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/80 pb-5">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Crew Resume Onboarding</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">Complete your 5-step professional profile to get verified and start receiving event booking requests.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-600 font-extrabold text-xs">
                Step <span id="current-step-num">1</span> of 5
            </span>
        </div>
    </div>

    @if(!$professional->is_onboarded)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold flex items-center gap-3">
            <span class="text-lg">🔒</span>
            <div>
                <span class="font-extrabold block">Mandatory Crew Profile Setup</span>
                <span class="font-normal text-amber-800">You must fill out and submit this resume wizard to activate your crew portal access (Dashboard, Shifts, Messages).</span>
            </div>
        </div>
    @endif

    <!-- Stepper Navigation Header -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-4 shadow-sm overflow-x-auto">
        <div class="flex items-center justify-between min-w-[640px] text-xs font-bold">
            
            <button onclick="goToStep(1)" id="step-tab-1" class="flex-1 flex items-center justify-center gap-2 py-3 px-2 rounded-2xl transition-all bg-amber-500 text-slate-950 font-black shadow-sm">
                <span class="w-6 h-6 rounded-full bg-slate-950/20 flex items-center justify-center text-xs">1</span>
                <span>Personal</span>
            </button>

            <div class="w-4 h-0.5 bg-slate-200"></div>

            <button onclick="goToStep(2)" id="step-tab-2" class="flex-1 flex items-center justify-center gap-2 py-3 px-2 rounded-2xl transition-all text-slate-600 hover:bg-slate-100">
                <span class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-xs text-slate-700">2</span>
                <span>Experience</span>
            </button>

            <div class="w-4 h-0.5 bg-slate-200"></div>

            <button onclick="goToStep(3)" id="step-tab-3" class="flex-1 flex items-center justify-center gap-2 py-3 px-2 rounded-2xl transition-all text-slate-600 hover:bg-slate-100">
                <span class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-xs text-slate-700">3</span>
                <span>Uploads</span>
            </button>

            <div class="w-4 h-0.5 bg-slate-200"></div>

            <button onclick="goToStep(4)" id="step-tab-4" class="flex-1 flex items-center justify-center gap-2 py-3 px-2 rounded-2xl transition-all text-slate-600 hover:bg-slate-100">
                <span class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-xs text-slate-700">4</span>
                <span>Availability</span>
            </button>

            <div class="w-4 h-0.5 bg-slate-200"></div>

            <button onclick="goToStep(5)" id="step-tab-5" class="flex-1 flex items-center justify-center gap-2 py-3 px-2 rounded-2xl transition-all text-slate-600 hover:bg-slate-100">
                <span class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-xs text-slate-700">5</span>
                <span>Labour Charges</span>
            </button>

        </div>
    </div>

    <!-- Onboarding Form Container -->
    <form method="POST" action="{{ route('professional.onboarding.store') }}" enctype="multipart/form-data" id="onboarding-form" class="space-y-8">
        @csrf

        <!-- STEP 1: Personal Details -->
        <div id="step-content-1" class="step-card bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">Step 1: Personal Details</h3>
                <p class="text-xs text-slate-500 mt-1">Tell event organizers about yourself and how to contact you.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">First Name *</label>
                    <input type="text" name="first_name" required value="{{ old('first_name', $professional->first_name ?? ($professional->full_name !== $professional->username ? strtok($professional->full_name, ' ') : '')) }}" placeholder="e.g. Alpha"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Last Name *</label>
                    <input type="text" name="last_name" required value="{{ old('last_name', $professional->last_name ?? '') }}" placeholder="e.g. Dweet"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Username *</label>
                    <input type="text" name="username" readonly value="{{ old('username', $professional->username ?? '') }}"
                        class="w-full bg-slate-100 border border-slate-200 text-slate-600 text-xs font-bold rounded-xl px-4 py-3 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Email Address *</label>
                    <input type="email" name="email" required value="{{ old('email', $professional->email) }}" placeholder="alpha@africrew.com"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Country *</label>
                    <select name="country" id="country-select" onchange="updateCounties()" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors font-semibold">
                        <option value="Kenya" {{ ($professional->country ?? 'Kenya') === 'Kenya' ? 'selected' : '' }}>Kenya</option>
                        <option value="Uganda">Uganda</option>
                        <option value="Tanzania">Tanzania</option>
                        <option value="Rwanda">Rwanda</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">State / County *</label>
                    <select name="state" id="state-select" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors font-semibold">
                        <option value="Nairobi County">Nairobi County</option>
                        <option value="Mombasa County">Mombasa County</option>
                        <option value="Nakuru County">Nakuru County</option>
                        <option value="Kiambu County">Kiambu County</option>
                        <option value="Kisumu County">Kisumu County</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">City *</label>
                    <input type="text" name="city" required value="{{ old('city', $professional->city ?? 'Nairobi') }}" placeholder="e.g. Nairobi"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number *</label>
                    <input type="tel" name="phone" required value="{{ old('phone', $professional->phone) }}" placeholder="+254 712 345 678"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Primary Crew Role *</label>
                    <select name="category" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors font-semibold">
                        <option value="">-- Select a Role --</option>
                        <option value="Usher" {{ $professional->category === 'Usher' ? 'selected' : '' }}>Usher</option>
                        <option value="Lead Usher" {{ $professional->category === 'Lead Usher' ? 'selected' : '' }}>Lead Usher</option>
                        <option value="Host" {{ $professional->category === 'Host' ? 'selected' : '' }}>Host / Hostess</option>
                        <option value="VIP Coordinator" {{ $professional->category === 'VIP Coordinator' ? 'selected' : '' }}>VIP Coordinator</option>
                        <option value="ENTERTAINER" {{ $professional->category === 'ENTERTAINER' ? 'selected' : '' }}>Entertainer / Live Performer</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700">Professional Bio *</label>
                        <span id="bio-char-count" class="text-xs font-bold text-amber-600">0/250 (250 more needed)</span>
                    </div>
                    <textarea name="about" id="bio-textarea" rows="5" oninput="updateBioCounter()" placeholder="Write a detailed biography about your event crewing experience, VIP greeting skills, and stage performance background..."
                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-4 outline-none focus:border-amber-500 focus:bg-white transition-colors">{{ old('about', $professional->about) }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1">A minimum of 250 characters is required for profile verification.</p>
                </div>
            </div>
        </div>

        <!-- STEP 2: Education & Experience -->
        <div id="step-content-2" class="step-card hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">Step 2: Education & Work Experience</h3>
                <p class="text-xs text-slate-500 mt-1">Highlight your educational background and past event ushering records.</p>
            </div>

            <!-- Education Section -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-800">Education Records</h4>
                    <button type="button" onclick="openEducationModal()" class="px-3.5 py-2 rounded-xl bg-slate-900 text-amber-400 font-extrabold text-xs hover:bg-slate-800 transition-all flex items-center gap-1">
                        <span>+ Add Education</span>
                    </button>
                </div>

                <div id="education-list" class="space-y-3">
                    @if(!empty($professional->education) && count($professional->education) > 0)
                        @foreach($professional->education as $edu)
                            @php
                                $eduItem = is_string($edu) ? json_decode($edu, true) : $edu;
                            @endphp
                            @if(is_array($eduItem))
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                    <div>
                                        <div class="text-xs font-black text-slate-900">{{ $eduItem['institution'] ?? 'Institution' }}</div>
                                        <div class="text-[11px] text-slate-500 font-medium">{{ $eduItem['degree'] ?? 'Degree' }} in {{ $eduItem['field'] ?? 'Field' }} ({{ $eduItem['start_date'] ?? '' }} - {{ $eduItem['end_date'] ?? '' }})</div>
                                    </div>
                                    <input type="hidden" name="education[]" value="{{ json_encode($eduItem) }}">
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="p-8 rounded-2xl border-2 border-dashed border-slate-200 text-center text-slate-400 text-xs font-medium">
                            No education added yet. Click "+ Add Education" to include your degree or diploma.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Experience Section -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-800">Work Experience</h4>
                    <button type="button" onclick="openExperienceModal()" class="px-3.5 py-2 rounded-xl bg-slate-900 text-amber-400 font-extrabold text-xs hover:bg-slate-800 transition-all flex items-center gap-1">
                        <span>+ Add Experience</span>
                    </button>
                </div>

                <div id="experience-list" class="space-y-3">
                    @if(!empty($professional->experience_records) && count($professional->experience_records) > 0)
                        @foreach($professional->experience_records as $exp)
                            @php
                                $expItem = is_string($exp) ? json_decode($exp, true) : $exp;
                            @endphp
                            @if(is_array($expItem))
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                    <div>
                                        <div class="text-xs font-black text-slate-900">{{ $expItem['employer'] ?? 'Employer' }} — <span class="text-amber-600">{{ $expItem['role'] ?? 'Crew' }}</span></div>
                                        <div class="text-[11px] text-slate-500 font-medium mt-0.5">{{ $expItem['responsibilities'] ?? '' }}</div>
                                    </div>
                                    <input type="hidden" name="experience_records[]" value="{{ json_encode($expItem) }}">
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="p-8 rounded-2xl border-2 border-dashed border-slate-200 text-center text-slate-400 text-xs font-medium">
                            No experience added yet. Click "+ Add Experience" to record past crewing gigs.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- STEP 3: Uploads & Portfolio -->
        <div id="step-content-3" class="step-card hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">Step 3: Uploads & Portfolio</h3>
                <p class="text-xs text-slate-500 mt-1">Upload your avatar, cover banner, and on-duty event gallery photos.</p>
            </div>

            <!-- Profile & Cover Photo Upload Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Avatar Upload -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">Profile Photo *</label>
                    <div class="flex flex-col items-center p-5 border-2 border-dashed border-slate-200 rounded-3xl text-center bg-slate-50">
                        @if($professional->profile_photo)
                            <img id="avatar-preview" src="{{ get_storage_url($professional->profile_photo) }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md mb-3">
                        @else
                            <div id="avatar-placeholder" class="w-24 h-24 rounded-full bg-amber-500/20 text-slate-900 font-black text-3xl flex items-center justify-center mb-3">
                                {{ strtoupper(substr($professional->full_name, 0, 1)) }}
                            </div>
                            <img id="avatar-preview" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md mb-3 hidden">
                        @endif
                        <input type="file" name="profile_photo" id="profile_photo_input" accept="image/*" onchange="previewImage(this, 'avatar-preview', 'avatar-placeholder')" class="hidden">
                        <label for="profile_photo_input" class="px-4 py-2 rounded-xl bg-slate-900 text-amber-400 font-black text-xs cursor-pointer hover:bg-slate-800 transition-all">
                            Upload Avatar
                        </label>
                    </div>
                </div>

                <!-- Cover Photo Banner Upload -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">Cover Photo Banner</label>
                    <div class="p-5 border-2 border-dashed border-slate-200 rounded-3xl text-center bg-slate-50 flex flex-col items-center justify-center min-h-[160px]">
                        @if($professional->cover_photo)
                            <img id="cover-preview" src="{{ get_storage_url($professional->cover_photo) }}" class="w-full h-24 rounded-2xl object-cover border border-slate-200 shadow-sm mb-3">
                        @else
                            <img id="cover-preview" class="w-full h-24 rounded-2xl object-cover border border-slate-200 shadow-sm mb-3 hidden">
                        @endif
                        <input type="file" name="cover_photo" id="cover_photo_input" accept="image/*" onchange="previewImage(this, 'cover-preview')" class="hidden">
                        <label for="cover_photo_input" class="px-5 py-2.5 rounded-xl bg-slate-900 text-amber-400 font-black text-xs cursor-pointer hover:bg-slate-800 transition-all flex items-center gap-1.5">
                            <span>✏️ Edit Cover Banner</span>
                        </label>
                    </div>
                </div>

            </div>

            <!-- Helper Text Card -->
            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-slate-800 text-xs font-medium flex items-center gap-3">
                <span class="text-xl">📸</span>
                <span><em>"Snap pics while ushering at events. Smiling, well-dressed on-duty shots increase your booking chances by up to 40%."</em></span>
            </div>

            <!-- Gallery Photos (4-slot Grid) -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700">Gallery Portfolio Photos (4 Slots)</label>
                    <span class="text-[11px] text-amber-700 font-bold">Select photos to upload & preview in real-time</span>
                </div>
                
                @php
                    $existingGallery = is_array($professional->gallery_photos) ? $professional->gallery_photos : [];
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @for($i = 0; $i < 4; $i++)
                        @php
                            $existingPhoto = $existingGallery[$i] ?? null;
                            $existingUrl = get_storage_url($existingPhoto);
                        @endphp
                        <div class="relative aspect-square border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 flex flex-col items-center justify-center p-2 text-center group hover:border-amber-400 transition-all overflow-hidden shadow-sm">
                            
                            <!-- Image Preview Element -->
                            <img id="gallery-preview-{{ $i }}" 
                                 src="{{ $existingUrl ?? '' }}" 
                                 class="absolute inset-0 w-full h-full object-cover rounded-xl {{ $existingUrl ? '' : 'hidden' }}">

                            <!-- Placeholder Content when no image selected -->
                            <div id="gallery-placeholder-{{ $i }}" class="flex flex-col items-center justify-center {{ $existingUrl ? 'hidden' : '' }}">
                                <span class="text-2xl text-slate-300 mb-1">🖼️</span>
                                <span class="text-[10px] font-extrabold text-slate-400">Slot {{ $i + 1 }}</span>
                                <span class="text-[9px] text-amber-600 font-bold mt-1">Click to Upload</span>
                            </div>

                            <!-- File Input -->
                            <input type="file" 
                                   name="gallery_photos[]" 
                                   accept="image/*" 
                                   onchange="previewGalleryImage(this, 'gallery-preview-{{ $i }}', 'gallery-placeholder-{{ $i }}')" 
                                   class="absolute inset-0 opacity-0 cursor-pointer z-10">

                            <!-- Hover Overlay Tag -->
                            <div class="absolute bottom-1.5 right-1.5 z-20 bg-slate-900/80 backdrop-blur text-white text-[9px] font-bold px-2 py-0.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                {{ $existingUrl ? 'Change' : 'Upload' }}
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- STEP 4: Availability & Booking Rules -->
        <div id="step-content-4" class="step-card hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">Step 4: Availability & Booking Rules</h3>
                <p class="text-xs text-slate-500 mt-1">Configure your booking policy options and select available working days on the calendar.</p>
            </div>

            <!-- Booking Policy Option Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                
                <label class="cursor-pointer">
                    <input type="radio" name="booking_policy" value="instant" checked class="peer hidden">
                    <div class="p-5 rounded-3xl border-2 border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50/50 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-black text-slate-900">Option A: Instant Book</span>
                            <span class="w-5 h-5 rounded-full border-2 border-slate-300 peer-checked:border-amber-500 peer-checked:bg-amber-500 flex items-center justify-center"></span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed font-normal"><em>"Organizers can book you automatically for open shifts."</em></p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="booking_policy" value="approve" class="peer hidden">
                    <div class="p-5 rounded-3xl border-2 border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50/50 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-black text-slate-900">Option B: Request Approval</span>
                            <span class="w-5 h-5 rounded-full border-2 border-slate-300 peer-checked:border-amber-500 peer-checked:bg-amber-500 flex items-center justify-center"></span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed font-normal"><em>"Organizers must ask if they can book you before confirming."</em></p>
                    </div>
                </label>

            </div>

            <!-- Availability Calendar Matrix Section -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-800">3-Month Availability Calendar</h4>
                    <div class="flex items-center gap-4 text-xs font-bold">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-700">
                            <input type="checkbox" id="select-all-days" onchange="toggleSelectAllDays(this)" class="rounded border-slate-300 text-amber-500 focus:ring-amber-400">
                            <span>Select all days</span>
                        </label>
                    </div>
                </div>

                <!-- Calendar Legend -->
                <div class="flex items-center gap-4 text-xs font-semibold text-slate-600 bg-slate-50 p-3 rounded-2xl border border-slate-200">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-slate-200"></span> Default (Unavailable)</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Available (Selected)</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Booked Shift</span>
                </div>

                <!-- 3-Month Matrix Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                    @for($m = 0; $m < 3; $m++)
                        @php
                            $monthTime = strtotime("+{$m} month");
                            $monthName = date('F Y', $monthTime);
                            $daysInMonth = date('t', $monthTime);
                        @endphp
                        <div class="bg-slate-50 border border-slate-200 rounded-3xl p-4 space-y-3 text-center">
                            <div class="text-xs font-black text-slate-900 border-b border-slate-200 pb-2">{{ $monthName }}</div>
                            <div class="grid grid-cols-7 gap-1 text-[10px] font-bold text-slate-400">
                                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                            </div>
                            <div class="grid grid-cols-7 gap-1.5">
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dateStr = date('Y-m-', $monthTime) . sprintf('%02d', $d);
                                        $isSelected = in_array($dateStr, $professional->availability_dates ?? []);
                                    @endphp
                                    <button type="button" onclick="toggleDate(this, '{{ $dateStr }}')"
                                        class="calendar-day-btn h-8 rounded-xl text-xs font-bold transition-all {{ $isSelected ? 'bg-emerald-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:border-amber-400' }}">
                                        {{ $d }}
                                    </button>
                                @endfor
                            </div>
                        </div>
                    @endfor
                </div>

                <div id="availability-hidden-inputs">
                    @foreach($professional->availability_dates ?? [] as $dateVal)
                        <input type="hidden" name="availability_dates[]" value="{{ $dateVal }}">
                    @endforeach
                </div>
            </div>
        </div>

        <!-- STEP 5: Labour Charges & Pricing -->
        <div id="step-content-5" class="step-card hidden bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-900">Step 5: Labour Charges & Pricing</h3>
                <p class="text-xs text-slate-500 mt-1">Set your standard daily rates and multi-day discounts in KES.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">1 Day Rate (KES) *</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-xs font-bold text-slate-400">KES</span>
                        <input type="number" step="0.01" name="one_day_rate" value="{{ old('one_day_rate', $professional->one_day_rate ?? 5000.00) }}" required placeholder="5000.00"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs font-bold rounded-xl pl-14 pr-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">2 Day Rate (KES) *</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-xs font-bold text-slate-400">KES</span>
                        <input type="number" step="0.01" name="two_day_rate" value="{{ old('two_day_rate', $professional->two_day_rate ?? 9500.00) }}" required placeholder="9500.00"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs font-bold rounded-xl pl-14 pr-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Rehearsal / Setup Day Charge (KES) *</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-xs font-bold text-slate-400">KES</span>
                        <input type="number" step="0.01" name="rehearsal_rate" value="{{ old('rehearsal_rate', $professional->rehearsal_rate ?? 2500.00) }}" required placeholder="2500.00"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs font-bold rounded-xl pl-14 pr-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Multi-Day Discount (%)</label>
                    <div class="relative">
                        <input type="number" min="0" max="100" name="multi_day_discount" value="{{ old('multi_day_discount', $professional->multi_day_discount ?? 10) }}" placeholder="10"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs font-bold rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                        <span class="absolute right-4 top-3 text-xs font-bold text-slate-400">%</span>
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 font-medium leading-relaxed">
                💡 <strong>Pricing Tip:</strong> Setting transparent KES daily rates helps event managers quickly approve your booking without manual back-and-forth negotiations.
            </div>
        </div>

        <!-- Wizard Sticky Footer Actions -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-4 shadow-xl flex items-center justify-between gap-4 sticky bottom-4 z-20">
            <button type="button" id="prev-btn" onclick="prevStep()" class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold text-xs hover:bg-slate-200 transition-all opacity-50 cursor-not-allowed">
                ← Back
            </button>

            <div class="flex items-center gap-3">
                <button type="submit" name="action" value="draft" class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-800 font-extrabold text-xs hover:bg-slate-200 transition-all">
                    Save Draft
                </button>

                <button type="button" id="next-btn" onclick="nextStep()" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-amber-400 via-gold-500 to-amber-500 text-slate-950 font-black text-xs hover:brightness-105 shadow-md transition-all flex items-center gap-1.5">
                    <span>Next</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>

                <button type="submit" id="submit-btn" class="px-7 py-3 rounded-2xl bg-gradient-to-r from-amber-400 via-gold-500 to-amber-500 text-slate-950 font-black text-xs hover:brightness-105 shadow-md transition-all hidden">
                    Submit Onboarding Profile ✓
                </button>
            </div>
        </div>

    </form>

</div>

<!-- Education Modal -->
<div id="education-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4">
        <h3 class="text-lg font-black text-slate-900">Add Education Record</h3>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Institution Name</label>
            <input type="text" id="edu-inst" placeholder="University of Nairobi" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Degree / Certificate</label>
            <input type="text" id="edu-degree" placeholder="Bachelor of Arts" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Field of Study</label>
            <input type="text" id="edu-field" placeholder="Event Management" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Start Date</label>
                <input type="date" id="edu-start" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">End Date</label>
                <input type="date" id="edu-end" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none">
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 pt-3">
            <button type="button" onclick="closeEducationModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
            <button type="button" onclick="saveEducation()" class="px-5 py-2.5 rounded-xl bg-amber-500 text-slate-950 font-black text-xs shadow-md">Add Record</button>
        </div>
    </div>
</div>

<!-- Experience Modal -->
<div id="experience-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4">
        <h3 class="text-lg font-black text-slate-900">Add Work Experience</h3>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Employer / Event Name</label>
            <input type="text" id="exp-emp" placeholder="Apex Tech Summit 2026" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Role Title</label>
            <input type="text" id="exp-role" placeholder="Senior Usher / Protocol Officer" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Responsibilities Summary</label>
            <textarea id="exp-resp" rows="3" placeholder="Managed VIP registration desk and guest seating flow..." class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 outline-none"></textarea>
        </div>
        <div class="flex items-center justify-end gap-3 pt-3">
            <button type="button" onclick="closeExperienceModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
            <button type="button" onclick="saveExperience()" class="px-5 py-2.5 rounded-xl bg-amber-500 text-slate-950 font-black text-xs shadow-md">Add Experience</button>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;
    let selectedDates = new Set(@json($professional->availability_dates ?? []));

    function updateBioCounter() {
        const textarea = document.getElementById('bio-textarea');
        const countEl = document.getElementById('bio-char-count');
        if (!textarea || !countEl) return;
        
        const len = textarea.value.length;
        if (len < 250) {
            const needed = 250 - len;
            countEl.innerText = `${len}/250 (${needed} more needed)`;
            countEl.className = 'text-xs font-bold text-amber-600';
        } else {
            countEl.innerText = `${len} characters (Verified ✓)`;
            countEl.className = 'text-xs font-bold text-emerald-600';
        }
    }

    function goToStep(step) {
        currentStep = step;
        document.getElementById('current-step-num').innerText = step;

        for (let i = 1; i <= 5; i++) {
            const content = document.getElementById(`step-content-${i}`);
            const tab = document.getElementById(`step-tab-${i}`);
            if (content) content.classList.add('hidden');
            if (tab) {
                tab.className = 'flex-1 flex items-center justify-center gap-2 py-3 px-2 rounded-2xl transition-all text-slate-600 hover:bg-slate-100';
            }
        }

        const activeContent = document.getElementById(`step-content-${step}`);
        const activeTab = document.getElementById(`step-tab-${step}`);
        if (activeContent) activeContent.classList.remove('hidden');
        if (activeTab) {
            activeTab.className = 'flex-1 flex items-center justify-center gap-2 py-3 px-2 rounded-2xl transition-all bg-amber-500 text-slate-950 font-black shadow-sm';
        }

        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const submitBtn = document.getElementById('submit-btn');

        if (step === 1) {
            prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        if (step === 5) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function nextStep() {
        if (currentStep < 5) goToStep(currentStep + 1);
    }

    function prevStep() {
        if (currentStep > 1) goToStep(currentStep - 1);
    }

    function previewImage(input, previewId, placeholderId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                const placeholder = placeholderId ? document.getElementById(placeholderId) : null;
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if (placeholder) placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleDate(btn, dateStr) {
        if (selectedDates.has(dateStr)) {
            selectedDates.delete(dateStr);
            btn.className = 'calendar-day-btn h-8 rounded-xl text-xs font-bold transition-all bg-white border border-slate-200 text-slate-700 hover:border-amber-400';
        } else {
            selectedDates.add(dateStr);
            btn.className = 'calendar-day-btn h-8 rounded-xl text-xs font-bold transition-all bg-emerald-500 text-white shadow-sm';
        }
        renderHiddenDateInputs();
    }

    function toggleSelectAllDays(checkbox) {
        const btns = document.querySelectorAll('.calendar-day-btn');
        btns.forEach(btn => {
            const onclickAttr = btn.getAttribute('onclick');
            const match = onclickAttr ? onclickAttr.match(/'([^']+)'/) : null;
            if (match && match[1]) {
                const dStr = match[1];
                if (checkbox.checked) {
                    selectedDates.add(dStr);
                    btn.className = 'calendar-day-btn h-8 rounded-xl text-xs font-bold transition-all bg-emerald-500 text-white shadow-sm';
                } else {
                    selectedDates.clear();
                    btn.className = 'calendar-day-btn h-8 rounded-xl text-xs font-bold transition-all bg-white border border-slate-200 text-slate-700 hover:border-amber-400';
                }
            }
        });
        renderHiddenDateInputs();
    }

    function renderHiddenDateInputs() {
        const container = document.getElementById('availability-hidden-inputs');
        if (!container) return;
        container.innerHTML = '';
        selectedDates.forEach(d => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'availability_dates[]';
            inp.value = d;
            container.appendChild(inp);
        });
    }

    function openEducationModal() { document.getElementById('education-modal').classList.replace('hidden', 'flex'); }
    function closeEducationModal() { document.getElementById('education-modal').classList.replace('flex', 'hidden'); }

    function saveEducation() {
        const inst = document.getElementById('edu-inst').value;
        const degree = document.getElementById('edu-degree').value;
        const field = document.getElementById('edu-field').value;
        const start = document.getElementById('edu-start').value;
        const end = document.getElementById('edu-end').value;

        if (!inst || !degree) return;

        const eduObj = { institution: inst, degree: degree, field: field, start_date: start, end_date: end };
        const list = document.getElementById('education-list');

        const card = document.createElement('div');
        card.className = 'p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between';
        card.innerHTML = `
            <div>
                <div class="text-xs font-black text-slate-900">${inst}</div>
                <div class="text-[11px] text-slate-500 font-medium">${degree} in ${field} (${start} - ${end})</div>
            </div>
            <input type="hidden" name="education[]" value='${JSON.stringify(eduObj)}'>
        `;
        list.appendChild(card);
        closeEducationModal();
    }

    function openExperienceModal() { document.getElementById('experience-modal').classList.replace('hidden', 'flex'); }
    function closeExperienceModal() { document.getElementById('experience-modal').classList.replace('flex', 'hidden'); }

    function saveExperience() {
        const emp = document.getElementById('exp-emp').value;
        const role = document.getElementById('exp-role').value;
        const resp = document.getElementById('exp-resp').value;

        if (!emp || !role) return;

        const expObj = { employer: emp, role: role, responsibilities: resp };
        const list = document.getElementById('experience-list');

        const card = document.createElement('div');
        card.className = 'p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between';
        card.innerHTML = `
            <div>
                <div class="text-xs font-black text-slate-900">${emp} — <span class="text-amber-600">${role}</span></div>
                <div class="text-[11px] text-slate-500 font-medium mt-0.5">${resp}</div>
            </div>
            <input type="hidden" name="experience_records[]" value='${JSON.stringify(expObj)}'>
        `;
        list.appendChild(card);
        closeExperienceModal();
    }

    function previewGalleryImage(input, previewId, placeholderId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
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

    document.addEventListener('DOMContentLoaded', () => { updateBioCounter(); });
</script>
@endsection
