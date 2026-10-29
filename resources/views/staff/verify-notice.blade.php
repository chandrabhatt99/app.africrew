@extends('layouts.app')

@section('content')
@php
    $name = session('submitted_name', $professional?->full_name ?? 'Applicant');
    $email = session('submitted_email', session('pending_email', $professional?->email ?? 'your email'));
    $category = session('submitted_category', $professional?->category ?? 'Event Talent');
    $refId = session('ref_id', $professional ? ('AFR-CRW-' . str_pad($professional->id, 5, '0', STR_PAD_LEFT)) : 'AFR-CRW-PENDING');

    $isRejected = ($professional?->status === 'rejected') || session('status_rejected');
    $isApproved = ($professional?->status === 'approved') || session('status_approved');
    $isInterviewScheduled = ($professional?->interview_status === 'scheduled') || session('status_interview_scheduled') || session('interview_scheduled');

    $interviewDate = $professional?->interview_date ? $professional->interview_date->format('l, F j, Y') : session('interview_date', 'Upcoming Date');
    $interviewTime = $professional?->interview_time ?? session('interview_time', '10:00 AM');
    $interviewerName = $professional?->interviewer_name ?? session('interviewer_name', 'AfriCrew Vetting Team');
    $interviewLink = $professional?->interview_link ?? session('interview_link');
    $interviewNotes = $professional?->interview_notes ?? session('interview_notes');
@endphp

<section class="min-h-[calc(100vh-140px)] flex items-center justify-center py-12 px-4 sm:px-6 bg-slate-50 relative overflow-hidden">
    <!-- Ambient Background Glows -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-pink-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-xl w-full bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-10 shadow-xl relative z-10 text-center">

        @if($isRejected)
            <!-- REJECTED STATE -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-800 text-xs font-black uppercase tracking-wider mb-6">
                <span>✕ Application Not Approved</span>
            </div>

            <div class="w-20 h-20 rounded-3xl bg-rose-50 border-2 border-rose-200 text-rose-600 flex items-center justify-center mx-auto mb-5 shadow-sm text-3xl">
                ❌
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">Profile Not Approved</h1>
            
            <p class="text-slate-600 text-sm leading-relaxed mt-3 max-w-md mx-auto">
                Thank you for your interest in AfriCrew, <strong class="text-slate-900">{{ $name }}</strong>. We regret to inform you that your application and profile have not been approved at this time following our vetting evaluation.
            </p>

            @if($interviewNotes)
                <div class="mt-5 p-4 rounded-2xl bg-rose-50/80 border border-rose-200 text-left text-xs space-y-1">
                    <span class="text-rose-900 font-extrabold uppercase text-[10px] tracking-wider block">Evaluation Feedback:</span>
                    <p class="text-rose-950 italic">"{{ $interviewNotes }}"</p>
                </div>
            @endif

            <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-left text-xs text-slate-600">
                If you have questions regarding this decision, feel free to contact our administrative team at <a href="mailto:mail@africrew.ke" class="text-slate-900 font-bold hover:underline">mail@africrew.ke</a>.
            </div>

            <div class="mt-7 flex items-center justify-center">
                <a href="{{ route('home') }}" class="px-7 py-3 rounded-full bg-slate-950 hover:bg-slate-900 text-white font-extrabold text-xs transition-all shadow-md text-decoration-none">
                    ← Return to Home
                </a>
            </div>

        @elseif($isApproved)
            <!-- APPROVED STATE -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 text-xs font-black uppercase tracking-wider mb-6">
                <span>✓ Account Approved</span>
            </div>

            <div class="w-20 h-20 rounded-3xl bg-emerald-50 border-2 border-emerald-200 text-emerald-600 flex items-center justify-center mx-auto mb-5 shadow-sm text-3xl">
                🎉
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">Congratulations, {{ $name }}!</h1>
            
            <p class="text-slate-600 text-sm leading-relaxed mt-3 max-w-md mx-auto">
                Your profile has been fully vetted and <strong class="text-emerald-700">APPROVED</strong> by the AfriCrew administration! You can now log in to access your portal, view bookings, and start receiving gigs.
            </p>

            <div class="mt-7 flex items-center justify-center">
                <a href="{{ route('staff.login') }}" class="px-8 py-3.5 rounded-full bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 hover:opacity-95 text-white font-black text-sm transition-all shadow-lg shadow-pink-500/25 text-decoration-none">
                    Log In to Crew Portal Now →
                </a>
            </div>

        @elseif($isInterviewScheduled)
            <!-- INTERVIEW SCHEDULED STATE -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-900 text-xs font-black uppercase tracking-wider mb-6">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse"></span>
                <span>🎙️ Interview Scheduled</span>
            </div>

            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-indigo-500/20 via-amber-500/20 to-orange-500/20 border-2 border-indigo-500/30 text-indigo-700 flex items-center justify-center mx-auto mb-5 shadow-sm text-3xl">
                📅
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">Interview Scheduled!</h1>
            
            <p class="text-slate-600 text-sm leading-relaxed mt-3 max-w-md mx-auto">
                Great news, <strong class="text-slate-900">{{ $name }}</strong>! Your application has been reviewed, and our team has scheduled your official vetting interview:
            </p>

            <!-- Interview Schedule Box -->
            <div class="mt-6 bg-gradient-to-br from-slate-50 to-amber-50/40 border-2 border-amber-300/80 rounded-2xl p-5 text-left text-xs space-y-3 shadow-xs">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[11px]">📅 Interview Date</span>
                    <strong class="font-black text-slate-950 text-sm">{{ $interviewDate }}</strong>
                </div>
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[11px]">⏰ Interview Time</span>
                    <strong class="font-black text-slate-950 text-sm">{{ $interviewTime }}</strong>
                </div>
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[11px]">👤 Interviewer</span>
                    <span class="font-extrabold text-slate-900">{{ $interviewerName }}</span>
                </div>
                @if($interviewLink)
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-200">
                        <span class="text-slate-500 font-bold uppercase tracking-wider text-[11px]">🔗 Meeting Link / Desk</span>
                        @if(Str::startsWith($interviewLink, ['http://', 'https://']))
                            <a href="{{ $interviewLink }}" target="_blank" class="font-black text-amber-700 hover:underline">Join Meeting Online ↗</a>
                        @else
                            <span class="font-bold text-slate-900">{{ $interviewLink }}</span>
                        @endif
                    </div>
                @endif
                @if($interviewNotes)
                    <div class="pt-1">
                        <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px] block mb-1">📝 Instructions & Notes</span>
                        <p class="text-slate-700 leading-relaxed font-medium bg-white p-3 rounded-xl border border-slate-200">{{ $interviewNotes }}</p>
                    </div>
                @endif
            </div>

            <!-- 3-Step Vetting Timeline -->
            <div class="mt-7 text-left">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 mb-3 text-center sm:text-left">Interview Process Progress</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/80">
                        <div class="w-6 h-6 rounded-full bg-emerald-500 text-white font-black text-xs flex items-center justify-center shrink-0 mt-0.5">✓</div>
                        <div class="text-xs">
                            <div class="font-black text-slate-900">1. Application Logged</div>
                            <div class="text-slate-500 text-[11px] mt-0.5">Your profile and contact credentials have been safely recorded.</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-xl bg-amber-50 border-2 border-amber-300 shadow-xs">
                        <div class="w-6 h-6 rounded-full bg-amber-500 text-slate-950 font-black text-xs flex items-center justify-center shrink-0 mt-0.5 animate-pulse">2</div>
                        <div class="text-xs">
                            <div class="font-black text-amber-950">2. Vetting Interview Scheduled (Current Step)</div>
                            <div class="text-amber-800 text-[11px] mt-0.5">Please be prepared for your interview at <strong>{{ $interviewTime }}</strong> on <strong>{{ $interviewDate }}</strong>.</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/80 opacity-75">
                        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 font-black text-xs flex items-center justify-center shrink-0 mt-0.5">3</div>
                        <div class="text-xs">
                            <div class="font-black text-slate-700">3. Admin Evaluation & Portal Access</div>
                            <div class="text-slate-500 text-[11px] mt-0.5">After the interview, the admin will approve your profile so you can log in to the Crew Portal.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notice -->
            <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-left text-xs text-slate-600 space-y-1">
                <p>📧 An invitation email was sent to <strong class="text-slate-900">{{ $email }}</strong>.</p>
                <p class="text-[11px] text-slate-500">Need help or want to reschedule? Contact us at <a href="mailto:mail@africrew.ke" class="text-amber-700 font-bold hover:underline">mail@africrew.ke</a>.</p>
            </div>

            <!-- Actions -->
            <div class="mt-7 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-full bg-slate-950 hover:bg-slate-900 text-white font-extrabold text-xs transition-all shadow-md text-center text-decoration-none">
                    ← Return to Home
                </a>
                <a href="{{ route('staff.login') }}" class="w-full sm:w-auto px-6 py-3 rounded-full bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 hover:opacity-95 text-white font-extrabold text-xs transition-all shadow-md shadow-pink-500/20 text-center text-decoration-none">
                    Crew Login Page →
                </a>
            </div>

        @else
            <!-- PENDING UNSCHEDULED STATE -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-800 text-xs font-black uppercase tracking-wider mb-6">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Application Under Review</span>
            </div>

            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-amber-400/20 via-orange-500/20 to-pink-500/20 border-2 border-amber-500/30 text-amber-600 flex items-center justify-center mx-auto mb-5 shadow-sm text-3xl">
                ⏳
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">Application Received!</h1>
            
            <p class="text-slate-600 text-sm leading-relaxed mt-3 max-w-md mx-auto">
                Thank you, <strong class="text-slate-900">{{ $name }}</strong>. Your registration for the AfriCrew talent roster has been received and is currently being reviewed by our administrative vetting team.
            </p>

            @if(session('info'))
                <div class="mt-4 p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold">
                    ℹ️ {{ session('info') }}
                </div>
            @endif

            <!-- Application Details Summary Box -->
            <div class="mt-6 bg-slate-50 border border-slate-200/90 rounded-2xl p-4 sm:p-5 text-left text-xs space-y-2.5">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200/80">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[11px]">Reference ID</span>
                    <span class="font-black text-slate-900 font-mono text-xs bg-white px-2 py-0.5 rounded border border-slate-200">{{ $refId }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-bold text-[11px]">Applicant Name</span>
                    <span class="font-extrabold text-slate-900">{{ $name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-bold text-[11px]">Registered Email</span>
                    <span class="font-extrabold text-slate-900">{{ $email }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-bold text-[11px]">Primary Specialty</span>
                    <span class="font-extrabold text-amber-700 bg-amber-100/70 px-2 py-0.5 rounded-md">{{ $category }}</span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-slate-200/80">
                    <span class="text-slate-500 font-bold text-[11px]">Vetting Status</span>
                    <span class="inline-flex items-center gap-1.5 font-black text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full text-[11px]">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-ping"></span>
                        Interview Scheduling in Progress
                    </span>
                </div>
            </div>

            <!-- 3-Step Vetting Timeline -->
            <div class="mt-7 text-left">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 mb-3 text-center sm:text-left">What Happens Next?</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/80">
                        <div class="w-6 h-6 rounded-full bg-emerald-500 text-white font-black text-xs flex items-center justify-center shrink-0 mt-0.5">✓</div>
                        <div class="text-xs">
                            <div class="font-black text-slate-900">1. Application Logged</div>
                            <div class="text-slate-500 text-[11px] mt-0.5">Your profile and contact credentials have been safely recorded.</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-xl bg-amber-50/80 border border-amber-200">
                        <div class="w-6 h-6 rounded-full bg-amber-500 text-slate-950 font-black text-xs flex items-center justify-center shrink-0 mt-0.5 animate-pulse">2</div>
                        <div class="text-xs">
                            <div class="font-black text-amber-950">2. Interview Scheduling (Current Step)</div>
                            <div class="text-amber-800 text-[11px] mt-0.5">Our admin team will schedule your interview and send you an invitation email.</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/80 opacity-75">
                        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 font-black text-xs flex items-center justify-center shrink-0 mt-0.5">3</div>
                        <div class="text-xs">
                            <div class="font-black text-slate-700">3. Admin Decision & Portal Login</div>
                            <div class="text-slate-500 text-[11px] mt-0.5">Once approved after the interview, you will receive an approval email and can log in immediately.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reassurance Notice -->
            <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-left text-xs text-slate-600 space-y-1">
                <p>📧 A confirmation email has been dispatched to <strong class="text-slate-900">{{ $email }}</strong>.</p>
                <p class="text-[11px] text-slate-500">Need urgent help? Contact the vetting desk at <a href="mailto:mail@africrew.ke" class="text-amber-700 font-bold hover:underline">mail@africrew.ke</a>.</p>
            </div>

            <!-- Actions -->
            <div class="mt-7 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-full bg-slate-950 hover:bg-slate-900 text-white font-extrabold text-xs transition-all shadow-md text-center text-decoration-none">
                    ← Return to Home
                </a>
                <a href="{{ route('staff.login') }}" class="w-full sm:w-auto px-6 py-3 rounded-full bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 hover:opacity-95 text-white font-extrabold text-xs transition-all shadow-md shadow-pink-500/20 text-center text-decoration-none">
                    Crew Member Login →
                </a>
            </div>
        @endif

    </div>
</section>
@endsection
