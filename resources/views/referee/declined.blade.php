@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 bg-slate-50">
    <div class="max-w-md w-full bg-white border border-slate-200/90 rounded-3xl p-8 shadow-xl text-center space-y-6">
        <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-600 text-2xl font-black flex items-center justify-center mx-auto">
            ✕
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-black text-slate-950 tracking-tight">Reference Request Declined</h1>
            <p class="text-xs text-slate-600 leading-relaxed">
                Thank you for letting us know. You will not be listed or contacted as a reference for <strong>{{ $professional->full_name }}</strong>.
            </p>
        </div>

        <a href="{{ route('home') }}" class="inline-block px-6 py-3 rounded-full bg-slate-900 text-white font-extrabold text-xs hover:bg-slate-800 transition-all text-decoration-none">
            Return to AfriCrew Home
        </a>
    </div>
</div>
@endsection
