@extends('admin.layout')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    <!-- Top Navigation & Action Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('admin.professionals.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition-colors">
            <span>← Back to Registered Crew Directory</span>
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('crew.show', $professional->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-xs shadow-xs transition-all flex items-center gap-1.5 text-decoration-none">
                <span>👁️ View Public Profile</span>
            </a>
            <a href="{{ route('admin.professionals.edit', $professional->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-xs transition-all flex items-center gap-1.5 text-decoration-none">
                <span>✏️ Edit Profile & Skills</span>
            </a>
            <button type="button" onclick="confirmDeleteStaffFromShow('{{ $professional->id }}', '{{ addslashes($professional->full_name) }}')" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5">
                <span>🗑️ Delete Account</span>
            </button>
        </div>
    </div>

    <!-- Main Staff Profile Card -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-8">
        
        <!-- Header Profile Summary -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6 border-b border-slate-100 pb-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 text-center sm:text-left">
                @php
                    $avatar = get_storage_url($professional->profile_photo);
                @endphp
                @if($avatar)
                    <img src="{{ $avatar }}" class="w-24 h-24 rounded-3xl object-cover border-4 border-amber-400 shadow-md shrink-0">
                @else
                    <div class="w-24 h-24 rounded-3xl bg-amber-500 text-slate-950 font-black text-3xl flex items-center justify-center border-4 border-amber-300 shadow-md shrink-0">
                        {{ strtoupper(substr($professional->full_name, 0, 1)) }}
                    </div>
                @endif

                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $professional->full_name }}</h1>
                    <div class="text-xs font-extrabold text-amber-700 uppercase tracking-wider">
                        {{ strtoupper($professional->category) }} • @ {{ $professional->username ?? Str::slug(strtok($professional->full_name, ' ')) }}
                    </div>
                    <div class="text-xs text-slate-500 font-semibold flex items-center justify-center sm:justify-start gap-2 flex-wrap">
                        <span>📍 {{ $professional->city ?: 'Nairobi' }}, {{ $professional->country ?: 'Kenya' }}</span>
                        <span>•</span>
                        <span>Registered {{ $professional->created_at ? $professional->created_at->format('M j, Y') : 'N/A' }}</span>
                        <span>•</span>
                        <span class="text-emerald-700 font-bold">Profile {{ $professional->completion_percentage }}% Complete</span>
                    </div>
                </div>
            </div>

            <!-- Current Status Badge & Action Controls -->
            <div class="flex flex-col items-center sm:items-end gap-3">
                <div class="flex items-center gap-2 flex-wrap justify-end">
                    @php
                        $statusClasses = [
                            'pending' => 'bg-amber-100 text-amber-900 border-amber-300',
                            'approved' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                            'rejected' => 'bg-rose-100 text-rose-900 border-rose-300',
                            'deactivated' => 'bg-rose-100 text-rose-900 border-rose-300',
                            'suspended' => 'bg-slate-200 text-slate-900 border-slate-300',
                        ][$professional->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                    @endphp
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-black border uppercase tracking-wider shadow-xs {{ $statusClasses }}">
                        @if($professional->status === 'approved')
                            ✓ Approved
                        @elseif($professional->status === 'rejected')
                            ✕ Rejected
                        @elseif($professional->status === 'deactivated')
                            🚫 Deactivated
                        @else
                            {{ ucfirst($professional->status) }}
                        @endif
                    </span>

                    @if($professional->interview_status === 'scheduled')
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-indigo-100 text-indigo-900 border border-indigo-300 uppercase tracking-wider">
                            🎙️ Interview Scheduled
                        </span>
                    @elseif($professional->interview_status === 'completed')
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-900 border border-emerald-300 uppercase tracking-wider">
                            ✓ Interview Completed
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2 flex-wrap justify-end">
                    <!-- Schedule or Reschedule Interview -->
                    <button type="button" onclick="toggleScheduleInterviewModal()" class="px-3.5 py-2 rounded-xl {{ $professional->interview_status === 'scheduled' ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-amber-500 hover:bg-amber-400 text-slate-950' }} font-extrabold text-xs shadow-xs transition-all flex items-center gap-1.5">
                        <span>🎙️ {{ $professional->interview_status === 'scheduled' ? 'Reschedule Interview' : 'Schedule Interview' }}</span>
                    </button>

                    <!-- Approve Profile -->
                    @if($professional->status !== 'approved')
                        <button type="button" onclick="toggleApproveModal()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition-all flex items-center gap-1.5">
                            <span>✓ Approve Profile</span>
                        </button>
                    @endif

                    <!-- Reject Profile -->
                    @if($professional->status !== 'rejected')
                        <button type="button" onclick="toggleRejectModal()" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition-all flex items-center gap-1.5">
                            <span>✕ Reject Profile</span>
                        </button>
                    @endif

                    @if($professional->status === 'approved')
                        <form method="POST" action="{{ route('admin.professionals.status', $professional) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="deactivated">
                            <button type="submit" onclick="return confirm('Deactivate this crew account?')" class="px-3.5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-extrabold text-xs shadow-xs">
                                🚫 Deactivate
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if($professional->interview_date || $professional->interview_time || $professional->interviewer_name)
            <div class="p-4 rounded-2xl {{ $professional->interview_status === 'scheduled' ? 'bg-amber-500/10 border-amber-500/30' : 'bg-slate-100 border-slate-200' }} border space-y-2 text-xs">
                <div class="font-extrabold text-slate-900 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span>🎙️ Vetting Interview Details</span>
                        <span class="px-2 py-0.5 rounded-full {{ $professional->interview_status === 'scheduled' ? 'bg-amber-200 text-amber-900' : 'bg-emerald-100 text-emerald-900' }} font-black text-[10px] uppercase">
                            {{ ucfirst($professional->interview_status ?? 'pending') }}
                        </span>
                    </span>
                    <button type="button" onclick="toggleScheduleInterviewModal()" class="text-amber-800 font-extrabold hover:underline text-[11px]">Edit / Reschedule →</button>
                </div>
                <div class="text-slate-800 font-semibold grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1">
                    <div>
                        <span class="text-slate-400 font-bold block text-[10px] uppercase">Date & Time</span>
                        <strong>{{ $professional->interview_date ? $professional->interview_date->format('M j, Y') : 'Date not set' }}</strong> @ {{ $professional->interview_time ?: '10:00 AM' }}
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block text-[10px] uppercase">Interviewer</span>
                        <strong>{{ $professional->interviewer_name ?: 'AfriCrew Team' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block text-[10px] uppercase">Meeting Link / Location</span>
                        @if($professional->interview_link)
                            @if(Str::startsWith($professional->interview_link, ['http://', 'https://']))
                                <a href="{{ $professional->interview_link }}" target="_blank" class="text-amber-700 font-bold hover:underline">Join Meeting Link ↗</a>
                            @else
                                <span class="font-bold text-slate-900">{{ $professional->interview_link }}</span>
                            @endif
                        @else
                            <span class="text-slate-400 italic">None provided</span>
                        @endif
                    </div>
                </div>
                @if($professional->interview_notes)
                    <div class="text-slate-600 font-medium italic pt-1 border-t border-slate-200/60">
                        <span class="font-bold text-slate-700 not-italic">Notes:</span> "{{ $professional->interview_notes }}"
                    </div>
                @endif
            </div>
        @endif

        <!-- Section 1: Contact & Identity Grid (Readonly View) -->
        <div class="space-y-4">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                Contact & Identity Details
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Email Address</span>
                    <span class="font-black text-slate-900 text-sm">{{ $professional->email }}</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Phone Number</span>
                    <span class="font-black text-slate-900 text-sm">{{ $professional->phone }}</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Gender</span>
                    <span class="font-black text-slate-900 text-sm">{{ $professional->gender ?: 'Not specified' }}</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Primary Location</span>
                    <span class="font-black text-slate-900 text-sm">{{ $professional->city ?: 'Nairobi' }}, {{ $professional->country ?: 'Kenya' }}</span>
                </div>
            </div>

            <!-- Locations, Languages & Assignment Preferences -->
            @php
                $citiesArr = is_array($professional->other_cities) ? $professional->other_cities : (is_string($professional->other_cities) ? array_map('trim', explode(',', $professional->other_cities)) : []);
                $prefLocsArr = is_array($professional->preferred_locations) ? $professional->preferred_locations : (is_string($professional->preferred_locations) ? array_map('trim', explode(',', $professional->preferred_locations)) : []);
                $allCitiesCombined = array_values(array_unique(array_filter(array_merge([$professional->city ?: 'Nairobi'], $citiesArr, $prefLocsArr))));
                $allCitiesStr = implode(', ', $allCitiesCombined);

                $langsArr = is_array($professional->languages) ? $professional->languages : (is_string($professional->languages) ? array_map('trim', explode(',', $professional->languages)) : []);
                $assignTypesArr = is_array($professional->assignment_types) ? $professional->assignment_types : (is_string($professional->assignment_types) ? array_map('trim', explode(',', $professional->assignment_types)) : []);
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">📍 Working Cities / Locations</span>
                    <span class="font-black text-slate-900">{{ $allCitiesStr ?: 'Nairobi' }}</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">🗣️ Languages Spoken</span>
                    <span class="font-black text-slate-900">{{ implode(', ', array_filter($langsArr)) ?: 'English, Swahili' }}</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">🎯 Preferred Event Types</span>
                    <span class="font-black text-slate-900">{{ implode(', ', array_filter($assignTypesArr)) ?: 'Corporate Galas, Conferences, VIP' }}</span>
                </div>
            </div>
        </div>

        <!-- Section 2: Work Description, Experience Summary, Highlight Quote & Social Media -->
        <div class="space-y-6 pt-4 border-t border-slate-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Work Description (About Me)</span>
                    <p class="text-xs text-slate-700 font-medium leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200 whitespace-pre-line min-h-[90px]">
                        {{ $professional->about ?: 'No work description written.' }}
                    </p>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Experience Summary</span>
                    <p class="text-xs text-slate-700 font-medium leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200 whitespace-pre-line min-h-[90px]">
                        {{ $professional->experience_summary ?: 'No experience summary written.' }}
                    </p>
                </div>
            </div>

            <!-- Today (My Update) & Let's Connect (Tier 1 & Tier 3 Persona) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-orange-50/60 border border-orange-200/80 space-y-1">
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-800 block">📢 Today / My Daily Update (Tier 1 Persona)</span>
                    <p class="text-xs text-slate-800 font-semibold leading-relaxed m-0 whitespace-pre-line">
                        {{ $professional->my_update ?: 'No daily update provided by crew member.' }}
                    </p>
                </div>
                <div class="p-4 rounded-2xl bg-pink-50/60 border border-pink-200/80 space-y-1">
                    <span class="text-[10px] font-black uppercase tracking-wider text-pink-800 block">🤝 Let's Connect / Personal Invitation (Tier 3 Persona)</span>
                    <p class="text-xs text-slate-800 font-semibold leading-relaxed m-0 whitespace-pre-line">
                        {{ $professional->lets_connect ?: 'No personal connection statement provided by crew member.' }}
                    </p>
                </div>
            </div>

            @if(!empty($professional->highlight_quote))
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 italic text-xs font-bold">
                    "{{ $professional->highlight_quote }}"
                </div>
            @endif

            <!-- Social Links & Portfolio Grid (Admin Vetting Eyes Only) -->
            @php
                $socials = is_array($professional->social_links) ? $professional->social_links : [];
                $hasSocials = count(array_filter($socials)) > 0;
            @endphp
            <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200 space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-purple-950 flex items-center gap-1.5">
                        <span>🔒</span> Social Media & Portfolio Links (Admin Vetting Eyes Only)
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-purple-200 text-purple-900 font-black text-[10px]">
                        Strictly Hidden from Public Website Profile
                    </span>
                </div>
                @if($hasSocials)
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        @if(!empty($socials['instagram']))
                            <a href="{{ Str::startsWith($socials['instagram'], 'http') ? $socials['instagram'] : 'https://instagram.com/' . ltrim($socials['instagram'], '@') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white border border-purple-200 text-purple-950 font-bold hover:bg-purple-100 transition-colors">
                                📷 Instagram: {{ $socials['instagram'] }} ↗
                            </a>
                        @endif
                        @if(!empty($socials['linkedin']))
                            <a href="{{ Str::startsWith($socials['linkedin'], 'http') ? $socials['linkedin'] : 'https://linkedin.com/in/' . $socials['linkedin'] }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white border border-purple-200 text-purple-950 font-bold hover:bg-purple-100 transition-colors">
                                💼 LinkedIn: {{ $socials['linkedin'] }} ↗
                            </a>
                        @endif
                        @if(!empty($socials['facebook']))
                            <a href="{{ Str::startsWith($socials['facebook'], 'http') ? $socials['facebook'] : 'https://facebook.com/' . $socials['facebook'] }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white border border-purple-200 text-purple-950 font-bold hover:bg-purple-100 transition-colors">
                                📘 Facebook: {{ $socials['facebook'] }} ↗
                            </a>
                        @endif
                        @if(!empty($socials['tiktok']))
                            <a href="{{ Str::startsWith($socials['tiktok'], 'http') ? $socials['tiktok'] : 'https://tiktok.com/@' . ltrim($socials['tiktok'], '@') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white border border-purple-200 text-purple-950 font-bold hover:bg-purple-100 transition-colors">
                                🎵 TikTok: {{ $socials['tiktok'] }} ↗
                            </a>
                        @endif
                        @if(!empty($socials['x']))
                            <a href="{{ Str::startsWith($socials['x'], 'http') ? $socials['x'] : 'https://x.com/' . ltrim($socials['x'], '@') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white border border-purple-200 text-purple-950 font-bold hover:bg-purple-100 transition-colors">
                                🐦 X: {{ $socials['x'] }} ↗
                            </a>
                        @endif
                        @if(!empty($socials['website']))
                            <a href="{{ Str::startsWith($socials['website'], 'http') ? $socials['website'] : 'https://' . $socials['website'] }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white border border-purple-200 text-purple-950 font-bold hover:bg-purple-100 transition-colors">
                                🌐 Portfolio / Resume Link: {{ $socials['website'] }} ↗
                            </a>
                        @endif
                    </div>
                @else
                    <p class="text-xs text-purple-800 italic">No social media or portfolio links submitted for verification.</p>
                @endif
            </div>

            <!-- Key Skills & Service Capabilities Container -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl border border-slate-800">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4 flex-wrap gap-3">
                    <div>
                        <span class="text-[11px] font-black uppercase tracking-widest text-rose-400 block mb-0.5">CAPABILITIES & SERVICES</span>
                        <h3 class="text-xl font-black text-white tracking-tight flex items-center gap-2">
                            <span>Key Skills & Services Offered</span>
                        </h3>
                    </div>
                </div>

                <!-- Key Skills Badges -->
                <div class="space-y-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Key Skills</span>
                    @php
                        $rawSkills = is_array($professional->skills) ? $professional->skills : array_map('trim', explode(',', $professional->skills ?: ''));
                        $featSkills = is_array($professional->featured_skills) ? $professional->featured_skills : [];
                        $allSkillsCombined = array_values(array_unique(array_filter(array_merge($rawSkills, $featSkills))));
                    @endphp
                    @if(count($allSkillsCombined) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($allSkillsCombined as $sk)
                                @if(trim($sk))
                                    <span class="px-3.5 py-1.5 rounded-xl bg-slate-800/90 border border-slate-700 text-amber-300 font-black text-xs flex items-center gap-1.5 shadow-sm">
                                        <span class="text-amber-400">⚡</span> {{ trim($sk) }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <span class="text-xs text-slate-500 italic block">No key skills added yet.</span>
                    @endif
                </div>

                <!-- Detailed Expertise Areas (Name + Description) -->
                @if(!empty($professional->expertises) && is_array($professional->expertises) && count($professional->expertises) > 0)
                    <div class="space-y-3 pt-3 border-t border-slate-800">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-400 block">Detailed Expertise Areas (Featured on Public Profile)</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($professional->expertises as $expItem)
                                @php $ex = is_string($expItem) ? json_decode($expItem, true) : $expItem; @endphp
                                @if(!empty($ex['name']))
                                    <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 flex items-start gap-3">
                                        @if(!empty($ex['image']))
                                            <img src="{{ get_storage_url($ex['image']) }}" class="w-10 h-10 rounded-xl object-cover border border-slate-700 shrink-0">
                                        @else
                                            <span class="text-xl shrink-0 p-1.5 bg-slate-700/60 rounded-xl">{{ $ex['icon'] ?? '✨' }}</span>
                                        @endif
                                        <div class="space-y-1 min-w-0 flex-1">
                                            <div class="font-extrabold text-amber-300 text-xs flex items-center gap-1.5">
                                                @if(!empty($ex['image']))
                                                    <span>{{ $ex['icon'] ?? '✨' }}</span>
                                                @endif
                                                <span>{{ $ex['name'] }}</span>
                                            </div>
                                            @if(!empty($ex['description']))
                                                <p class="text-[11px] text-slate-300 font-medium leading-relaxed m-0">
                                                    {{ $ex['description'] }}
                                                </p>
                                            @endif
                                            @php
                                                $admSkills = $ex['skills'] ?? ($ex['key_skills'] ?? []);
                                                if (is_string($admSkills)) {
                                                    $admSkills = array_map('trim', explode(',', $admSkills));
                                                }
                                                $admSkills = array_slice(array_values(array_filter((array)$admSkills)), 0, 3);
                                            @endphp
                                            @if(count($admSkills) > 0)
                                                <div class="flex flex-wrap gap-1 pt-1.5 border-t border-slate-700/60">
                                                    @foreach($admSkills as $askill)
                                                        <span class="px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-300 border border-amber-500/20 text-[9px] font-bold">
                                                            {{ $askill }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Section 3: Work Experience Records -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                Work Experience History
            </h3>

            @if(!empty($professional->experience_records) && is_array($professional->experience_records) && count($professional->experience_records) > 0)
                <div class="space-y-3">
                    @foreach($professional->experience_records as $exp)
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
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <div>
                                    <h4 class="font-black text-slate-900 text-sm">{{ $xItem['role'] ?? 'Lead Usher' }}</h4>
                                    <span class="font-extrabold text-amber-700">{{ $xItem['employer'] ?? 'Event Organization' }}</span>
                                </div>
                                @if($startDisp || $endDisp)
                                    <span class="px-3 py-1 rounded-full bg-slate-900 text-white font-extrabold text-[10px] uppercase">
                                        {{ $startDisp }}{{ ($startDisp && $endDisp) ? ' - ' : '' }}{{ $endDisp }}
                                    </span>
                                @endif
                            </div>
                            @if(!empty($xItem['responsibilities']))
                                <p class="text-slate-600 font-medium pt-1 leading-relaxed">
                                    {{ $xItem['responsibilities'] }}
                                </p>
                            @endif

                            @if(!empty($xItem['document']))
                                <div class="pt-2 border-t border-slate-200/80 flex items-center justify-between flex-wrap gap-2">
                                    <span class="text-[11px] font-bold text-slate-500">Supporting Verification Document:</span>
                                    <a href="{{ get_storage_url($xItem['document']) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-950 font-black text-[11px] transition-colors border border-amber-300 shadow-xs">
                                        <span>📄</span> View Work Proof / Reference Document ↗
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-500 italic text-center">
                    No work experience records uploaded.
                </div>
            @endif
        </div>

        <!-- Section 4: Education Background -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                Academic Qualifications & Education
            </h3>

            @if(!empty($professional->education) && is_array($professional->education) && count($professional->education) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    @foreach($professional->education as $edu)
                        @php
                            $eItem = is_string($edu) ? json_decode($edu, true) : $edu;
                            $sDate = $eItem['start_date'] ?? '';
                            $eDate = $eItem['end_date'] ?? '';
                            $startEduDisp = !empty($sDate) && strtotime($sDate) ? date('M Y', strtotime($sDate)) : trim(($eItem['start_month'] ?? '') . ' ' . ($eItem['start_year'] ?? $sDate));
                            $endEduDisp = !empty($eDate) && strtotime($eDate) ? date('M Y', strtotime($eDate)) : trim(($eItem['end_month'] ?? '') . ' ' . ($eDate));
                            if ($startEduDisp && empty($endEduDisp) && !empty($sDate)) {
                                $endEduDisp = 'Present';
                            }
                        @endphp
                        <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/80 space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-900 block">
                                {{ !empty($eItem['degree']) ? $eItem['degree'] : ($eItem['field'] ?? 'Education') }}
                            </span>
                            <h4 class="font-black text-slate-900 text-sm">{{ $eItem['institution'] ?? 'Academic Institution' }}</h4>
                            @if($startEduDisp || $endEduDisp)
                                <span class="text-[11px] text-slate-500 font-medium block">📅 {{ $startEduDisp }} {{ $endEduDisp ? '- ' . $endEduDisp : '' }}</span>
                            @endif

                            @if(!empty($eItem['document']))
                                <div class="pt-2 border-t border-amber-200/80 flex items-center justify-between flex-wrap gap-2">
                                    <span class="text-[11px] font-bold text-amber-900/70">Certificate Proof:</span>
                                    <a href="{{ get_storage_url($eItem['document']) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-200 hover:bg-amber-300 text-amber-950 font-black text-[11px] transition-colors border border-amber-400/60 shadow-xs">
                                        <span>🎓</span> View Certificate / Degree Proof ↗
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-500 italic text-center">
                    No education background entries uploaded.
                </div>
            @endif
        </div>

        <!-- Section 5: Professional Referees & Work Verification (Seen Only by Admin) -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-amber-500 font-black text-sm">👥</span>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 m-0">
                        Professional Referees & Work Verification
                    </h3>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 font-black text-[10px] flex items-center gap-1 border border-purple-200">
                    <span>🔒</span> Confidential • Admin Verification Eyes Only
                </span>
            </div>
            <p class="text-xs text-slate-500 font-medium -mt-1 m-0">
                Contact information for referees provided by {{ $professional->full_name }} to verify previous roles and work quality. Strictly private and never published on their public profile.
            </p>

            @if(!empty($professional->referees) && is_array($professional->referees) && count($professional->referees) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    @foreach($professional->referees as $rIdx => $ref)
                        @php
                            $rItem = is_string($ref) ? json_decode($ref, true) : $ref;
                            $rStatus = strtolower($rItem['status'] ?? 'pending');
                            $isVerified = ($rStatus === 'verified' || !empty($rItem['is_confirmed']));
                            $isUnverified = ($rStatus === 'unverified');
                        @endphp
                        <div class="p-4 sm:p-5 rounded-2xl bg-white border {{ $isVerified ? 'border-emerald-200 bg-emerald-50/20' : ($isUnverified ? 'border-rose-200 bg-rose-50/20' : 'border-slate-200') }} shadow-2xs space-y-3">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <span class="text-[10px] font-black uppercase text-slate-400">Reference #{{ $rIdx + 1 }}</span>
                                @if($isVerified)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-black text-[10px] flex items-center gap-1 border border-emerald-200">
                                        <span>✓</span> Verified Reference
                                    </span>
                                @elseif($isUnverified)
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-black text-[10px] flex items-center gap-1 border border-rose-200">
                                        <span>✕</span> Unverified
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-black text-[10px] flex items-center gap-1 border border-amber-200">
                                        <span>⏳</span> Verification Requested
                                    </span>
                                @endif
                            </div>

                            <div>
                                <h4 class="font-black text-slate-900 text-sm m-0">{{ $rItem['name'] ?? 'Supervisor' }}</h4>
                                <p class="text-slate-600 font-semibold m-0 text-xs">{{ $rItem['organization'] ?? 'Company' }} @if(!empty($rItem['relationship'])) • {{ $rItem['relationship'] }} @endif</p>
                                @if(!empty($rItem['period']) || !empty($rItem['crew_role']))
                                    <p class="text-[11px] text-slate-500 m-0 mt-0.5">
                                        @if(!empty($rItem['crew_role'])) Role: <strong class="text-slate-700">{{ $rItem['crew_role'] }}</strong> @endif
                                        @if(!empty($rItem['period'])) ({{ $rItem['period'] }}) @endif
                                    </p>
                                @endif
                            </div>

                            @if($isVerified)
                                <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-200/80 text-[11px] space-y-1.5">
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-500 font-bold">Public Digital CV:</span>
                                        @if(!empty($rItem['show_on_cv']))
                                            <span class="text-emerald-700 font-black">🌐 Allowed on Profile</span>
                                        @else
                                            <span class="text-slate-600 font-bold">🔒 Admin Eyes Only</span>
                                        @endif
                                    </div>
                                    @if(!empty($rItem['rating']))
                                        <div class="flex justify-between items-center">
                                            <span class="text-slate-500 font-bold">Rating:</span>
                                            <span class="text-amber-500 font-black">{{ str_repeat('★', (int)$rItem['rating']) }} ({{ $rItem['rating'] }}/5)</span>
                                        </div>
                                    @endif
                                    @if(!empty($rItem['comments']))
                                        <div class="pt-1 border-t border-emerald-200/60">
                                            <span class="text-slate-500 font-bold block">Feedback:</span>
                                            <p class="italic text-slate-800 m-0">"{{ $rItem['comments'] }}"</p>
                                        </div>
                                    @endif
                                    @if(!empty($rItem['suggested_correction']))
                                        <div class="pt-1 border-t border-emerald-200/60 text-slate-700">
                                            <span class="font-bold text-slate-500">Suggested Correction:</span> {{ $rItem['suggested_correction'] }}
                                        </div>
                                    @endif
                                </div>
                            @elseif($isUnverified)
                                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-[11px] space-y-1">
                                    <span class="font-black text-rose-900 block">Recorded Reason:</span>
                                    <span class="font-bold text-rose-800 block">
                                        {{ ($rItem['unverified_reason'] ?? '') === 'name_removal_request' ? '2. Referee Name Removal Request (Does not wish to be included)' : '1. Employment Not Confirmed (Did not work with candidate/org)' }}
                                    </span>
                                    @if(!empty($rItem['unverified_notes']))
                                        <p class="text-slate-600 italic m-0 pt-0.5">Notes: "{{ $rItem['unverified_notes'] }}"</p>
                                    @endif
                                </div>
                            @endif

                            @if(!empty($rItem['phone']) || !empty($rItem['email']))
                                <div class="pt-2 border-t border-slate-100 flex flex-wrap gap-2 text-xs">
                                    @if(!empty($rItem['phone']))
                                        <a href="tel:{{ $rItem['phone'] }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-800 font-bold transition-colors text-decoration-none">
                                            <span>📞</span>
                                            <span>{{ $rItem['phone'] }}</span>
                                        </a>
                                    @endif
                                    @if(!empty($rItem['email']))
                                        <a href="mailto:{{ $rItem['email'] }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-800 font-bold transition-colors text-decoration-none">
                                            <span>✉️</span>
                                            <span>{{ $rItem['email'] }}</span>
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <!-- Admin Action Controls -->
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2">
                                <form method="POST" action="{{ route('admin.professionals.referees.toggleVerify', [$professional, $rIdx]) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-extrabold cursor-pointer {{ $isVerified ? 'bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs' }} transition-all flex items-center gap-1">
                                        @if($isVerified)
                                            <span>✕</span> <span>Revoke Verification</span>
                                        @else
                                            <span>✓</span> <span>Approve & Verify Referee</span>
                                        @endif
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.professionals.referees.toggleVisibility', [$professional, $rIdx]) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-extrabold cursor-pointer border transition-all flex items-center gap-1 {{ !empty($rItem['show_on_cv']) ? 'border-purple-300 text-purple-900 bg-purple-50 hover:bg-purple-100' : 'border-slate-300 text-slate-700 bg-white hover:bg-slate-100' }}">
                                        @if(!empty($rItem['show_on_cv']))
                                            <span>🌐</span> <span>Visible on CV (Click to Hide)</span>
                                        @else
                                            <span>🔒</span> <span>Hidden (Click to Show on CV)</span>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-500 italic text-center">
                    No professional referees submitted for verification yet.
                </div>
            @endif
        </div>

        <!-- Section 6: Standard Rates & Multi-Day Package Terms -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                Service Rates & Package Terms
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center text-xs">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-0.5">Hourly Rate</span>
                    <span class="font-black text-slate-900 text-base">
                        {{ $professional->hourly_rate ? ($professional->currency ?: 'KES') . ' ' . number_format($professional->hourly_rate) : 'N/A' }}
                    </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-0.5">Full Day Rate</span>
                    <span class="font-black text-slate-900 text-base">
                        {{ $professional->currency ?: 'KES' }} {{ number_format($professional->one_day_rate ?? ($professional->full_day_rate ?? 5000)) }}
                    </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-0.5">Rehearsal Option</span>
                    <span class="font-black text-slate-900 text-base">
                        {{ $professional->rehearsal_option ? 'Available' : 'None' }}
                    </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <span class="text-[10px] font-extrabold text-emerald-800 uppercase block mb-0.5">Multi-Day Discount</span>
                    <span class="font-black text-emerald-950 text-base">
                        {{ $professional->has_multi_day_discount ? ($professional->multi_day_discount_pct . '% Off') : 'None' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Section 7: Verification Attachments & Portfolio Gallery -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                Identity Verification Documents & Attachments
            </h3>

            <div class="grid sm:grid-cols-3 gap-4">
                @foreach([
                    ['Profile Headshot', $professional->profile_photo],
                    ['Resume / CV Document', $professional->resume],
                    ['Government ID / Passport', $professional->government_id],
                ] as $doc)
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-2">
                        <span class="block text-xs font-bold text-slate-700">{{ $doc[0] }}</span>
                        @if($doc[1])
                            @php
                                $url = get_storage_url($doc[1]);
                            @endphp
                            <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-amber-400 font-extrabold text-xs shadow-xs hover:bg-slate-800 transition-all text-decoration-none">
                                <span>📄 View Document</span>
                            </a>
                        @else
                            <span class="text-xs text-slate-400 font-medium italic block">Not Uploaded</span>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Gallery Portfolio Photos Grid -->
            @php
                $rawGallery = is_array($professional->gallery_photos) ? $professional->gallery_photos : [];
                $galleryPhotos = array_values(array_unique(array_filter($rawGallery)));
            @endphp
            @if(count($galleryPhotos) > 0)
                <div class="space-y-2 pt-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Uploaded Event Portfolio Photos</span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($galleryPhotos as $gPhoto)
                            @php $gUrl = get_storage_url($gPhoto); @endphp
                            <a href="{{ $gUrl }}" target="_blank" class="block relative h-32 rounded-2xl overflow-hidden border border-slate-200 group shadow-xs">
                                <img src="{{ $gUrl }}" alt="Event photo" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Section 8: Admin Status Update Bar -->
        <div class="pt-6 border-t border-slate-100">
            <form method="POST" action="{{ route('admin.professionals.status', $professional) }}" class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-slate-950 text-white shadow-md">
                @csrf
                @method('PATCH')
                <div class="flex items-center gap-2">
                    <span class="text-amber-400 font-black text-sm">🛡️ Admin Status Control:</span>
                    <span class="text-xs text-slate-400 font-medium">Update account verification state</span>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <select name="status" class="bg-slate-900 border border-slate-700 text-white text-xs font-extrabold rounded-xl px-4 py-2.5 outline-none focus:border-amber-400">
                        <option value="approved" @selected($professional->status==='approved')>Approved</option>
                        <option value="pending" @selected($professional->status==='pending')>Pending</option>
                        <option value="deactivated" @selected($professional->status==='deactivated' || $professional->status==='rejected')>Deactivated</option>
                        <option value="suspended" @selected($professional->status==='suspended')>Suspended</option>
                    </select>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-xs shrink-0">
                        Save Status Changes ⚡
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

<!-- Hidden Form for Staff Deletion -->
<form id="admin-delete-staff-show-form" method="POST" action="{{ route('admin.professionals.destroy', $professional) }}" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDeleteStaffFromShow(staffId, staffName) {
    Swal.fire({
        title: 'Delete Crew Member?',
        text: `Are you sure you want to permanently delete '${staffName}' from the database? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Delete Crew Member 🗑️'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('admin-delete-staff-show-form').submit();
        }
    });
}

let showClockHour = 10;
let showClockMinute = 0;
let showClockPeriod = 'AM';

function updateShowClockDisplay() {
    const hourStr = String(showClockHour).padStart(2, '0');
    const minuteStr = String(showClockMinute).padStart(2, '0');
    const formatted = `${hourStr}:${minuteStr} ${showClockPeriod}`;

    const elHour = document.getElementById('show-clock-hour');
    const elMin = document.getElementById('show-clock-minute');
    const elBadge = document.getElementById('show-clock-badge');
    const elInput = document.getElementById('show-input-interview-time');

    if (elHour) elHour.innerText = hourStr;
    if (elMin) elMin.innerText = minuteStr;
    if (elBadge) elBadge.innerText = formatted;
    if (elInput) elInput.value = formatted;

    const btnAm = document.getElementById('show-clock-btn-am');
    const btnPm = document.getElementById('show-clock-btn-pm');
    if (btnAm && btnPm) {
        if (showClockPeriod === 'AM') {
            btnAm.className = 'px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-amber-400 text-slate-950 shadow-sm cursor-pointer';
            btnPm.className = 'px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-slate-800 text-slate-400 hover:text-white cursor-pointer';
        } else {
            btnPm.className = 'px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-amber-400 text-slate-950 shadow-sm cursor-pointer';
            btnAm.className = 'px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-slate-800 text-slate-400 hover:text-white cursor-pointer';
        }
    }
}

function adjustShowClockHour(delta) {
    showClockHour += delta;
    if (showClockHour > 12) showClockHour = 1;
    if (showClockHour < 1) showClockHour = 12;
    updateShowClockDisplay();
}

function adjustShowClockMinute(delta) {
    showClockMinute += delta;
    if (showClockMinute >= 60) showClockMinute = 0;
    if (showClockMinute < 0) showClockMinute = 55;
    updateShowClockDisplay();
}

function setShowClockPeriod(period) {
    showClockPeriod = period;
    updateShowClockDisplay();
}

function setShowClockDirect(h, m, period) {
    showClockHour = h;
    showClockMinute = m;
    showClockPeriod = period;
    updateShowClockDisplay();
}

function parseAndSetShowClock(timeStr) {
    if (!timeStr) {
        setShowClockDirect(10, 0, 'AM');
        return;
    }
    timeStr = timeStr.trim();
    let period = 'AM';
    if (/pm/i.test(timeStr)) {
        period = 'PM';
    } else if (/am/i.test(timeStr)) {
        period = 'AM';
    }
    const match = timeStr.match(/(\d{1,2}):(\d{2})/);
    if (match) {
        let h = parseInt(match[1], 10);
        let m = parseInt(match[2], 10);
        if (h > 12) {
            h = h - 12;
            period = 'PM';
        } else if (h === 0) {
            h = 12;
        }
        setShowClockDirect(h, m, period);
    } else {
        setShowClockDirect(10, 0, 'AM');
    }
}

function toggleScheduleInterviewModal() {
    const modal = document.getElementById('modal-schedule-interview-show');
    if (modal) {
        modal.classList.toggle('hidden');
        if (!modal.classList.contains('hidden')) {
            const inputVal = document.getElementById('show-input-interview-time')?.value;
            parseAndSetShowClock(inputVal || '10:00 AM');
        }
    }
}

function toggleApproveModal() {
    const modal = document.getElementById('modal-approve-show');
    if (modal) modal.classList.toggle('hidden');
}

function toggleRejectModal() {
    const modal = document.getElementById('modal-reject-show');
    if (modal) modal.classList.toggle('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    parseAndSetShowClock('{{ addslashes($professional->interview_time ?: '10:00 AM') }}');
});
</script>

<!-- Modal 1: Schedule / Reschedule Interview -->
<div id="modal-schedule-interview-show" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span>🎙️ Schedule Vetting Interview</span>
            </h3>
            <button onclick="toggleScheduleInterviewModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black flex items-center justify-center">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.professionals.scheduleInterview', $professional) }}" class="space-y-4 text-xs font-bold">
            @csrf

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 text-xs">
                Scheduling interview for <strong class="text-slate-900">{{ $professional->full_name }}</strong>. An invitation email will be dispatched immediately.
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 mb-1">Interview Date *</label>
                    <input type="date" name="interview_date" required value="{{ old('interview_date', $professional->interview_date ? $professional->interview_date->format('Y-m-d') : date('Y-m-d', strtotime('+1 day'))) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Interviewer Name (Admin / Staff) *</label>
                    <input type="text" name="interviewer_name" required placeholder="e.g. Sarah Jenkins (Ops Lead)" value="{{ old('interviewer_name', $professional->interviewer_name ?: Auth::user()?->name) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                </div>
            </div>

            <!-- Alarm Clock Time Selector -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-slate-700 font-bold text-xs flex items-center gap-1.5">
                        <span>⏰ Interview Time (Alarm Clock) *</span>
                    </label>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] text-slate-400 font-bold">Selected:</span>
                        <span id="show-clock-badge" class="text-xs font-black text-amber-950 bg-amber-200/80 border border-amber-300 px-2.5 py-0.5 rounded-full font-mono">{{ $professional->interview_time ?: '10:00 AM' }}</span>
                    </div>
                </div>

                <!-- Hidden synced input that sends formatted time to backend -->
                <input type="hidden" id="show-input-interview-time" name="interview_time" value="{{ old('interview_time', $professional->interview_time ?: '10:00 AM') }}" required>

                <!-- Interactive Alarm Clock Widget -->
                <div class="bg-slate-950 text-white rounded-2xl p-3.5 sm:p-4 border border-slate-800 shadow-md space-y-3">
                    <div class="flex items-center justify-center gap-3 select-none">
                        <!-- Hour Unit -->
                        <div class="flex flex-col items-center">
                            <button type="button" onclick="adjustShowClockHour(1)" class="w-9 h-7 rounded-lg bg-slate-800 hover:bg-amber-400 hover:text-slate-950 text-slate-300 text-xs font-black transition-all flex items-center justify-center cursor-pointer active:scale-95" title="Hour Up">▲</button>
                            <div id="show-clock-hour" class="text-3xl sm:text-4xl font-black font-mono tracking-wider text-amber-400 py-1 px-3 bg-slate-900 rounded-xl my-1 border border-slate-800 min-w-[58px] text-center shadow-inner">10</div>
                            <button type="button" onclick="adjustShowClockHour(-1)" class="w-9 h-7 rounded-lg bg-slate-800 hover:bg-amber-400 hover:text-slate-950 text-slate-300 text-xs font-black transition-all flex items-center justify-center cursor-pointer active:scale-95" title="Hour Down">▼</button>
                            <span class="text-[9px] uppercase tracking-wider text-slate-400 font-extrabold mt-1">Hour</span>
                        </div>

                        <!-- Colon Separator -->
                        <div class="text-3xl sm:text-4xl font-black font-mono text-amber-400/80 mb-6 animate-pulse">:</div>

                        <!-- Minute Unit -->
                        <div class="flex flex-col items-center">
                            <button type="button" onclick="adjustShowClockMinute(5)" class="w-9 h-7 rounded-lg bg-slate-800 hover:bg-amber-400 hover:text-slate-950 text-slate-300 text-xs font-black transition-all flex items-center justify-center cursor-pointer active:scale-95" title="Minute Up (+5m)">▲</button>
                            <div id="show-clock-minute" class="text-3xl sm:text-4xl font-black font-mono tracking-wider text-amber-400 py-1 px-3 bg-slate-900 rounded-xl my-1 border border-slate-800 min-w-[58px] text-center shadow-inner">00</div>
                            <button type="button" onclick="adjustShowClockMinute(-5)" class="w-9 h-7 rounded-lg bg-slate-800 hover:bg-amber-400 hover:text-slate-950 text-slate-300 text-xs font-black transition-all flex items-center justify-center cursor-pointer active:scale-95" title="Minute Down (-5m)">▼</button>
                            <span class="text-[9px] uppercase tracking-wider text-slate-400 font-extrabold mt-1">Minute</span>
                        </div>

                        <!-- AM / PM Selector -->
                        <div class="flex flex-col gap-2 ml-2 mb-5">
                            <button type="button" id="show-clock-btn-am" onclick="setShowClockPeriod('AM')" class="px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-amber-400 text-slate-950 shadow-sm cursor-pointer">
                                AM
                            </button>
                            <button type="button" id="show-clock-btn-pm" onclick="setShowClockPeriod('PM')" class="px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-slate-800 text-slate-400 hover:text-white cursor-pointer">
                                PM
                            </button>
                        </div>
                    </div>

                    <!-- Alarm Quick Time Presets -->
                    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-center gap-1.5 flex-wrap">
                        <span class="text-[10px] text-slate-400 font-bold mr-1">⏰ Presets:</span>
                        <button type="button" onclick="setShowClockDirect(9, 0, 'AM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">09:00 AM</button>
                        <button type="button" onclick="setShowClockDirect(10, 0, 'AM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">10:00 AM</button>
                        <button type="button" onclick="setShowClockDirect(11, 30, 'AM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">11:30 AM</button>
                        <button type="button" onclick="setShowClockDirect(2, 0, 'PM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">02:00 PM</button>
                        <button type="button" onclick="setShowClockDirect(3, 30, 'PM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">03:30 PM</button>
                        <button type="button" onclick="setShowClockDirect(4, 0, 'PM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">04:00 PM</button>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Meeting Link or Location (Optional)</label>
                <input type="text" name="interview_link" placeholder="e.g. https://meet.google.com/xxx-xxxx or AfriCrew Office" value="{{ old('interview_link', $professional->interview_link) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Preparation Instructions / Notes</label>
                <textarea name="interview_notes" rows="2" placeholder="e.g. Please be ready 5 minutes early with your National ID ready for verification..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500">{{ old('interview_notes', $professional->interview_notes) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="toggleScheduleInterviewModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black shadow-md">Schedule & Send Email ✉️</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Approve Profile -->
<div id="modal-approve-show" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span>✓ Approve Crew Profile</span>
            </h3>
            <button onclick="toggleApproveModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black flex items-center justify-center">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.professionals.approve', $professional) }}" class="space-y-4 text-xs font-bold">
            @csrf

            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-900 text-xs">
                Approving <strong>{{ $professional->full_name }}</strong>. They will receive an approval email and will immediately be permitted to log in to the Crew Portal.
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Approval Notes (Optional)</label>
                <textarea name="interview_notes" rows="2" placeholder="e.g. Interview cleared satisfactorily, verified ID." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="toggleApproveModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md">Confirm Approval ✓</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Reject Profile -->
<div id="modal-reject-show" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span>✕ Reject Crew Profile</span>
            </h3>
            <button onclick="toggleRejectModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black flex items-center justify-center">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.professionals.reject', $professional) }}" class="space-y-4 text-xs font-bold">
            @csrf

            <div class="p-3 bg-rose-50 rounded-xl border border-rose-200 text-rose-900 text-xs">
                Rejecting application for <strong>{{ $professional->full_name }}</strong>. They will see "Profile Rejected" upon login and will be barred from portal access.
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Rejection Reason / Feedback (Included in email)</label>
                <textarea name="rejection_reason" rows="3" placeholder="e.g. Identity documents did not meet requirements, or interview evaluation was unsatisfactory." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="toggleRejectModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black shadow-md">Confirm Rejection ✕</button>
            </div>
        </form>
    </div>
</div>
@endsection
