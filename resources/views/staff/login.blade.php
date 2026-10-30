@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-140px)] flex items-center justify-center py-8 px-4 sm:px-6 bg-slate-50 text-slate-900 relative overflow-hidden">
    <!-- Ambient Background Glows -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl space-y-5 relative z-10">
        
        <!-- Header -->
        <div class="text-center space-y-1.5">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-800 text-xs font-black uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>✨ Crew Member Portal</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">Member Login</h1>
            <p class="text-xs text-slate-500 leading-relaxed">Access your assigned event shifts, update availability & manage profile</p>
        </div>

        <!-- Notification: Interview Scheduled Notice -->
        @if(session('status_interview_scheduled'))
            <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-400/80 shadow-md space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                    <span class="text-xs font-black uppercase tracking-wider text-amber-900">🎙️ Vetting Interview Scheduled</span>
                </div>
                <div class="text-xs text-slate-700 leading-relaxed">
                    Great news! An official interview has been scheduled for your AfriCrew application:
                </div>
                <div class="p-3 bg-white/90 rounded-xl border border-amber-200/80 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-bold">📅 Date:</span>
                        <strong class="text-slate-900">{{ session('interview_date') }}</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-bold">⏰ Time:</span>
                        <strong class="text-slate-900">{{ session('interview_time') }}</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-bold">👤 Interviewer:</span>
                        <strong class="text-slate-900">{{ session('interviewer_name') }}</strong>
                    </div>
                    @if(session('interview_link'))
                        <div class="pt-1 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-slate-500 font-bold">🔗 Link / Desk:</span>
                            @if(Str::startsWith(session('interview_link'), ['http://', 'https://']))
                                <a href="{{ session('interview_link') }}" target="_blank" class="text-amber-700 font-extrabold underline truncate max-w-[200px]">Join Meeting Online ↗</a>
                            @else
                                <span class="font-bold text-slate-800">{{ session('interview_link') }}</span>
                            @endif
                        </div>
                    @endif
                    @if(session('interview_notes'))
                        <div class="pt-1 border-t border-slate-100 text-[11px] text-slate-600">
                            <span class="font-bold text-slate-700">Instructions:</span> {{ session('interview_notes') }}
                        </div>
                    @endif
                </div>
                <p class="text-[11px] text-amber-900 font-medium">
                    ✉️ An invitation email was also sent to your inbox. Once completed and approved by admin, you will be able to log in.
                </p>
            </div>
        @endif

        <!-- Notification: Application Rejected Notice -->
        @if(session('status_rejected'))
            <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-300 shadow-sm space-y-2 text-xs">
                <div class="flex items-center gap-2 text-rose-900 font-black">
                    <span class="text-base">❌</span>
                    <span class="uppercase tracking-wider">Application / Profile Rejected</span>
                </div>
                <p class="text-rose-950 font-semibold leading-relaxed">
                    Your profile is rejected. Your application was not approved by the AfriCrew vetting team.
                </p>
                @if(session('rejection_reason'))
                    <div class="p-2.5 bg-white/80 rounded-xl border border-rose-200 text-rose-900 italic text-[11px]">
                        "{{ session('rejection_reason') }}"
                    </div>
                @endif
                <p class="text-[11px] text-rose-800">
                    If you have questions or wish to appeal this decision, please reach out to <a href="mailto:mail@africrew.ke" class="font-bold underline text-rose-950">mail@africrew.ke</a>.
                </p>
            </div>
        @endif

        @if(session('status_check_success'))
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-extrabold flex items-center gap-2 shadow-xs">
                <span>✓ {{ session('status_check_success') }}</span>
            </div>
        @endif

        @if(session('status_check_info'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold shadow-xs">
                <span>ℹ️ {{ session('status_check_info') }}</span>
            </div>
        @endif

        @if(session('status_check_error'))
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold shadow-xs">
                <span>⚠️ {{ session('status_check_error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-extrabold flex items-center gap-2 shadow-xs">
                <span>✓ {{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold shadow-xs">{{ session('error') }}</div>
        @endif

        @if($errors->any() && !session('status_rejected'))
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold shadow-xs">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Social Login Options -->
        <div class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('social.redirect', ['provider' => 'google', 'type' => 'staff']) }}" class="w-full bg-white hover:bg-slate-50 text-slate-700 font-extrabold text-xs border border-slate-200/90 rounded-2xl py-3 px-3 flex items-center justify-center gap-2.5 shadow-xs hover:shadow-sm transition-all cursor-pointer text-decoration-none">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Google</span>
                </a>

                <a href="{{ route('social.redirect', ['provider' => 'facebook', 'type' => 'staff']) }}" class="w-full bg-[#1877F2] hover:bg-[#166FE5] text-white font-extrabold text-xs border border-transparent rounded-2xl py-3 px-3 flex items-center justify-center gap-2.5 shadow-xs hover:shadow-sm transition-all cursor-pointer text-decoration-none">
                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>Facebook</span>
                </a>
            </div>

            <div class="relative py-1">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200/80"></div>
                </div>
                <div class="relative flex justify-center text-[10px] uppercase font-extrabold tracking-widest text-slate-400">
                    <span class="bg-white px-3">or log in with credentials</span>
                </div>
            </div>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('staff.login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                    Crew Email Address
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', old('status_email')) }}" required autofocus
                    placeholder="usher.sarah@africrew.com"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-amber-500 focus:bg-white transition-all font-medium">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-700">
                        Password
                    </label>
                    <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-amber-700 hover:underline">Forgot password?</a>
                </div>
                <input type="password" name="password" id="password" required
                    placeholder="••••••••"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-amber-500 focus:bg-white transition-all">
            </div>

            <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 hover:opacity-95 text-white font-black text-xs sm:text-sm shadow-lg shadow-pink-500/25 transition-all flex items-center justify-center gap-2 group cursor-pointer border-0">
                <span>Log In to Crew Portal</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <!-- Quick Status Checker Tool -->
        <div class="pt-3 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('status-checker-drawer').classList.toggle('hidden')" class="w-full py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200/80 text-slate-700 font-extrabold text-xs flex items-center justify-center gap-2 transition-colors">
                <span>🔍 Check Interview & Application Status</span>
                <span class="text-xs">▼</span>
            </button>

            <div id="status-checker-drawer" class="mt-3 p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3 {{ session('status_interview_scheduled') || session('status_rejected') || session('status_check_error') || session('status_check_info') || session('status_check_success') ? '' : 'hidden' }}">
                <p class="text-[11px] text-slate-500 font-medium">
                    Enter your registered email below to check your scheduled interview date, time, and application review status:
                </p>
                <form method="POST" action="{{ route('staff.check-status') }}" class="space-y-2">
                    @csrf
                    <div class="flex gap-2">
                        <input type="email" name="status_email" required value="{{ old('status_email', old('email')) }}" placeholder="your.email@example.com" class="flex-grow bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold outline-none focus:border-amber-500">
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs rounded-xl shadow-xs shrink-0">
                            Check Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="pt-2 text-center text-xs text-slate-500">
            <span>Not a crew member yet?</span> 
            <a href="{{ route('crew.create') }}" class="font-extrabold text-amber-700 hover:underline ml-1">Join Crew →</a>
        </div>

    </div>
</div>
@endsection
