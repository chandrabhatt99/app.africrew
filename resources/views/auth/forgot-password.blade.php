@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-16 px-4 flex items-center justify-center">
    <div class="max-w-md w-full bg-white border border-slate-200 rounded-3xl p-8 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <img src="{{ asset('images/africrew_logo.jpg') }}" alt="AfriCrew" class="h-10 w-auto mx-auto mb-3">
            <h1 class="text-2xl font-black text-slate-900">Forgot Your Password?</h1>
            <p class="text-xs text-slate-500 font-semibold">Enter your account email to receive a password reset token.</p>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold leading-relaxed">
                ✓ {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4 text-xs font-bold">
            @csrf
            <div>
                <label class="block text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" placeholder="user@domain.com" required class="w-full px-4 py-3 border border-slate-300 rounded-2xl focus:outline-none focus:border-amber-500">
            </div>

            <button type="submit" class="w-full py-3.5 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black rounded-2xl text-xs shadow-md">
                Send Reset Link 📧
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
