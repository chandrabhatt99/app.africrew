@extends('layouts.app')

@section('content')
<section class="max-w-xl mx-auto px-6 py-20">
    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 md:p-10 shadow-xl text-center">
        
        <!-- Animated Envelope Badge -->
        <div class="w-20 h-20 rounded-full bg-amber-500/10 border-2 border-amber-500/30 text-amber-600 flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm">
            ✉️
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Check Your Email</h1>
        
        <p class="text-slate-600 text-sm leading-relaxed mt-3">
            We've sent a verification link to:
            <br>
            <span class="font-extrabold text-slate-900 bg-slate-100 px-3 py-1 rounded-lg inline-block mt-2">
                {{ session('pending_email', 'your email address') }}
            </span>
        </p>

        <p class="text-slate-500 text-xs mt-4 leading-relaxed">
            Please check your inbox and click the verification link to activate email verification and continue to your Profile & Resume Builder.
        </p>

        <!-- Simulation Link for local testing -->
        @if(session('verification_token'))
            <div class="mt-6 p-5 rounded-2xl bg-amber-50 border border-amber-200 text-left">
                <div class="flex items-center gap-2 text-xs font-black text-amber-900 mb-1">
                    <span>⚡ Quick Test Email Verification Link</span>
                </div>
                <p class="text-xs text-amber-800 mb-3">Click the test link below to simulate email verification:</p>
                
                <a href="{{ route('staff.verify.email', session('verification_token')) }}" class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-400 via-gold-500 to-amber-500 text-slate-950 font-black text-xs py-3 px-5 rounded-xl shadow hover:brightness-105 transition-all text-center">
                    <span>Click Here to Verify Email</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        @endif

        <!-- Resend & Correct Email Options -->
        <div class="mt-8 pt-6 border-t border-slate-100 space-y-4 text-xs font-semibold">
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <form method="POST" action="{{ route('staff.verify.resend') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('pending_email') }}">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-xs transition-all shadow-sm">
                        🔄 Resend Verification Email
                    </button>
                </form>

                <button type="button" onclick="document.getElementById('correct-email-form').classList.toggle('hidden')" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-all border border-slate-200">
                    ✏️ Correct Email Address
                </button>
            </div>

            <!-- Inline Form to Correct Email Address -->
            <div id="correct-email-form" class="hidden mt-4 p-4 bg-slate-50 border border-slate-200 rounded-2xl text-left space-y-3">
                <form method="POST" action="{{ route('staff.verify.updateEmail') }}">
                    @csrf
                    <input type="hidden" name="old_email" value="{{ session('pending_email') }}">
                    <label class="block text-[11px] font-extrabold text-slate-700 uppercase">New Email Address</label>
                    <div class="flex gap-2">
                        <input type="email" name="new_email" required placeholder="enter.correct@email.com" class="flex-1 bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-amber-500">
                        <button type="submit" class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl transition-colors">
                            Update & Send
                        </button>
                    </div>
                </form>
            </div>

            <div class="pt-2 text-slate-400 text-[11px]">
                <a href="{{ route('staff.login') }}" class="font-bold text-slate-600 hover:underline">Back to Login</a>
            </div>
        </div>
    </div>
</section>
@endsection
