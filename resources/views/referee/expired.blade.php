@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 bg-slate-50">
    <div class="max-w-md w-full bg-white border border-slate-200/90 rounded-3xl p-8 shadow-xl text-center space-y-6">
        <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 text-2xl font-black flex items-center justify-center mx-auto">
            ⏳
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-black text-slate-950 tracking-tight">Verification Link Invalid or Expired</h1>
            <p class="text-xs text-slate-600 leading-relaxed">
                This reference verification link is either no longer valid, has already been processed, or expired.
            </p>
        </div>

        <a href="{{ route('home') }}" class="inline-block px-6 py-3 rounded-full bg-slate-900 text-white font-extrabold text-xs hover:bg-slate-800 transition-all text-decoration-none">
            Return to AfriCrew Home
        </a>
    </div>
</div>
@endsection
