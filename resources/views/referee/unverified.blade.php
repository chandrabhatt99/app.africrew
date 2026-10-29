@extends('layouts.app')

@section('content')
@php
    $crewFullName = $professional->full_name;
    $refName = $referee['name'] ?? 'Referee';
    $reason = $referee['unverified_reason'] ?? 'employment_not_confirmed';
    $reasonTitle = $reason === 'name_removal_request'
        ? 'Referee Name Removal Request'
        : 'Employment Not Confirmed';
    $reasonDesc = $reason === 'name_removal_request'
        ? 'The referee does not wish to have their name included in the crew member’s CV/resume as a referee.'
        : 'The referee confirms that the crew member did not work with them or at their organization.';
@endphp

<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 bg-slate-50">
    <div class="max-w-lg w-full bg-white border border-slate-200/90 rounded-3xl p-8 sm:p-10 shadow-xl text-center space-y-6">
        
        <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-700 text-2xl font-black flex items-center justify-center mx-auto shadow-sm">
            ✕
        </div>

        <div class="space-y-2">
            <span class="text-[11px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 border border-rose-200 px-3.5 py-1 rounded-full inline-block">
                Reference Marked as Unverified
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight m-0">Thank You, {{ $refName }}</h1>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed m-0">
                Your response has been securely recorded. You will not be listed as a reference for <strong>{{ $crewFullName }}</strong>.
            </p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 text-left text-xs space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-slate-500 font-bold">Candidate:</span>
                <span class="font-black text-slate-900">{{ $crewFullName }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-500 font-bold">Status:</span>
                <span class="text-rose-700 font-black">Unverified</span>
            </div>
            <div class="pt-2 border-t border-slate-200 space-y-1">
                <span class="text-slate-500 font-bold block">Recorded Reason:</span>
                <span class="font-black text-slate-900 block text-xs">{{ $reasonTitle }}</span>
                <p class="text-slate-600 text-[11px] m-0 leading-relaxed">{{ $reasonDesc }}</p>
            </div>
        </div>

        <p class="text-[11px] text-slate-400 m-0">
            Thank you for helping us maintain transparency and accuracy across our crewing platform.
        </p>

        <div class="pt-2">
            <a href="{{ route('home') }}" class="inline-block px-7 py-3 rounded-full bg-slate-950 hover:bg-slate-800 text-white font-extrabold text-xs transition-all text-decoration-none shadow-md">
                Return to AfriCrew Home
            </a>
        </div>
    </div>
</div>
@endsection
