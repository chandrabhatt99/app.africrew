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
                <span>👁️ View Public Frontend Profile</span>
            </a>
            <a href="{{ route('admin.professionals.edit', $professional->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-xs transition-all flex items-center gap-1.5 text-decoration-none">
                <span>✏️ Edit Profile & Skills</span>
            </a>
            <button type="button" onclick="confirmDeleteStaffFromShow('{{ $professional->id }}', '{{ addslashes($professional->full_name) }}')" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5">
                <span>🗑️ Delete Crew Account</span>
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
                    <img src="{{ $avatar }}" class="w-24 h-24 rounded-full object-cover border-4 border-amber-400 shadow-md shrink-0">
                @else
                    <div class="w-24 h-24 rounded-full bg-amber-500 text-slate-950 font-black text-3xl flex items-center justify-center border-4 border-amber-300 shadow-md shrink-0">
                        {{ strtoupper(substr($professional->full_name, 0, 1)) }}
                    </div>
                @endif

                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $professional->full_name }}</h1>
                    <div class="text-xs font-extrabold text-amber-700 uppercase tracking-wider mt-1">
                        {{ strtoupper($professional->category) }} • @ {{ $professional->username ?? Str::slug(strtok($professional->full_name, ' ')) }}
                    </div>
                    <div class="text-xs text-slate-500 font-semibold mt-1">
                        <span>📍 {{ $professional->city }}, {{ $professional->country }}</span>
                        <span class="mx-2">•</span>
                        <span>Registered {{ $professional->created_at ? $professional->created_at->format('M j, Y') : 'N/A' }}</span>
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

        <!-- Section 1: Contact & Basic Details Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Email Address</span>
                <span class="font-black text-slate-900 text-sm">{{ $professional->email }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Phone Number</span>
                <span class="font-black text-slate-900 text-sm">{{ $professional->phone }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Location</span>
                <span class="font-black text-slate-900 text-sm">{{ $professional->city }}, {{ $professional->country }}</span>
            </div>
        </div>

        <!-- Section 2: Key Skills & What I Can Help You With -->
        <div class="space-y-6 pt-4 border-t border-slate-100">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Biography & Summary</span>
                <p class="text-xs text-slate-700 font-medium leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200 whitespace-pre-line">
                    {{ $professional->about ?: 'No biography written.' }}
                </p>
            </div>

            <!-- Key Skills & What I Can Help You With Container (Matching Frontend View) -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl border border-slate-800">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4 flex-wrap gap-3">
                    <div>
                        <span class="text-[11px] font-black uppercase tracking-widest text-rose-400 block mb-0.5">CAPABILITIES & SERVICES</span>
                        <h3 class="text-xl font-black text-white tracking-tight flex items-center gap-2">
                            <span>Key Skills & What I Can Help You With</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30 text-[10px] uppercase font-bold">Frontend View Sync</span>
                        </h3>
                    </div>
                    <a href="{{ route('crew.show', $professional->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs shadow-md transition-all flex items-center gap-1.5 text-decoration-none">
                        <span>👁️ View Live Public Profile (http://127.0.0.1:8000/crew/{{ $professional->id }}) →</span>
                    </a>
                </div>

                <!-- Key Skills Badges -->
                <div class="space-y-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Key Skills</span>
                    @if(!empty($professional->skills))
                        @php
                            $skillsList = is_array($professional->skills) ? $professional->skills : array_map('trim', explode(',', $professional->skills));
                        @endphp
                        <div class="flex flex-wrap gap-2">
                            @foreach($skillsList as $sk)
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

                <!-- Capabilities / What I Can Help You With Cards Grid -->
                <div class="space-y-3 pt-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">What I Can Help You With (Services Cards)</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                                <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-amber-400/50 transition-all flex flex-col justify-between space-y-3 shadow-sm">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <div class="w-10 h-10 rounded-xl bg-slate-800 text-amber-400 flex items-center justify-center text-lg font-black shrink-0 border border-slate-700">
                                                {{ $iconDisplay }}
                                            </div>
                                            @if(!empty($taglineDisplay))
                                                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 font-extrabold text-[10px] uppercase">
                                                    {{ $taglineDisplay }}
                                                </span>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-black text-white leading-snug">{{ $titleDisplay }}</h4>
                                        <p class="text-xs text-slate-400 font-medium leading-relaxed">
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
                                <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-amber-400/50 transition-all flex flex-col justify-between space-y-3 shadow-sm">
                                    <div class="space-y-2">
                                        <div class="w-10 h-10 rounded-xl bg-slate-800 text-amber-400 flex items-center justify-center text-lg font-black shrink-0 border border-slate-700">
                                            {{ $idx % 2 === 0 ? '👥' : '👑' }}
                                        </div>
                                        <h4 class="text-sm font-black text-white">{{ $sk }}</h4>
                                        <p class="text-xs text-slate-400 font-medium leading-relaxed">
                                            Professional {{ strtolower($sk) }} services tailored for corporate galas, VIP guest reception, and stage management.
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-5 rounded-2xl bg-slate-900/50 border border-slate-800 text-center space-y-2 col-span-full">
                                <span class="text-2xl block">⚡</span>
                                <h4 class="text-sm font-black text-white">No Services or Capabilities Added</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto">No specific services listed yet. Click "Edit Profile & Skills" to configure custom service cards for this professional.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Standard Rates & Labour Packages -->
        <div class="space-y-3 pt-2">
            <h3 class="text-sm font-black text-slate-900">Standard Rates & Charges</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center text-xs">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-0.5">Hourly</span>
                    <span class="font-black text-slate-900 text-base">
                        {{ $professional->hourly_rate ? '$'.number_format($professional->hourly_rate, 2) : 'N/A' }}
                    </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-0.5">1 Day Event</span>
                    <span class="font-black text-slate-900 text-base">
                        KES {{ number_format($professional->one_day_rate ?? 5000, 2) }}
                    </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-0.5">2 Day Package</span>
                    <span class="font-black text-slate-900 text-base">
                        KES {{ number_format($professional->two_day_rate ?? 9500, 2) }}
                    </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200">
                    <span class="text-[10px] font-extrabold text-amber-800 uppercase block mb-0.5">Rehearsal</span>
                    <span class="font-black text-amber-950 text-base">
                        KES {{ number_format($professional->rehearsal_rate ?? 2500, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Section 4: Verification Attachments -->
        <div class="space-y-3 pt-4 border-t border-slate-100">
            <h3 class="text-sm font-black text-slate-900">Verification Files & Identity Attachments</h3>
            <div class="grid sm:grid-cols-3 gap-4">
                @foreach([
                    ['Profile Headshot', $professional->profile_photo],
                    ['Resume / CV', $professional->resume],
                    ['Government ID / Passport', $professional->government_id],
                ] as $doc)
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-2">
                        <span class="block text-xs font-bold text-slate-700">{{ $doc[0] }}</span>
                        @if($doc[1])
                            @php
                                $url = get_storage_url($doc[1]);
                            @endphp
                            <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-amber-400 font-extrabold text-xs shadow-xs hover:bg-slate-800 transition-all">
                                <span>📄 View Document</span>
                            </a>
                        @else
                            <span class="text-xs text-slate-400 font-medium italic block">Not Uploaded</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section 5: Admin Status Update Bar -->
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
