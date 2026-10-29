@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-16 px-4 flex items-center justify-center">
    <div class="max-w-md w-full bg-white border border-slate-200 rounded-3xl p-8 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <img src="{{ asset('images/africrew_logo.jpg') }}" alt="AfriCrew" class="h-10 w-auto mx-auto mb-3">
            <h1 class="text-2xl font-black text-slate-900">Set New Password</h1>
            <p class="text-xs text-slate-500 font-semibold">Enter your email and choose a secure new password for your account.</p>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold leading-relaxed space-y-1">
                @foreach($errors->all() as $error)
                    <div>⚠️ {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4 text-xs font-bold">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ request('email') }}" required class="w-full px-4 py-3 border border-slate-300 rounded-2xl focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-slate-700 mb-1">New Password</label>
                <input type="password" name="password" placeholder="At least 8 characters" required class="w-full px-4 py-3 border border-slate-300 rounded-2xl focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" placeholder="Re-enter password" required class="w-full px-4 py-3 border border-slate-300 rounded-2xl focus:outline-none focus:border-amber-500">
            </div>

            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500 text-white font-black rounded-2xl text-xs shadow-md hover:opacity-95 transition-all">
                Update Password 🔒
            </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-100">
            <a href="{{ route('client.login') }}" class="text-xs font-bold text-amber-700 hover:underline">
                ← Back to Login Screen
            </a>
        </div>
    </div>
</div>
@endsection
