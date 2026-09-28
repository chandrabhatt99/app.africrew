@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-140px)] flex items-center justify-center py-10 px-4 sm:px-6 bg-slate-50 text-slate-900 relative overflow-hidden">
    <!-- Ambient Background Glows -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full bg-white border border-slate-200/90 rounded-3xl p-8 shadow-xl space-y-6 relative z-10">
        
        <div class="text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 text-xs font-black uppercase mb-3">
                🏢 Client Account Registration
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">Create Client Account</h1>
            <p class="text-xs text-slate-500 mt-1">Register to post event crewing requests, manage assigned crew, and track shift details.</p>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold shadow-xs space-y-1">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Social Register Options -->
        <div class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('social.redirect', ['provider' => 'google', 'type' => 'client']) }}" class="w-full bg-white hover:bg-slate-50 text-slate-700 font-extrabold text-xs border border-slate-200/90 rounded-2xl py-3 px-3 flex items-center justify-center gap-2.5 shadow-xs hover:shadow-sm transition-all cursor-pointer text-decoration-none">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Google</span>
                </a>

                <a href="{{ route('social.redirect', ['provider' => 'facebook', 'type' => 'client']) }}" class="w-full bg-[#1877F2] hover:bg-[#166FE5] text-white font-extrabold text-xs border border-transparent rounded-2xl py-3 px-3 flex items-center justify-center gap-2.5 shadow-xs hover:shadow-sm transition-all cursor-pointer text-decoration-none">
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
                    <span class="bg-white px-3">or register with email</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('client.register.submit') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Full Name
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Kamau"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-amber-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Company / Organization Name <span class="text-slate-400 font-normal lowercase">(optional)</span>
                </label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Apex Events Ltd"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-amber-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Email Address
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="you@company.com"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-amber-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Phone Number
                </label>
                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="+254 712 345678"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-amber-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Password
                </label>
                <input type="password" name="password" required placeholder="At least 6 characters"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-amber-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Confirm Password
                </label>
                <input type="password" name="password_confirmation" required placeholder="Repeat password"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-amber-500 focus:bg-white transition-all">
            </div>

            <button type="submit" class="w-full py-4 rounded-2xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-sm shadow-lg transition-all mt-2">
                Register Client Account →
            </button>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Already have a Client Account? 
            <a href="{{ route('client.login') }}" class="font-bold text-amber-700 hover:underline">Log In Here →</a>
        </div>

    </div>
</div>
@endsection
