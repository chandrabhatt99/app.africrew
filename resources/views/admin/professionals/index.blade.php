@extends('admin.layout')

@section('content')
<div class="space-y-6 pb-12">

    <!-- Page Header & Onboard CTA -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Registered Crew & Talent Directory</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage registered crew members, schedule vetting interviews, and approve or reject profiles.</p>
        </div>
        
        <a href="{{ route('admin.professionals.create') }}" class="px-5 py-3 rounded-2xl bg-slate-900 text-amber-400 font-extrabold text-xs shadow-md hover:bg-slate-800 transition-all flex items-center justify-center gap-2 w-full sm:w-auto text-decoration-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span>+ Onboard New Crew</span>
        </a>
    </div>

    <!-- Metric Filter Cards Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <a href="{{ route('admin.professionals.index', ['status' => 'all']) }}" class="p-3.5 rounded-2xl bg-white border {{ ($status=='all' || !$status) ? 'border-slate-900 ring-2 ring-slate-900/10' : 'border-slate-200' }} shadow-xs hover:border-amber-400 transition-all text-decoration-none">
            <span class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider block">Total Registered</span>
            <span class="text-2xl font-black text-slate-900 mt-1 block">{{ number_format($counts['total']) }}</span>
        </a>
        <a href="{{ route('admin.professionals.index', ['status' => 'pending']) }}" class="p-3.5 rounded-2xl bg-amber-50 border {{ $status=='pending' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-amber-200' }} shadow-xs hover:border-amber-400 transition-all text-decoration-none">
            <span class="text-[10px] uppercase font-extrabold text-amber-800 tracking-wider block">⏳ Pending Review</span>
            <span class="text-2xl font-black text-amber-950 mt-1 block">{{ number_format($counts['pending']) }}</span>
        </a>
        <a href="{{ route('admin.professionals.index', ['status' => 'interview_scheduled']) }}" class="p-3.5 rounded-2xl bg-indigo-50 border {{ $status=='interview_scheduled' ? 'border-indigo-500 ring-2 ring-indigo-500/20' : 'border-indigo-200' }} shadow-xs hover:border-indigo-400 transition-all text-decoration-none">
            <span class="text-[10px] uppercase font-extrabold text-indigo-800 tracking-wider block">🎙️ Interview Scheduled</span>
            <span class="text-2xl font-black text-indigo-950 mt-1 block">{{ number_format($counts['interview_scheduled']) }}</span>
        </a>
        <a href="{{ route('admin.professionals.index', ['status' => 'approved']) }}" class="p-3.5 rounded-2xl bg-emerald-50 border {{ $status=='approved' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-emerald-200' }} shadow-xs hover:border-emerald-400 transition-all text-decoration-none">
            <span class="text-[10px] uppercase font-extrabold text-emerald-700 tracking-wider block">✓ Approved Crew</span>
            <span class="text-2xl font-black text-emerald-950 mt-1 block">{{ number_format($counts['approved']) }}</span>
        </a>
        <a href="{{ route('admin.professionals.index', ['status' => 'deactivated']) }}" class="p-3.5 rounded-2xl bg-rose-50 border {{ ($status=='deactivated' || $status=='rejected') ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-rose-200' }} shadow-xs hover:border-rose-400 transition-all text-decoration-none">
            <span class="text-[10px] uppercase font-extrabold text-rose-800 tracking-wider block">🚫 Rejected / Inactive</span>
            <span class="text-2xl font-black text-rose-950 mt-1 block">{{ number_format($counts['deactivated']) }}</span>
        </a>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.professionals.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
            <div class="relative flex-grow w-full">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, email, phone, city, category, interviewer..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-4 py-2.5 text-xs font-semibold text-slate-900 outline-none focus:border-amber-500">
                <span class="absolute left-3.5 top-3 text-slate-400">🔍</span>
            </div>

            <select name="status" onchange="this.form.submit()" class="w-full sm:w-56 bg-slate-50 border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-slate-900 outline-none focus:border-amber-500">
                <option value="all" @selected($status=='all' || !$status)>All Statuses</option>
                <option value="interview_scheduled" @selected($status=='interview_scheduled')>🎙️ Interview Scheduled</option>
                <option value="pending" @selected($status=='pending')>⏳ Pending Review</option>
                <option value="approved" @selected($status=='approved')>✓ Approved</option>
                <option value="rejected" @selected($status=='rejected')>✕ Rejected</option>
                <option value="deactivated" @selected($status=='deactivated')>🚫 Deactivated</option>
                <option value="suspended" @selected($status=='suspended')>⚠️ Suspended</option>
            </select>

            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-slate-900 text-amber-400 font-extrabold text-xs shrink-0 w-full sm:w-auto">
                Filter Results
            </button>
        </form>
    </div>

    <!-- Staff Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[900px]">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold text-[11px] uppercase tracking-wider border-b border-slate-200/80">
                        <th class="py-4 px-5">Crew Member</th>
                        <th class="py-4 px-5">Specialty</th>
                        <th class="py-4 px-5">Contact & Location</th>
                        <th class="py-4 px-5">Interview Schedule</th>
                        <th class="py-4 px-5">Profile Status</th>
                        <th class="py-4 px-5 text-right">Admin Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($professionals as $p)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                @php
                                    $avatar = get_storage_url($p->profile_photo);
                                @endphp
                                @if($avatar)
                                    <img src="{{ $avatar }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-amber-500/20 text-slate-950 font-black flex items-center justify-center text-sm border border-amber-300 shrink-0">
                                        {{ strtoupper(substr($p->full_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('admin.professionals.show', $p) }}" class="font-extrabold text-slate-900 hover:text-amber-600 transition-colors block text-decoration-none">
                                        {{ $p->full_name }}
                                    </a>
                                    <span class="text-[11px] text-slate-400 font-semibold">Registered {{ $p->created_at ? $p->created_at->format('M j, Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <span class="inline-flex px-3 py-1 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold">
                                {{ $p->category }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-xs text-slate-600">
                            <div class="font-bold text-slate-900">{{ $p->phone }}</div>
                            <div class="text-[11px] text-slate-400">{{ $p->email }}</div>
                            <div class="text-[10px] font-bold text-slate-500 mt-0.5">📍 {{ $p->city }}, {{ $p->country }}</div>
                        </td>
                        
                        <!-- Interview Schedule Status Column -->
                        <td class="py-4 px-5">
                            @if($p->interview_status === 'scheduled')
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-[11px] font-black">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                        <span>🎙️ Scheduled</span>
                                    </span>
                                    <div class="text-xs font-black text-slate-900">
                                        {{ $p->interview_date ? $p->interview_date->format('M j, Y') : 'Date set' }} @ {{ $p->interview_time }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-semibold truncate max-w-[170px]" title="{{ $p->interviewer_name }}">
                                        👤 {{ $p->interviewer_name ?: 'AfriCrew Team' }}
                                    </div>
                                </div>
                            @elseif($p->interview_status === 'completed')
                                <div class="space-y-0.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300 text-[10px] font-bold">
                                        ✓ Interview Done
                                    </span>
                                    @if($p->interview_date)
                                        <div class="text-[10px] text-slate-500 font-medium">{{ $p->interview_date->format('M j, Y') }}</div>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200 text-[11px] font-bold">
                                    ⏳ Not Scheduled
                                </span>
                            @endif
                        </td>

                        <!-- Account Status Column -->
                        <td class="py-4 px-5">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-100 text-amber-900 border-amber-300',
                                    'approved' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                                    'rejected' => 'bg-rose-100 text-rose-900 border-rose-300',
                                    'deactivated' => 'bg-rose-100 text-rose-900 border-rose-300',
                                    'suspended' => 'bg-slate-200 text-slate-900 border-slate-300',
                                ][$p->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-black border uppercase tracking-wider {{ $statusClasses }}">
                                @if($p->status === 'approved')
                                    ✓ Approved
                                @elseif($p->status === 'rejected')
                                    ✕ Rejected
                                @elseif($p->status === 'deactivated')
                                    🚫 Deactivated
                                @else
                                    {{ ucfirst($p->status) }}
                                @endif
                            </span>
                        </td>

                        <!-- Admin Action Controls -->
                        <td class="py-4 px-5 text-right">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                <!-- Review Profile Link -->
                                <a href="{{ route('admin.professionals.show', $p) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-xs shadow-xs text-decoration-none" title="View Full Details">
                                    Review
                                </a>

                                <!-- Schedule / Reschedule Interview Button -->
                                <button type="button" 
                                    onclick="openScheduleInterviewModal('{{ $p->id }}', '{{ addslashes($p->full_name) }}', '{{ $p->interview_date ? $p->interview_date->format('Y-m-d') : '' }}', '{{ addslashes($p->interview_time ?? '') }}', '{{ addslashes($p->interviewer_name ?? '') }}', '{{ addslashes($p->interview_link ?? '') }}', '{{ addslashes($p->interview_notes ?? '') }}')"
                                    class="px-2.5 py-1.5 rounded-xl {{ $p->interview_status === 'scheduled' ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-amber-500 hover:bg-amber-600 text-slate-950' }} font-bold text-xs shadow-xs"
                                    title="{{ $p->interview_status === 'scheduled' ? 'Reschedule Interview' : 'Schedule Interview' }}">
                                    🎙️ {{ $p->interview_status === 'scheduled' ? 'Reschedule' : 'Interview' }}
                                </button>

                                <!-- Approve Button -->
                                @if($p->status !== 'approved')
                                    <button type="button" onclick="openApproveModal('{{ $p->id }}', '{{ addslashes($p->full_name) }}')" class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs" title="Approve Profile">
                                        ✓ Approve
                                    </button>
                                @endif

                                <!-- Reject Button -->
                                @if($p->status !== 'rejected')
                                    <button type="button" onclick="openRejectModal('{{ $p->id }}', '{{ addslashes($p->full_name) }}')" class="px-2.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs" title="Reject Profile">
                                        ✕ Reject
                                    </button>
                                @endif

                                <!-- Delete Staff Action -->
                                <button type="button" onclick="confirmDeleteStaff('{{ $p->id }}', '{{ addslashes($p->full_name) }}')" class="p-1.5 rounded-xl bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-600 font-bold text-xs transition-colors" title="Delete Crew Account">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 text-xs italic">No registered crew records match your criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($professionals->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $professionals->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Hidden Form for Staff Deletion -->
<form id="admin-delete-staff-form" method="POST" action="" class="hidden">
    @csrf
    @method('DELETE')
</form>

<!-- Modal 1: Schedule Interview -->
<div id="modal-schedule-interview" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span>🎙️ Schedule Vetting Interview</span>
            </h3>
            <button onclick="closeScheduleInterviewModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black flex items-center justify-center">✕</button>
        </div>

        <form id="form-schedule-interview" method="POST" action="" class="space-y-4 text-xs font-bold">
            @csrf

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 text-xs">
                Scheduling interview for <strong id="schedule-modal-staff-name" class="text-slate-900">Crew Member</strong>. An email invitation will automatically be sent to the candidate with these details!
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 mb-1">Interview Date *</label>
                    <input type="date" id="input-interview-date" name="interview_date" required value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Interviewer Name (Admin / Staff) *</label>
                    <input type="text" id="input-interviewer-name" name="interviewer_name" required placeholder="e.g. Sarah Jenkins (Ops Lead)" value="{{ Auth::user()?->name }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold outline-none focus:border-amber-500">
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
                        <span id="index-clock-badge" class="text-xs font-black text-amber-950 bg-amber-200/80 border border-amber-300 px-2.5 py-0.5 rounded-full font-mono">10:00 AM</span>
                    </div>
                </div>

                <!-- Hidden synced input that sends formatted time to backend -->
                <input type="hidden" id="input-interview-time" name="interview_time" value="10:00 AM" required>

                <!-- Interactive Alarm Clock Widget -->
                <div class="bg-slate-950 text-white rounded-2xl p-3.5 sm:p-4 border border-slate-800 shadow-md space-y-3">
                    <div class="flex items-center justify-center gap-3 select-none">
                        <!-- Hour Unit -->
                        <div class="flex flex-col items-center">
                            <button type="button" onclick="adjustIndexClockHour(1)" class="w-9 h-7 rounded-lg bg-slate-800 hover:bg-amber-400 hover:text-slate-950 text-slate-300 text-xs font-black transition-all flex items-center justify-center cursor-pointer active:scale-95" title="Hour Up">▲</button>
                            <div id="index-clock-hour" class="text-3xl sm:text-4xl font-black font-mono tracking-wider text-amber-400 py-1 px-3 bg-slate-900 rounded-xl my-1 border border-slate-800 min-w-[58px] text-center shadow-inner">10</div>
                            <button type="button" onclick="adjustIndexClockHour(-1)" class="w-9 h-7 rounded-lg bg-slate-800 hover:bg-amber-400 hover:text-slate-950 text-slate-300 text-xs font-black transition-all flex items-center justify-center cursor-pointer active:scale-95" title="Hour Down">▼</button>
                            <span class="text-[9px] uppercase tracking-wider text-slate-400 font-extrabold mt-1">Hour</span>
                        </div>

                        <!-- Colon Separator -->
                        <div class="text-3xl sm:text-4xl font-black font-mono text-amber-400/80 mb-6 animate-pulse">:</div>

                        <!-- Minute Unit -->
                        <div class="flex flex-col items-center">
                            <button type="button" onclick="adjustIndexClockMinute(5)" class="w-9 h-7 rounded-lg bg-slate-800 hover:bg-amber-400 hover:text-slate-950 text-slate-300 text-xs font-black transition-all flex items-center justify-center cursor-pointer active:scale-95" title="Minute Up (+5m)">▲</button>
                            <div id="index-clock-minute" class="text-3xl sm:text-4xl font-black font-mono tracking-wider text-amber-400 py-1 px-3 bg-slate-900 rounded-xl my-1 border border-slate-800 min-w-[58px] text-center shadow-inner">00</div>
                            <button type="button" onclick="adjustIndexClockMinute(-5)" class="w-9 h-7 rounded-lg bg-slate-800 hover:bg-amber-400 hover:text-slate-950 text-slate-300 text-xs font-black transition-all flex items-center justify-center cursor-pointer active:scale-95" title="Minute Down (-5m)">▼</button>
                            <span class="text-[9px] uppercase tracking-wider text-slate-400 font-extrabold mt-1">Minute</span>
                        </div>

                        <!-- AM / PM Selector -->
                        <div class="flex flex-col gap-2 ml-2 mb-5">
                            <button type="button" id="index-clock-btn-am" onclick="setIndexClockPeriod('AM')" class="px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-amber-400 text-slate-950 shadow-sm cursor-pointer">
                                AM
                            </button>
                            <button type="button" id="index-clock-btn-pm" onclick="setIndexClockPeriod('PM')" class="px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-slate-800 text-slate-400 hover:text-white cursor-pointer">
                                PM
                            </button>
                        </div>
                    </div>

                    <!-- Alarm Quick Time Presets -->
                    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-center gap-1.5 flex-wrap">
                        <span class="text-[10px] text-slate-400 font-bold mr-1">⏰ Presets:</span>
                        <button type="button" onclick="setIndexClockDirect(9, 0, 'AM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">09:00 AM</button>
                        <button type="button" onclick="setIndexClockDirect(10, 0, 'AM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">10:00 AM</button>
                        <button type="button" onclick="setIndexClockDirect(11, 30, 'AM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">11:30 AM</button>
                        <button type="button" onclick="setIndexClockDirect(2, 0, 'PM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">02:00 PM</button>
                        <button type="button" onclick="setIndexClockDirect(3, 30, 'PM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">03:30 PM</button>
                        <button type="button" onclick="setIndexClockDirect(4, 0, 'PM')" class="px-2 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-200 transition-colors">04:00 PM</button>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Meeting Link or Location (Optional)</label>
                <input type="text" id="input-interview-link" name="interview_link" placeholder="e.g. https://meet.google.com/xxx-xxxx or AfriCrew Nairobi Office" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Preparation Instructions / Notes</label>
                <textarea id="input-interview-notes" name="interview_notes" rows="2" placeholder="e.g. Please be ready 5 minutes early with your National ID ready for verification..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeScheduleInterviewModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black shadow-md">Schedule & Send Email ✉️</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Approve Crew Profile -->
<div id="modal-approve-crew" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span>✓ Approve Crew Profile</span>
            </h3>
            <button onclick="closeApproveModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black flex items-center justify-center">✕</button>
        </div>

        <form id="form-approve-crew" method="POST" action="" class="space-y-4 text-xs font-bold">
            @csrf

            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-900 text-xs">
                Are you sure you want to approve <strong id="approve-modal-staff-name" class="text-slate-900">Crew Member</strong>?
                They will receive an approval email and will immediately be allowed to log in to the Crew Portal.
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Approval Notes (Optional)</label>
                <textarea name="interview_notes" rows="2" placeholder="e.g. Excellent interview evaluation, verified ID, approved for ushering roster." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md">Confirm Approval ✓</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Reject Crew Profile -->
<div id="modal-reject-crew" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span>✕ Reject Crew Profile</span>
            </h3>
            <button onclick="closeRejectModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black flex items-center justify-center">✕</button>
        </div>

        <form id="form-reject-crew" method="POST" action="" class="space-y-4 text-xs font-bold">
            @csrf

            <div class="p-3 bg-rose-50 rounded-xl border border-rose-200 text-rose-900 text-xs">
                Rejecting application for <strong id="reject-modal-staff-name" class="text-slate-900">Crew Member</strong>.
                They will see a "Profile Rejected" notice upon logging in and will be barred from portal access.
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Rejection Reason / Feedback (Included in email)</label>
                <textarea name="rejection_reason" rows="3" placeholder="e.g. Identity documents did not meet requirements, or interview evaluation was unsatisfactory." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black shadow-md">Confirm Rejection ✕</button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmDeleteStaff(staffId, staffName) {
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
            const form = document.getElementById('admin-delete-staff-form');
            form.action = `/admin/professionals/${staffId}`;
            form.submit();
        }
    });
}

let indexClockHour = 10;
let indexClockMinute = 0;
let indexClockPeriod = 'AM';

function updateIndexClockDisplay() {
    const hourStr = String(indexClockHour).padStart(2, '0');
    const minuteStr = String(indexClockMinute).padStart(2, '0');
    const formatted = `${hourStr}:${minuteStr} ${indexClockPeriod}`;

    const elHour = document.getElementById('index-clock-hour');
    const elMin = document.getElementById('index-clock-minute');
    const elBadge = document.getElementById('index-clock-badge');
    const elInput = document.getElementById('input-interview-time');

    if (elHour) elHour.innerText = hourStr;
    if (elMin) elMin.innerText = minuteStr;
    if (elBadge) elBadge.innerText = formatted;
    if (elInput) elInput.value = formatted;

    const btnAm = document.getElementById('index-clock-btn-am');
    const btnPm = document.getElementById('index-clock-btn-pm');
    if (btnAm && btnPm) {
        if (indexClockPeriod === 'AM') {
            btnAm.className = 'px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-amber-400 text-slate-950 shadow-sm cursor-pointer';
            btnPm.className = 'px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-slate-800 text-slate-400 hover:text-white cursor-pointer';
        } else {
            btnPm.className = 'px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-amber-400 text-slate-950 shadow-sm cursor-pointer';
            btnAm.className = 'px-3 py-1.5 rounded-lg text-xs font-black transition-all bg-slate-800 text-slate-400 hover:text-white cursor-pointer';
        }
    }
}

function adjustIndexClockHour(delta) {
    indexClockHour += delta;
    if (indexClockHour > 12) indexClockHour = 1;
    if (indexClockHour < 1) indexClockHour = 12;
    updateIndexClockDisplay();
}

function adjustIndexClockMinute(delta) {
    indexClockMinute += delta;
    if (indexClockMinute >= 60) indexClockMinute = 0;
    if (indexClockMinute < 0) indexClockMinute = 55;
    updateIndexClockDisplay();
}

function setIndexClockPeriod(period) {
    indexClockPeriod = period;
    updateIndexClockDisplay();
}

function setIndexClockDirect(h, m, period) {
    indexClockHour = h;
    indexClockMinute = m;
    indexClockPeriod = period;
    updateIndexClockDisplay();
}

function parseAndSetIndexClock(timeStr) {
    if (!timeStr) {
        setIndexClockDirect(10, 0, 'AM');
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
        setIndexClockDirect(h, m, period);
    } else {
        setIndexClockDirect(10, 0, 'AM');
    }
}

function openScheduleInterviewModal(staffId, staffName, date, time, interviewer, link, notes) {
    const form = document.getElementById('form-schedule-interview');
    form.action = `/admin/professionals/${staffId}/schedule-interview`;
    document.getElementById('schedule-modal-staff-name').innerText = staffName;
    if (date) document.getElementById('input-interview-date').value = date;
    parseAndSetIndexClock(time || '10:00 AM');
    if (interviewer) document.getElementById('input-interviewer-name').value = interviewer;
    if (link) document.getElementById('input-interview-link').value = link;
    if (notes) document.getElementById('input-interview-notes').value = notes;
    document.getElementById('modal-schedule-interview').classList.remove('hidden');
}

function closeScheduleInterviewModal() {
    document.getElementById('modal-schedule-interview').classList.add('hidden');
}

function openApproveModal(staffId, staffName) {
    const form = document.getElementById('form-approve-crew');
    form.action = `/admin/professionals/${staffId}/approve`;
    document.getElementById('approve-modal-staff-name').innerText = staffName;
    document.getElementById('modal-approve-crew').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('modal-approve-crew').classList.add('hidden');
}

function openRejectModal(staffId, staffName) {
    const form = document.getElementById('form-reject-crew');
    form.action = `/admin/professionals/${staffId}/reject`;
    document.getElementById('reject-modal-staff-name').innerText = staffName;
    document.getElementById('modal-reject-crew').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('modal-reject-crew').classList.add('hidden');
}
</script>
@endsection
