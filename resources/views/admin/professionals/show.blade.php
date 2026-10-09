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

            <!-- Current Status Badge & Quick Toggle -->
            <div class="flex flex-col items-center sm:items-end gap-3">
                @php
                    $statusClasses = [
                        'pending' => 'bg-amber-100 text-amber-900 border-amber-300',
                        'approved' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                        'rejected' => 'bg-rose-100 text-rose-900 border-rose-300',
                        'deactivated' => 'bg-rose-100 text-rose-900 border-rose-300',
                        'suspended' => 'bg-slate-200 text-slate-900 border-slate-300',
                    ][$professional->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                @endphp
                <span class="px-4 py-1.5 rounded-full text-xs font-black border uppercase tracking-wider shadow-xs {{ $statusClasses }}">
                    {{ $professional->status === 'deactivated' ? '🚫 Deactivated' : ucfirst($professional->status) }}
                </span>

                @if($professional->status !== 'approved')
                    <button type="button" onclick="toggleApproveInterviewModal()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition-all flex items-center gap-1.5">
                        <span>✓ Schedule Interview & Approve Crew</span>
                    </button>
                @else
                    <form method="POST" action="{{ route('admin.professionals.status', $professional) }}" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="deactivated">
                        <button type="submit" onclick="return confirm('Deactivate this crew account?')" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs shadow-xs">
                            🚫 Deactivate Account
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if($professional->interview_date || $professional->interviewer_name)
            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 space-y-1 text-xs">
                <div class="font-extrabold text-slate-900 flex items-center gap-2">
                    <span>🎙️ Admin Vetting Interview Record</span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-900 font-black text-[10px] uppercase">
                        {{ ucfirst($professional->interview_status ?? 'completed') }}
                    </span>
                </div>
                <div class="text-slate-800 font-bold">
                    Interviewed on <strong>{{ $professional->interview_date ? $professional->interview_date->format('M j, Y') : 'N/A' }}</strong> at <strong>{{ $professional->interview_time }}</strong> by <strong>{{ $professional->interviewer_name }}</strong>.
                </div>
                @if($professional->interview_notes)
                    <div class="text-slate-600 font-medium italic mt-1">"{{ $professional->interview_notes }}"</div>
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

        <!-- Section 2: Biography, Highlight Quote & Social Media -->
        <div class="space-y-6 pt-4 border-t border-slate-100">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Biography & Summary</span>
                <p class="text-xs text-slate-700 font-medium leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200 whitespace-pre-line">
                    {{ $professional->about ?: 'No biography written.' }}
                </p>
            </div>

            @if(!empty($professional->highlight_quote))
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 italic text-xs font-bold">
                    "{{ $professional->highlight_quote }}"
                </div>
            @endif

            <!-- Social Links Grid -->
            @if(!empty($professional->social_links) && is_array($professional->social_links) && count(array_filter($professional->social_links)) > 0)
                <div class="space-y-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Social Media Profiles</span>
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        @if(!empty($professional->social_links['instagram']))
                            <a href="{{ Str::startsWith($professional->social_links['instagram'], 'http') ? $professional->social_links['instagram'] : 'https://instagram.com/' . ltrim($professional->social_links['instagram'], '@') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 font-extrabold hover:bg-slate-200 transition-colors">
                                📷 Instagram: {{ $professional->social_links['instagram'] }}
                            </a>
                        @endif
                        @if(!empty($professional->social_links['linkedin']))
                            <a href="{{ Str::startsWith($professional->social_links['linkedin'], 'http') ? $professional->social_links['linkedin'] : 'https://linkedin.com/in/' . $professional->social_links['linkedin'] }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 font-extrabold hover:bg-slate-200 transition-colors">
                                💼 LinkedIn: {{ $professional->social_links['linkedin'] }}
                            </a>
                        @endif
                        @if(!empty($professional->social_links['facebook']))
                            <a href="{{ Str::startsWith($professional->social_links['facebook'], 'http') ? $professional->social_links['facebook'] : 'https://facebook.com/' . $professional->social_links['facebook'] }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 font-extrabold hover:bg-slate-200 transition-colors">
                                📘 Facebook: {{ $professional->social_links['facebook'] }}
                            </a>
                        @endif
                        @if(!empty($professional->social_links['tiktok']))
                            <a href="{{ Str::startsWith($professional->social_links['tiktok'], 'http') ? $professional->social_links['tiktok'] : 'https://tiktok.com/@' . ltrim($professional->social_links['tiktok'], '@') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 font-extrabold hover:bg-slate-200 transition-colors">
                                🎵 TikTok / X: {{ $professional->social_links['tiktok'] }}
                            </a>
                        @endif
                    </div>
                </div>
            @endif

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
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
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
                        <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/80 space-y-1">
                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-900 block">
                                {{ !empty($eItem['degree']) ? $eItem['degree'] : ($eItem['field'] ?? 'Education') }}
                            </span>
                            <h4 class="font-black text-slate-900 text-sm">{{ $eItem['institution'] ?? 'Academic Institution' }}</h4>
                            @if($startEduDisp || $endEduDisp)
                                <span class="text-[11px] text-slate-500 font-medium block">📅 {{ $startEduDisp }} {{ $endEduDisp ? '- ' . $endEduDisp : '' }}</span>
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

        <!-- Section 5: Professional Referees & Verification Contact -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                Professional Referees & Contact References
            </h3>

            @if(!empty($professional->referees) && is_array($professional->referees) && count($professional->referees) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    @foreach($professional->referees as $rIdx => $ref)
                        @php $rItem = is_string($ref) ? json_decode($ref, true) : $ref; @endphp
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase text-slate-400">Reference #{{ $rIdx + 1 }}</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 font-extrabold text-[10px]">✓ Verified Reference</span>
                            </div>
                            <h4 class="font-black text-slate-900 text-sm">{{ $rItem['name'] ?? 'Supervisor' }}</h4>
                            <p class="text-slate-600 font-semibold">{{ $rItem['organization'] ?? 'Event Organization' }} • {{ $rItem['relationship'] ?? 'Supervisor' }}</p>
                            @if(!empty($rItem['phone']) || !empty($rItem['email']))
                                <div class="pt-1 text-[11px] text-amber-800 font-bold">
                                    @if(!empty($rItem['phone'])) <span>📞 {{ $rItem['phone'] }}</span> @endif
                                    @if(!empty($rItem['email'])) <span class="ml-2">✉️ {{ $rItem['email'] }}</span> @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-500 italic text-center">
                    No referees added yet.
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

function toggleApproveInterviewModal() {
    const modal = document.getElementById('modal-approve-interview');
    if (modal) modal.classList.toggle('hidden');
}
</script>

<!-- Modal: Interview & Approval Details -->
<div id="modal-approve-interview" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span>🎙️ Record Vetting Interview & Approve Crew</span>
            </h3>
            <button onclick="toggleApproveInterviewModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black flex items-center justify-center">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.professionals.status', $professional) }}" class="space-y-4 text-xs font-bold">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="approved">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 mb-1">Interview Date *</label>
                    <input type="date" name="interview_date" required value="{{ old('interview_date', date('Y-m-d')) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Interview Time *</label>
                    <input type="text" name="interview_time" required placeholder="e.g. 10:30 AM" value="{{ old('interview_time', '10:00 AM') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Interviewer Name (Admin / Staff) *</label>
                <input type="text" name="interviewer_name" required placeholder="e.g. Sarah Jenkins (Ops Lead)" value="{{ old('interviewer_name', Auth::user()?->name) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Interview Notes & Evaluation Remarks</label>
                <textarea name="interview_notes" rows="3" placeholder="Notes on communication skills, grooming, background check..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500">{{ old('interview_notes') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="toggleApproveInterviewModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md">Record Interview & Approve ⚡</button>
            </div>
        </form>
    </div>
</div>
@endsection
