@extends('layouts.app')

@section('content')
@php
    $crewFullName = $professional->full_name;
    $refName = $referee['name'] ?? 'Referee';
    $showOnCv = !empty($referee['show_on_cv']);
    $rating = !empty($referee['rating']) ? (int)$referee['rating'] : null;
@endphp

<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 bg-slate-50">
    <div class="max-w-lg w-full bg-white border border-slate-200/90 rounded-3xl p-8 sm:p-10 shadow-xl text-center space-y-6">
        
        <div class="w-16 h-16 rounded-3xl bg-emerald-100 text-emerald-600 text-3xl font-black flex items-center justify-center mx-auto shadow-sm">
            ✓
        </div>

        <div class="space-y-2">
            <span class="text-[11px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200 px-3.5 py-1 rounded-full inline-block">
                Reference Verified Successfully
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight m-0">Thank You, {{ $refName }}!</h1>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed m-0">
                You have successfully verified your professional reference for <strong>{{ $crewFullName }}</strong> on AfriCrew.
            </p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 text-left text-xs space-y-2.5">
            <div class="flex justify-between items-center">
                <span class="text-slate-500 font-bold">Candidate:</span>
                <span class="font-black text-slate-900">{{ $crewFullName }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-500 font-bold">Company / Project:</span>
                <span class="font-bold text-slate-800">{{ $referee['organization'] ?? 'Professional Reference' }}</span>
            </div>
            @if(!empty($referee['crew_role']))
            <div class="flex justify-between items-center">
                <span class="text-slate-500 font-bold">Role:</span>
                <span class="font-bold text-amber-800">{{ $referee['crew_role'] }}</span>
            </div>
            @endif
            @if($rating)
            <div class="flex justify-between items-center">
                <span class="text-slate-500 font-bold">Rating Given:</span>
                <span class="text-amber-500 font-black">
                    @for($s = 1; $s <= 5; $s++)
                        {{ $s <= $rating ? '★' : '☆' }}
                    @endfor
                    <span class="text-slate-700 text-[11px] ml-1">({{ $rating }}/5)</span>
                </span>
            </div>
            @endif
            <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                <span class="text-slate-500 font-bold">Public CV Display:</span>
                @if($showOnCv)
                    <span class="text-emerald-700 font-black flex items-center gap-1">
                        <span>✓</span> Allowed on Public Digital CV
                    </span>
                @else
                    <span class="text-slate-600 font-bold flex items-center gap-1">
                        <span>🔒</span> Private (Admin Vetting Only)
                    </span>
                @endif
            </div>
        </div>

        <p class="text-[11px] text-slate-400 m-0">
            🔒 Your private contact information (email & phone) remains strictly confidential.
        </p>

        <div class="pt-2">
            <a href="{{ route('home') }}" class="inline-block px-7 py-3 rounded-full bg-slate-950 hover:bg-slate-800 text-white font-extrabold text-xs transition-all text-decoration-none shadow-md">
                Return to AfriCrew Home
            </a>
        </div>
    </div>
</div>
@endsection
