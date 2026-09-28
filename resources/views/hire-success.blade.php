@extends('layouts.app')

@section('content')
<section class="max-w-2xl mx-auto px-6 py-24 text-center">
    <div class="bg-white border border-slate-200/80 rounded-3xl p-10 md:p-14 shadow-lg relative overflow-hidden">
        <div class="w-20 h-20 rounded-3xl bg-amber-50 text-amber-700 mx-auto flex items-center justify-center text-3xl font-black shadow-inner mb-6">
            ✓
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">Crew Request Submitted!</h1>
        <p class="text-slate-600 mt-4 text-base leading-relaxed">
            Thank you for choosing AfriCrew. Our crewing team will review your event requirements and contact you within 24 hours.
        </p>
        
        <div class="mt-8 pt-6 border-t border-slate-100 flex flex-wrap justify-center gap-4">
            <a href="{{ route('home') }}" class="px-8 py-3.5 rounded-full bg-slate-900 text-gold-400 font-extrabold text-sm shadow-md hover:bg-slate-800 transition-all">
                Back to Homepage
            </a>
            <a href="{{ route('hire.create') }}" class="px-8 py-3.5 rounded-full bg-slate-100 text-slate-700 font-bold text-sm hover:bg-slate-200 transition-all">
                Submit Another Request
            </a>
        </div>
    </div>
</section>
@endsection
