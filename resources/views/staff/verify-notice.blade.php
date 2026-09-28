@extends('layouts.app')

@section('content')
<section class="max-w-xl mx-auto px-6 py-20">
    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 md:p-10 shadow-xl text-center">
        
        <!-- Animated Envelope Badge -->
        <div class="w-20 h-20 rounded-full bg-amber-500/10 border-2 border-amber-500/30 text-amber-600 flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm">
            ✉️
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Verify Your Email Address</h1>
        
        <p class="text-slate-600 text-sm leading-relaxed mt-3">
            We have sent a verification link to:
            <br>
            <span class="font-extrabold text-slate-900 bg-slate-100 px-3 py-1 rounded-lg inline-block mt-2">
                {{ session('pending_email', 'your email address') }}
            </span>
        </p>

        <p class="text-slate-500 text-xs mt-4 leading-relaxed">
            Please check your email inbox and click the verification link to verify your account and begin your profile onboarding wizard.
        </p>

        <!-- Simulation Banner for Instant Testing -->
        @if(session('verification_token'))
            <div class="mt-8 p-6 rounded-2xl bg-amber-50 border border-amber-200 text-left">
                <div class="flex items-center gap-2 text-xs font-black text-amber-900 mb-1">
                    <span>⚡ Quick Test Verification Link</span>
                </div>
                <p class="text-xs text-amber-800 mb-3">Click the simulated verification link below to verify your email instantly:</p>
                
                <a href="{{ route('staff.verify.email', session('verification_token')) }}" class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-400 via-gold-500 to-amber-500 text-slate-950 font-black text-xs py-3 px-5 rounded-xl shadow hover:brightness-105 transition-all text-center">
                    <span>Click Here to Verify Email</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        @endif

        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-center gap-4 text-xs font-semibold text-slate-500">
            <span>Didn't receive an email?</span>
            <a href="{{ route('staff.login') }}" class="font-bold text-amber-600 hover:underline">Back to Login</a>
        </div>
    </div>
</section>
@endsection
