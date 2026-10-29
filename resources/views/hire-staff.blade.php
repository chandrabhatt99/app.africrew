@extends('layouts.app')

@section('content')
@php
    $profs = isset($requestedProfessionals) && $requestedProfessionals->count() > 0 
        ? $requestedProfessionals 
        : (isset($requestedProfessional) && $requestedProfessional ? collect([$requestedProfessional]) : collect());
    
    $isMultiCrew = $profs->count() > 1;
    $primaryProf = $profs->first();
    $crewName = $primaryProf ? $primaryProf->full_name : 'Patrick Mwangi';
    $crewFirstName = $primaryProf ? strtok($primaryProf->full_name, ' ') : 'Patrick';
    $crewRole = $primaryProf ? strtoupper($primaryProf->category ?? 'Professional Event Usher') : 'PROFESSIONAL EVENT USHER';
    $avatarSrc = ($primaryProf && $primaryProf->profile_photo) ? get_storage_url($primaryProf->profile_photo) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400';
    $eventDate = request('event_date', date('d October Y', strtotime('+3 days')));
    $locationStr = request('location', ($primaryProf ? ($primaryProf->city . ', ' . ($primaryProf->country ?? 'Kenya')) : 'Nairobi, Kenya'));
    $serviceStr = request('category', ($primaryProf->category ?? 'Corporate Event Ushering'));
    $baseDailyRate = $primaryProf ? ($primaryProf->full_day_rate ?: ($primaryProf->one_day_rate ?: 3500)) : 3500;
@endphp

<div class="bg-[#FAF9F6] text-slate-900 min-h-screen py-8 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-6xl mx-auto space-y-8">

        <!-- TOP NAVIGATION BAR & STEP TRACKER -->
        <div class="bg-white/80 backdrop-blur-md border border-slate-100 rounded-3xl p-4 sm:p-5 shadow-sm flex items-center justify-between gap-4">
            <!-- Step Indicators -->
            <div class="flex items-center gap-2 sm:gap-6 overflow-x-auto py-1 scrollbar-none">
                
                <!-- Step 1 -->
                <div id="step-nav-1" onclick="goToStep(1)" class="flex items-center gap-2 sm:gap-3 cursor-pointer group shrink-0">
                    <div id="step-nav-badge-1" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-r from-orange-500 via-pink-500 to-rose-500 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center shadow-md shadow-pink-500/20 transition-all">
                        1
                    </div>
                    <div>
                        <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-slate-400 block leading-tight">STEP 1</span>
                        <span id="step-nav-text-1" class="text-xs sm:text-sm font-black text-slate-900 block leading-tight">Account Setup</span>
                    </div>
                </div>

                <div class="w-8 sm:w-12 h-0.5 bg-slate-200 rounded-full shrink-0"></div>

                <!-- Step 2 -->
                <div id="step-nav-2" onclick="goToStep(2)" class="flex items-center gap-2 sm:gap-3 cursor-pointer group opacity-60 hover:opacity-100 transition-all shrink-0">
                    <div id="step-nav-badge-2" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-rose-50 border border-rose-200 text-rose-500 font-extrabold text-xs sm:text-sm flex items-center justify-center transition-all">
                        2
                    </div>
                    <div>
                        <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-slate-400 block leading-tight">STEP 2</span>
                        <span id="step-nav-text-2" class="text-xs sm:text-sm font-bold text-slate-500 block leading-tight">Project Details</span>
                    </div>
                </div>

                <div class="w-8 sm:w-12 h-0.5 bg-slate-200 rounded-full shrink-0"></div>

                <!-- Step 3 -->
                <div id="step-nav-3" onclick="goToStep(3)" class="flex items-center gap-2 sm:gap-3 cursor-pointer group opacity-60 hover:opacity-100 transition-all shrink-0">
                    <div id="step-nav-badge-3" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-rose-50 border border-rose-200 text-rose-500 font-extrabold text-xs sm:text-sm flex items-center justify-center transition-all">
                        3
                    </div>
                    <div>
                        <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-slate-400 block leading-tight">STEP 3</span>
                        <span id="step-nav-text-3" class="text-xs sm:text-sm font-bold text-slate-500 block leading-tight">Files</span>
                    </div>
                </div>

                <div class="w-8 sm:w-12 h-0.5 bg-slate-200 rounded-full shrink-0"></div>

                <!-- Step 4 -->
                <div id="step-nav-4" onclick="goToStep(4)" class="flex items-center gap-2 sm:gap-3 cursor-pointer group opacity-60 hover:opacity-100 transition-all shrink-0">
                    <div id="step-nav-badge-4" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-rose-50 border border-rose-200 text-rose-500 font-extrabold text-xs sm:text-sm flex items-center justify-center transition-all">
                        4
                    </div>
                    <div>
                        <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-slate-400 block leading-tight">STEP 4</span>
                        <span id="step-nav-text-4" class="text-xs sm:text-sm font-bold text-slate-500 block leading-tight">Review</span>
                    </div>
                </div>

            </div>

            <!-- Logo -->
            <div class="shrink-0 flex items-center gap-2">
                <a href="{{ url('/') }}" class="flex items-center gap-1.5 font-black text-xl text-slate-900 tracking-tight text-decoration-none">
                    <span class="w-8 h-8 rounded-xl bg-gradient-to-r from-orange-500 via-pink-500 to-rose-500 flex items-center justify-center text-white text-base">A</span>
                    <span>AfriCrew</span>
                </a>
            </div>
        </div>

        <!-- Global Validation Error Summary -->
        @if($errors->any())
            <div class="rounded-3xl bg-rose-50 border border-rose-200 p-5 text-rose-900 text-xs sm:text-sm shadow-sm">
                <div class="font-extrabold mb-1.5 flex items-center gap-2 text-rose-950">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Please correct the errors before submitting:</span>
                </div>
                <ul class="list-disc pl-5 space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- MAIN WIZARD FORM -->
        <form id="hiring-form" method="POST" action="{{ route('hire.store') }}" enctype="multipart/form-data">
            @csrf

            @if($profs->count() > 0)
                @foreach($profs as $p)
                    <input type="hidden" name="requested_professional_ids[]" value="{{ $p->id }}">
                @endforeach
                <input type="hidden" name="requested_professional_id" value="{{ $primaryProf->id }}">
            @endif

            <!-- Hidden Defaults -->
            <input type="hidden" name="category" id="hidden_category" value="{{ old('category', $serviceStr) }}">
            <input type="hidden" name="company_name" id="company_name" value="{{ old('company_name', Auth::check() ? Auth::user()->company_name : '') }}">
            <input type="hidden" name="staff_count" value="{{ max(1, $profs->count()) }}">
            <input type="hidden" name="shift_duration" id="hidden_shift_duration" value="full_day">
            <input type="hidden" name="start_time" value="09:00">
            <input type="hidden" name="end_time" value="17:00">
            <input type="hidden" name="currency" id="hidden_currency" value="KES">
            <input type="hidden" name="rate_type" id="hidden_rate_type" value="{{ old('rate_type', 'negotiable') }}">
            <input type="hidden" name="budget" id="hidden_budget" value="{{ old('budget', $baseDailyRate * max(1, $profs->count())) }}">
            <input type="hidden" name="daily_rate" id="hidden_daily_rate" value="{{ old('daily_rate', $baseDailyRate) }}">


            <!-- ========================================================= -->
            <!-- STEP 1: ACCOUNT SIGN IN / CONTACT DETAILS (PDF PAGE 5) -->
            <!-- ========================================================= -->
            <div id="step-container-1" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Main Content Card -->
                <div class="lg:col-span-8 bg-white border border-slate-100 rounded-[32px] p-8 sm:p-12 shadow-xl shadow-slate-200/40 space-y-8">
                    
                    <!-- Step Badge -->
                    <div>
                        <span class="inline-block px-3.5 py-1 rounded-full bg-amber-50 border border-amber-200/60 text-amber-800 text-[11px] font-extrabold uppercase tracking-wider mb-3">
                            STEP 01 OF 04
                        </span>
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            Let’s start with your details.
                        </h1>
                        <p class="text-slate-500 text-sm sm:text-base font-normal mt-2 leading-relaxed">
                            Already have an AfriCrew account? <a href="{{ route('client.login') }}" class="text-rose-500 font-extrabold underline hover:text-rose-600">Sign in</a>. New here? Enter your email and details below and we'll guide you through the rest without losing your hiring request.
                        </p>
                    </div>

                    <!-- Social Login Options (Google & Facebook) -->
                    <div class="space-y-3 pt-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <a href="{{ route('social.redirect', ['provider' => 'google', 'type' => 'hire']) }}" class="w-full bg-white hover:bg-slate-50 text-slate-700 font-extrabold text-xs sm:text-sm border border-slate-200/90 rounded-2xl py-3.5 px-4 flex items-center justify-center gap-3 shadow-xs hover:shadow-sm transition-all group cursor-pointer text-decoration-none">
                                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                <span>Continue with Google</span>
                            </a>

                            <a href="{{ route('social.redirect', ['provider' => 'facebook', 'type' => 'hire']) }}" class="w-full bg-[#1877F2] hover:bg-[#166FE5] text-white font-extrabold text-xs sm:text-sm border border-transparent rounded-2xl py-3.5 px-4 flex items-center justify-center gap-3 shadow-xs hover:shadow-sm transition-all cursor-pointer text-decoration-none">
                                <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span>Continue with Facebook</span>
                            </a>
                        </div>

                        <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-200/80"></div>
                            </div>
                            <div class="relative flex justify-center text-[10px] uppercase font-extrabold tracking-widest text-slate-400">
                                <span class="bg-white px-3">or continue with email</span>
                            </div>
                        </div>
                    </div>

                    <!-- Email Input & Primary Action -->
                    <div class="space-y-4 pt-2">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            EMAIL ADDRESS <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex flex-col sm:flex-row items-stretch gap-3">
                            <div class="relative flex-1">
                                <input type="email" id="email_step1" name="email" value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" required
                                    placeholder="name@example.com"
                                    oninput="syncSidebarValues()"
                                    class="w-full bg-[#FAF9F6] border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-rose-500 focus:bg-white transition-all pr-12">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    ✉️
                                </span>
                            </div>

                            <button type="button" onclick="goToStep(2)" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-orange-500 via-pink-500 to-rose-500 text-white font-extrabold text-sm shadow-lg shadow-pink-500/25 hover:opacity-95 transition-all flex items-center justify-center gap-2 shrink-0">
                                <span>CONTINUE</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- Contact & Account Information -->
                    <div class="p-6 rounded-2xl bg-[#FAF9F6] border border-slate-200/80 space-y-4">
                        <div class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Contact & Account Details</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Full Name *</label>
                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name', Auth::check() ? Auth::user()->name : '') }}" required placeholder="e.g. Sarah Jenkins" oninput="syncSidebarValues()" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-900 outline-none focus:border-rose-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Phone Number *</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', Auth::check() ? Auth::user()->phone : '') }}" required placeholder="+254 700 000 000" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-900 outline-none focus:border-rose-500">
                            </div>
                        </div>

                        @guest
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-200/60">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Create Password *</label>
                                    <input type="password" id="password" name="password" required placeholder="Min 6 characters" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-900 outline-none focus:border-rose-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Confirm Password *</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Re-enter password" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-900 outline-none focus:border-rose-500">
                                </div>
                            </div>
                        @else
                            <div class="text-xs text-emerald-600 font-bold flex items-center gap-1.5 pt-1">
                                <span>✓</span>
                                <span>Logged in as {{ Auth::user()->name }} (Account details pre-filled)</span>
                            </div>
                        @endguest
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                        <div class="flex items-center gap-1.5">
                            <span>🔒</span>
                            <span>Authentication happens inside this order form.</span>
                        </div>
                        <button type="button" onclick="goToStep(2)" class="text-slate-400 hover:text-rose-500 font-extrabold flex items-center gap-1">
                            <span>Continue to project</span>
                            <span>→</span>
                        </button>
                    </div>

                </div>

                <!-- Right Sidebar Column (PDF PAGE 5 SPECIFICATION) -->
                <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                    
                    <!-- Hiring Request Summary Card -->
                    <div class="bg-white border border-slate-100 rounded-[32px] p-6 shadow-xl shadow-slate-200/40 space-y-6">
                        <div class="text-[11px] font-extrabold uppercase tracking-wider text-rose-500">
                            YOUR HIRING REQUEST
                        </div>

                        <!-- Talent Header Card (Single or Multi-Crew List) -->
                        <div class="space-y-3 pb-5 border-b border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 block leading-tight">
                                You're hiring {{ $isMultiCrew ? "({$profs->count()} Crew Members)" : "1 Crew Member" }}
                            </span>
                            
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @forelse($profs as $p)
                                    @php
                                        $pAvatar = $p->profile_photo ? get_storage_url($p->profile_photo) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400';
                                    @endphp
                                    <div class="flex items-center gap-3 bg-[#FAF9F6] p-2.5 rounded-2xl border border-slate-200/60">
                                        <img src="{{ $pAvatar }}" alt="{{ $p->full_name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-extrabold text-slate-900 truncate">{{ $p->full_name }}</h4>
                                            <span class="text-[9px] font-black uppercase text-amber-700 block truncate">
                                                {{ $p->category ?: 'Event Usher' }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-xs text-slate-400 font-medium italic">General Crew Request</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Request Details List -->
                        <div class="space-y-4 text-xs">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase text-slate-400 block tracking-wider">SERVICE</span>
                                <span id="sidebar-service" class="font-extrabold text-slate-800 text-sm block mt-0.5">{{ $serviceStr }}</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-extrabold uppercase text-slate-400 block tracking-wider">EVENT DATE</span>
                                <span id="sidebar-date" class="font-extrabold text-slate-800 text-sm block mt-0.5">{{ $eventDate }}</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-extrabold uppercase text-slate-400 block tracking-wider">LOCATION</span>
                                <span id="sidebar-location" class="font-extrabold text-slate-800 text-sm block mt-0.5">{{ $locationStr }}</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-extrabold uppercase text-slate-400 block tracking-wider">DURATION</span>
                                <span id="sidebar-duration" class="font-extrabold text-slate-800 text-sm block mt-0.5">8 Hours</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-extrabold uppercase text-slate-400 block tracking-wider">OFFER / BUDGET</span>
                                <span id="sidebar-budget" class="font-extrabold text-emerald-600 text-sm block mt-0.5">KES {{ number_format($baseDailyRate) }} (Negotiable)</span>
                            </div>
                        </div>

                        <!-- Talent Quote Box -->
                        <div class="p-4 rounded-2xl bg-[#FAF9F6] border border-amber-200/60 relative space-y-1">
                            <div class="text-amber-500 font-serif text-2xl leading-none font-bold">“</div>
                            <p class="text-xs text-slate-600 font-medium italic leading-relaxed">
                                "Thanks for considering working with us. Tell us a little more about your event and we'll have everything needed to understand the assignment."
                            </p>
                        </div>
                    </div>

                    <!-- Bottom Floating Next Up Card -->
                    <div class="bg-[#0D1322] text-white p-6 rounded-[28px] shadow-2xl shadow-slate-900/30 space-y-2 border border-slate-800">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-400 block">NEXT UP</span>
                        <h4 class="text-lg font-extrabold text-white">Project Details</h4>
                        <p class="text-xs text-slate-400 leading-relaxed font-normal">
                            Tell us about the specific role and event vibes you're looking for.
                        </p>
                    </div>

                </div>

            </div>


            <!-- ========================================================= -->
            <!-- STEP 2: PROJECT DETAILS (PDF PAGE 6 DYNAMIC GREETING SPECIFICATION) -->
            <!-- ========================================================= -->
            <div id="step-container-2" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Main Form Card -->
                <div class="lg:col-span-8 bg-white border border-slate-100 rounded-[32px] p-8 sm:p-12 shadow-xl shadow-slate-200/40 space-y-8">
                    
                    <div>
                        <span class="inline-block px-3.5 py-1 rounded-full bg-amber-50 border border-amber-200/60 text-amber-800 text-[11px] font-extrabold uppercase tracking-wider mb-3">
                            STEP 02 OF 04
                        </span>

                        <!-- PDF PAGE 6 GREETING LOGIC: If multiple crew selected -> "Dear Client, Tell us more about your event." -->
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight" id="step2-heading">
                            @if($isMultiCrew)
                                Dear Client, Tell us more about your event.
                            @else
                                Tell me about your event.
                            @endif
                        </h1>
                        
                        <p class="text-slate-500 text-sm sm:text-base font-normal mt-2 leading-relaxed" id="step2-subheading">
                            @if($isMultiCrew)
                                A few details will help our team of {{ $profs->count() }} selected crew members understand exactly what you need for this assignment.
                            @else
                                A few details will help {{ $crewFirstName }} understand exactly what you need for this assignment.
                            @endif
                        </p>
                    </div>

                    <div class="space-y-6">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Event Name -->
                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                                    EVENT NAME <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="event_name" name="event_name" value="{{ old('event_name', request('event_name')) }}" required
                                    placeholder="e.g. Annual Tech Summit"
                                    oninput="syncSidebarValues()"
                                    class="w-full bg-[#FAF9F6] border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-rose-500 focus:bg-white transition-all">
                            </div>

                            <!-- Event Type -->
                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                                    EVENT TYPE <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="event_type" name="event_type" value="{{ old('event_type', 'Corporate Gala') }}" required
                                    placeholder="Corporate Gala, Concert, Wedding..."
                                    oninput="syncSidebarValues()"
                                    class="w-full bg-[#FAF9F6] border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-rose-500 focus:bg-white transition-all">
                            </div>
                        </div>

                        <!-- Date & Location -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2 flex items-center justify-between">
                                    <span>EVENT DATE(S) <span class="text-rose-500">*</span></span>
                                    <span id="event_days_badge" class="text-[10px] font-black text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">1 Day</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">📅</span>
                                    <input type="text" id="event_date" name="event_date" value="{{ old('event_date', request('dates', date('d M Y', strtotime('+3 days')))) }}" required
                                        placeholder="Select date(s)..."
                                        onchange="syncSidebarValues()"
                                        class="w-full bg-[#FAF9F6] border border-slate-200 rounded-2xl pl-11 pr-5 py-4 text-slate-900 text-sm outline-none focus:border-rose-500 focus:bg-white transition-all cursor-pointer font-extrabold">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                                    LOCATION / VENUE <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="location" name="location" value="{{ old('location', $locationStr) }}" required
                                    placeholder="e.g. Nairobi, Kenya"
                                    oninput="syncSidebarValues()"
                                    class="w-full bg-[#FAF9F6] border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-rose-500 focus:bg-white transition-all font-extrabold">
                            </div>
                        </div>

                        <!-- Daily Shift Hours & Dynamic Duration Calculation -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                                    DAILY SHIFT DURATION <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">⏱️</span>
                                    <select id="shift_hours_select" name="shift_hours" onchange="syncSidebarValues()"
                                        class="w-full bg-[#FAF9F6] border border-slate-200 rounded-2xl pl-11 pr-5 py-4 text-slate-900 text-sm outline-none focus:border-rose-500 focus:bg-white transition-all font-bold appearance-none cursor-pointer">
                                        <option value="8" selected>8 Hours / Day (Standard Full Day)</option>
                                        <option value="4">4 Hours / Day (Half Day Shift)</option>
                                        <option value="5">5 Hours / Day (5-Hour Shift)</option>
                                        <option value="6">6 Hours / Day (6-Hour Shift)</option>
                                        <option value="10">10 Hours / Day (Extended Shift)</option>
                                        <option value="12">12 Hours / Day (Full Day + Overtime)</option>
                                    </select>
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none">▼</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                                    CALCULATED WORKLOAD COMMITMENT
                                </label>
                                <div id="calculated_duration_box" class="w-full bg-rose-50/70 border border-rose-200/80 rounded-2xl px-5 py-3.5 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-rose-500 text-base">📊</span>
                                        <span class="text-xs font-bold text-slate-600">Total Commitment:</span>
                                    </div>
                                    <span id="calculated_duration_display" class="text-xs sm:text-sm font-black text-rose-600 bg-white px-3 py-1 rounded-xl shadow-xs border border-rose-100">
                                        1 Day (8 Hours Full Day)
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Rate & Negotiation Price Section -->
                        <div class="bg-gradient-to-br from-amber-50/70 via-white to-rose-50/40 border border-amber-200/80 rounded-[28px] p-6 sm:p-7 space-y-6 shadow-xs">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-100/80 pb-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-base">💰</span>
                                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 uppercase tracking-wide">
                                            Pricing & Negotiation Preference
                                        </h3>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Set your offered rate per day. You can propose a negotiable price to discuss with the crew or offer a fixed price.
                                    </p>
                                </div>
                                <span class="text-[11px] font-black text-amber-800 bg-amber-100/90 px-3 py-1 rounded-full border border-amber-200 shrink-0 self-start sm:self-auto">
                                    Standard Rate: KES {{ number_format($baseDailyRate) }}/day
                                </span>
                            </div>

                            <!-- Rate Type Toggle Cards -->
                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-3">
                                    SELECT RATE TYPE <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Negotiable Option -->
                                    <div id="rate-type-card-negotiable" onclick="setRateType('negotiable')"
                                        class="cursor-pointer p-4 rounded-2xl border-2 border-amber-500 bg-amber-50/50 hover:bg-amber-50 transition-all flex items-start gap-3.5 shadow-xs">
                                        <div class="w-5 h-5 rounded-full border-2 border-amber-500 bg-amber-500 flex items-center justify-center shrink-0 mt-0.5 text-white text-[10px] font-black" id="rate-radio-negotiable">
                                            ✓
                                        </div>
                                        <div class="space-y-0.5">
                                            <div class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                                                <span>Negotiable Price</span>
                                                <span class="px-2 py-0.5 rounded-md bg-amber-200 text-amber-900 text-[9px] font-extrabold uppercase">Recommended</span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 leading-relaxed font-medium">
                                                Propose your offer. Crew can accept or discuss counter-offers directly with you in chat.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Fixed Option -->
                                    <div id="rate-type-card-fixed" onclick="setRateType('fixed')"
                                        class="cursor-pointer p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-slate-300 transition-all flex items-start gap-3.5">
                                        <div class="w-5 h-5 rounded-full border-2 border-slate-300 bg-white flex items-center justify-center shrink-0 mt-0.5 text-white text-[10px] font-black" id="rate-radio-fixed">
                                        </div>
                                        <div class="space-y-0.5">
                                            <div class="text-xs font-black text-slate-900">
                                                Fixed Rate Offer
                                            </div>
                                            <p class="text-[11px] text-slate-500 leading-relaxed font-medium">
                                                Lock in a firm price. Crew members will review and accept your set fixed rate without negotiation.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Negotiation Price Input & Live Calculation -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center pt-1">
                                <div>
                                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                                        <span id="rate_input_label">OFFERED NEGOTIATION RATE (PER DAY / CREW)</span> <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs font-black pointer-events-none">KES</span>
                                        <input type="number" id="offered_daily_rate" name="daily_rate_input" value="{{ old('daily_rate', $baseDailyRate) }}" min="500" step="100" required
                                            placeholder="{{ $baseDailyRate }}"
                                            oninput="syncSidebarValues()"
                                            class="w-full bg-[#FAF9F6] border border-slate-200 rounded-2xl pl-13 pr-5 py-4 text-slate-900 text-sm outline-none focus:border-amber-500 focus:bg-white transition-all font-black">
                                    </div>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-[11px] text-slate-400">Quick set:</span>
                                        <button type="button" onclick="setQuickRate({{ $baseDailyRate }})" class="text-[11px] font-bold text-amber-700 bg-amber-100/70 hover:bg-amber-100 px-2 py-0.5 rounded-md border border-amber-200 cursor-pointer">
                                            Standard (KES {{ number_format($baseDailyRate) }})
                                        </button>
                                        @if($baseDailyRate > 2500)
                                            <button type="button" onclick="setQuickRate({{ round($baseDailyRate * 0.85, -2) }})" class="text-[11px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 px-2 py-0.5 rounded-md border border-slate-200 cursor-pointer">
                                                Target Offer (KES {{ number_format(round($baseDailyRate * 0.85, -2)) }})
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                                        ESTIMATED TOTAL ESCROW BUDGET
                                    </label>
                                    <div class="w-full bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-4 space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-600">Total Commitment:</span>
                                            <span id="calculated_budget_display" class="text-sm font-black text-emerald-700 bg-white px-3 py-1 rounded-xl shadow-xs border border-emerald-100">
                                                KES {{ number_format($baseDailyRate) }}
                                            </span>
                                        </div>
                                        <p id="budget_breakdown_note" class="text-[10px] text-slate-400 font-medium">
                                            1 Day × 1 Crew Member @ KES {{ number_format($baseDailyRate) }}/day
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project / Event Description -->
                        <div>
                            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                                PROJECT / EVENT DESCRIPTION
                            </label>
                            <textarea id="requirements" name="requirements" rows="4"
                                placeholder="Briefly describe what's happening..."
                                class="w-full bg-[#FAF9F6] border border-slate-200 rounded-2xl p-5 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-rose-500 focus:bg-white transition-all">{{ old('requirements', request('requirements')) }}</textarea>
                        </div>

                        <!-- Crew Duties & Dress Code Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                                    CREW DUTIES
                                </label>
                                <input type="text" id="crew_duties" name="crew_duties" value="{{ old('crew_duties', 'Guest check-in, seating assistance') }}"
                                    placeholder="Guest check-in, seating assistance"
                                    oninput="syncSidebarValues()"
                                    class="w-full bg-[#FAF9F6] border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-rose-500 focus:bg-white transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                                    DRESS CODE
                                </label>
                                <input type="text" id="dress_code" name="dress_code" value="{{ old('dress_code', 'Black Suit / Formal') }}"
                                    placeholder="e.g. Black Suit / Formal"
                                    class="w-full bg-[#FAF9F6] border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 text-sm placeholder-slate-400 outline-none focus:border-rose-500 focus:bg-white transition-all">
                            </div>
                        </div>

                    </div>

                    <!-- Navigation Footer -->
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" onclick="goToStep(1)" class="text-slate-500 font-bold text-sm hover:text-slate-900 transition-all">
                            Back
                        </button>

                        <button type="button" onclick="goToStep(3)" class="px-8 py-4 rounded-full bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 text-white font-extrabold text-sm shadow-lg shadow-pink-500/25 hover:opacity-95 transition-all flex items-center gap-2 cursor-pointer">
                            <span>NEXT STEP</span>
                            <span>→</span>
                        </button>
                    </div>

                </div>

                <!-- Right Sidebar Column (Step 2 Summary) -->
                <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                    <div class="bg-white border border-slate-100 rounded-[32px] p-6 shadow-xl shadow-slate-200/40 space-y-6">
                        <div class="flex items-center gap-3">
                            <img src="{{ $avatarSrc }}" alt="{{ $crewName }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase text-slate-400 block tracking-wider">HIRING REQUEST</span>
                                <span class="text-xs font-extrabold text-rose-500 flex items-center gap-1">
                                    <span>✓</span> Step 1 Completed
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3.5 pt-2 border-t border-slate-100 text-xs">
                            <div class="flex justify-between items-center py-1">
                                <span class="text-slate-400 font-bold">Service</span>
                                <span class="font-extrabold text-slate-800" id="sidebar2-service">{{ $serviceStr }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-slate-400 font-bold">Date</span>
                                <span class="font-extrabold text-slate-800" id="sidebar2-date">{{ $eventDate }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-slate-400 font-bold">Duration</span>
                                <span class="font-extrabold text-rose-600" id="sidebar2-duration">8 Hours</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-slate-400 font-bold">Budget</span>
                                <span class="font-extrabold text-emerald-600" id="sidebar2-budget">KES {{ number_format($baseDailyRate) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-slate-400 font-bold">Email</span>
                                <span class="font-extrabold text-slate-800 truncate max-w-[180px]" id="sidebar2-email">client@example.com</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <!-- ========================================================= -->
            <!-- STEP 3: FILES (OPTIONAL - PDF PAGE 6 SPECIFICATION) -->
            <!-- ========================================================= -->
            <div id="step-container-3" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Main Form Card -->
                <div class="lg:col-span-8 bg-white border border-slate-100 rounded-[32px] p-8 sm:p-12 shadow-xl shadow-slate-200/40 space-y-8">
                    
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-block px-3.5 py-1 rounded-full bg-amber-50 border border-amber-200/60 text-amber-800 text-[11px] font-extrabold uppercase tracking-wider mb-3">
                                STEP 03 OF 04
                            </span>
                            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                                Anything you’d like me to see?
                            </h1>
                            <p class="text-slate-500 text-sm sm:text-base font-normal mt-2 leading-relaxed">
                                Share your event brief, run sheet, mood board, or any reference materials that will help our crew prepare.
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 font-extrabold text-[10px] uppercase tracking-wider shrink-0 hidden sm:inline-block">
                            OPTIONAL STEP
                        </span>
                    </div>

                    <!-- File Drop Zone -->
                    <div class="bg-[#FAF9F6]/60 border-2 border-dashed border-slate-200 rounded-[32px] p-10 text-center space-y-4 relative transition-all hover:border-rose-400">
                        <input type="file" id="attachment" name="attachment" onchange="handleFileSelected(this)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        
                        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl mx-auto shadow-sm">
                            📤
                        </div>

                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Drag & Drop files here</h3>
                            <p class="text-xs text-slate-400 font-medium mt-1">
                                Support for PDF, Images, and Documents up to 20MB
                            </p>
                        </div>

                        <div class="pt-2">
                            <span class="inline-block px-6 py-2.5 rounded-xl bg-slate-900 text-white font-extrabold text-xs shadow-md">
                                BROWSE FILES
                            </span>
                        </div>

                        <!-- Selected File Display Badge -->
                        <div id="file-name-preview" class="hidden p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold items-center justify-center gap-2">
                            <span>📄</span>
                            <span id="file-name-text">event_brief.pdf</span>
                            <span class="text-emerald-600">✓ Ready</span>
                        </div>
                    </div>

                    <!-- Suggestion Chips (PDF PAGE 6) -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Event Brief</span>
                        <span class="px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Mood Board</span>
                        <span class="px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">Venue Info</span>
                    </div>

                    <!-- Navigation Footer -->
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" onclick="goToStep(4)" class="text-rose-500 font-extrabold text-sm underline hover:text-rose-600 transition-all">
                            Skip for now →
                        </button>

                        <button type="button" onclick="goToStep(4)" class="px-8 py-4 rounded-full bg-gradient-to-r from-amber-400 via-rose-500 to-pink-500 text-white font-extrabold text-sm shadow-lg shadow-pink-500/25 hover:opacity-95 transition-all flex items-center gap-2 cursor-pointer">
                            <span>CONTINUE TO REVIEW</span>
                            <span>→</span>
                        </button>
                    </div>

                </div>

                <!-- Right Sidebar Column (Step 3 Summary) -->
                <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                    <div class="bg-white border border-slate-100 rounded-[32px] p-6 shadow-xl shadow-slate-200/40 space-y-6">
                        <div class="text-[11px] font-extrabold uppercase tracking-wider text-rose-500">SUMMARY</div>
                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-bold">Event</span>
                                <span class="font-extrabold text-slate-800" id="sidebar3-event">Tech Summit 2026</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-bold">Duties</span>
                                <span class="font-extrabold text-slate-800" id="sidebar3-duties">Registration Lead</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-bold">Duration</span>
                                <span class="font-extrabold text-rose-600" id="sidebar3-duration">8 Hours</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-bold">Budget</span>
                                <span class="font-extrabold text-emerald-600" id="sidebar3-budget">KES {{ number_format($baseDailyRate) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <!-- ========================================================= -->
            <!-- STEP 4: REVIEW & CONFIRM -->
            <!-- ========================================================= -->
            <div id="step-container-4" class="hidden max-w-5xl mx-auto space-y-8">
                
                <!-- Main Master Review Card -->
                <div class="bg-white border border-slate-100 rounded-[32px] p-6 sm:p-10 lg:p-12 shadow-xl shadow-slate-200/40 space-y-8">
                    
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
                        <div>
                            <span class="inline-block px-3.5 py-1 rounded-full bg-amber-50 border border-amber-200/60 text-amber-800 text-[11px] font-extrabold uppercase tracking-wider mb-2">
                                STEP 04 OF 04
                            </span>
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                                Review & Send Order Request
                            </h1>
                            <p class="text-slate-500 text-xs sm:text-sm font-normal mt-1 leading-relaxed">
                                Please review your event specifications, schedule commitment, and selected crew before submitting.
                            </p>
                        </div>
                        <div class="shrink-0 bg-rose-50 border border-rose-100 px-4 py-2.5 rounded-2xl flex items-center gap-2">
                            <span class="text-base">✨</span>
                            <span class="text-xs font-black text-rose-700">Almost Done</span>
                        </div>
                    </div>

                    <!-- 4-Column Specifications Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Project -->
                        <div class="bg-[#FAF9F6] border border-slate-200/70 rounded-2xl p-4 space-y-1">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">PROJECT / EVENT</span>
                            <div id="review-event" class="font-extrabold text-slate-900 text-sm truncate">Annual Tech Summit</div>
                            <div id="review-type" class="text-xs text-slate-500 font-semibold truncate">Corporate Gala</div>
                        </div>

                        <!-- Schedule & Duration -->
                        <div class="bg-[#FAF9F6] border border-slate-200/70 rounded-2xl p-4 space-y-1">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">SCHEDULE & DURATION</span>
                            <div id="review-date" class="font-extrabold text-slate-900 text-xs truncate">Select dates</div>
                            <div id="review-duration" class="font-black text-rose-600 bg-rose-100/70 border border-rose-200/80 px-2 py-0.5 rounded-lg text-[11px] inline-block mt-0.5">
                                8 Hours (1 Day)
                            </div>
                        </div>

                        <!-- Venue / Location -->
                        <div class="bg-[#FAF9F6] border border-slate-200/70 rounded-2xl p-4 space-y-1">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">LOCATION / VENUE</span>
                            <div id="review-location" class="font-extrabold text-slate-900 text-sm truncate">Nairobi, Kenya</div>
                            <div class="text-xs text-slate-400 font-medium">On-Site Event Crew</div>
                        </div>

                        <!-- Contact -->
                        <div class="bg-[#FAF9F6] border border-slate-200/70 rounded-2xl p-4 space-y-1">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">CLIENT CONTACT</span>
                            <div id="review-name" class="font-extrabold text-slate-900 text-sm truncate">Client</div>
                            <div id="review-email" class="text-xs text-slate-500 font-medium truncate">client@example.com</div>
                        </div>
                    </div>

                    <!-- Pricing & Negotiation Specification Box -->
                    <div class="bg-gradient-to-r from-amber-50/70 via-white to-emerald-50/70 border border-slate-200/90 rounded-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-700 border border-amber-200 flex items-center justify-center text-xl shrink-0">
                                💰
                            </div>
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">OFFERED RATE & PRICING</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span id="review-daily-rate" class="font-black text-slate-900 text-sm">KES {{ number_format($baseDailyRate) }} / Day</span>
                                    <span id="review-rate-type" class="text-[10px] font-black text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-md border border-amber-200">Negotiable Price</span>
                                </div>
                                <span class="text-[11px] text-slate-500 font-medium">Crew will review and accept your offer or discuss terms directly with you in chat.</span>
                            </div>
                        </div>

                        <div class="sm:text-right bg-white sm:bg-transparent p-3 sm:p-0 rounded-xl border sm:border-0 border-emerald-100 w-full sm:w-auto">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">ESTIMATED TOTAL BUDGET</span>
                            <div id="review-total-budget" class="text-base sm:text-lg font-black text-emerald-700">
                                KES {{ number_format($baseDailyRate) }} Total
                            </div>
                            <span id="review-budget-sub" class="text-[10px] text-slate-400 font-medium block">
                                1 Day × 1 Crew Member
                            </span>
                        </div>
                    </div>

                    <!-- Selected Crew Talent Showcase -->
                    <div class="bg-[#FAF9F6] border border-slate-200/80 rounded-2xl p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">SELECTED TALENT CREW</span>
                                <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700">
                                    {{ $profs->count() }} {{ $profs->count() === 1 ? 'Talent' : 'Talents' }}
                                </span>
                            </div>
                            <span class="text-xs font-bold text-slate-400">Step 4 of 4</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5 max-h-64 overflow-y-auto pr-1">
                            @forelse($profs as $p)
                                @php
                                    $pAvatar = $p->profile_photo ? get_storage_url($p->profile_photo) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400';
                                @endphp
                                <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 flex items-center gap-3 shadow-xs hover:border-rose-300 transition-all">
                                    <img src="{{ $pAvatar }}" alt="{{ $p->full_name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-black text-slate-900 truncate">{{ $p->full_name }}</div>
                                        <div class="text-[10px] text-slate-500 font-bold uppercase truncate">{{ $p->category ?: 'Event Usher' }}</div>
                                        <div class="text-[10px] text-emerald-600 font-black flex items-center gap-1 mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                            Available For Assignment
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full p-4 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700">
                                    General Event Crew Selection
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Duties & Brief Attachments Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-[#FAF9F6] border border-slate-200/70 rounded-2xl p-5 space-y-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">CREW DUTIES & NOTES</span>
                            <div id="review-duties" class="text-xs font-semibold text-slate-700 leading-relaxed bg-white p-3.5 rounded-xl border border-slate-100 min-h-[56px] flex items-center">
                                Guest check-in, seating assistance
                            </div>
                        </div>

                        <div class="bg-[#FAF9F6] border border-slate-200/70 rounded-2xl p-5 space-y-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">ATTACHED RUN SHEET / BRIEF</span>
                            <div class="bg-white p-3.5 rounded-xl border border-slate-100 min-h-[56px] flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-pink-500 text-white font-black text-[11px] flex items-center justify-center shrink-0 shadow-xs">
                                    FILE
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div id="review-file-name" class="text-xs font-black text-slate-900 truncate">No file attached</div>
                                    <div class="text-[10px] text-slate-400 font-medium">Optional supporting document</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Final Action Button & Navigation -->
                    <div class="pt-6 border-t border-slate-100 space-y-5">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <button type="button" onclick="goToStep(3)" class="text-slate-500 font-bold text-sm hover:text-slate-900 transition-all flex items-center gap-2 order-2 sm:order-1 cursor-pointer">
                                <span>←</span>
                                <span>Back to Edit Details</span>
                            </button>

                            <button type="submit" class="w-full sm:w-auto px-10 py-4 rounded-full bg-gradient-to-r from-orange-500 via-pink-500 to-rose-500 text-white font-extrabold text-sm sm:text-base shadow-xl shadow-pink-500/25 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 uppercase cursor-pointer order-1 sm:order-2">
                                <span>SEND HIRING REQUEST</span>
                                <span>🪄</span>
                            </button>
                        </div>

                        <p class="text-center text-xs text-slate-400 max-w-lg mx-auto leading-relaxed">
                            By sending, you agree to AfriCrew's terms. You'll be able to proceed through the hiring and secure payment process after crew accept your request.
                        </p>
                    </div>

                </div>

            </div>

        </form>

    </div>
</div>

<script>
let currentStep = 1;

function goToStep(step) {
    if (step > currentStep) {
        if (currentStep === 1) {
            const emailVal = document.getElementById('email_step1')?.value.trim();
            const nameVal = document.getElementById('full_name')?.value.trim();
            const phoneVal = document.getElementById('phone')?.value.trim();

            if (!emailVal || !emailVal.includes('@')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Email Required',
                    text: 'Please enter a valid email address to proceed with your hiring request.',
                    confirmButtonColor: '#F43F5E'
                });
                return;
            }
            if (!nameVal || !phoneVal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Contact Information Required',
                    text: 'Please provide your Full Name and Phone Number.',
                    confirmButtonColor: '#F43F5E'
                });
                return;
            }
        } else if (currentStep === 2) {
            const eventName = document.getElementById('event_name')?.value.trim();
            const location = document.getElementById('location')?.value.trim();

            if (!eventName) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Event Name Required',
                    text: 'Please specify the name of your event.',
                    confirmButtonColor: '#F43F5E'
                });
                return;
            }
            if (!location) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Location Required',
                    text: 'Please enter the location/venue of your event.',
                    confirmButtonColor: '#F43F5E'
                });
                return;
            }
        }
    }

    [1, 2, 3, 4].forEach(s => {
        const container = document.getElementById(`step-container-${s}`);
        const nav = document.getElementById(`step-nav-${s}`);
        const badge = document.getElementById(`step-nav-badge-${s}`);
        const text = document.getElementById(`step-nav-text-${s}`);

        if (container) {
            if (s === step) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        if (nav && badge && text) {
            if (s === step) {
                nav.className = "flex items-center gap-2 sm:gap-3 cursor-pointer group shrink-0 opacity-100";
                badge.className = "w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-r from-orange-500 via-pink-500 to-rose-500 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center shadow-md shadow-pink-500/20 transition-all";
                text.className = "text-xs sm:text-sm font-black text-slate-900 block leading-tight";
            } else if (s < step) {
                nav.className = "flex items-center gap-2 sm:gap-3 cursor-pointer group shrink-0 opacity-100";
                badge.className = "w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-emerald-500 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center transition-all";
                badge.textContent = '✓';
                text.className = "text-xs sm:text-sm font-bold text-slate-700 block leading-tight";
            } else {
                nav.className = "flex items-center gap-2 sm:gap-3 cursor-pointer group shrink-0 opacity-60 hover:opacity-100 transition-all";
                badge.className = "w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-rose-50 border border-rose-200 text-rose-500 font-extrabold text-xs sm:text-sm flex items-center justify-center transition-all";
                badge.textContent = s;
                text.className = "text-xs sm:text-sm font-bold text-slate-500 block leading-tight";
            }
        }
    });

    currentStep = step;
    syncSidebarValues();
    window.scrollTo({ top: 100, behavior: 'smooth' });
}

let currentRateType = 'negotiable';

function setRateType(type) {
    currentRateType = type;
    const cardNeg = document.getElementById('rate-type-card-negotiable');
    const cardFix = document.getElementById('rate-type-card-fixed');
    const radioNeg = document.getElementById('rate-radio-negotiable');
    const radioFix = document.getElementById('rate-radio-fixed');
    const inputLabel = document.getElementById('rate_input_label');

    if (type === 'negotiable') {
        if (cardNeg) cardNeg.className = "cursor-pointer p-4 rounded-2xl border-2 border-amber-500 bg-amber-50/50 hover:bg-amber-50 transition-all flex items-start gap-3.5 shadow-xs";
        if (cardFix) cardFix.className = "cursor-pointer p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-slate-300 transition-all flex items-start gap-3.5";
        if (radioNeg) { radioNeg.className = "w-5 h-5 rounded-full border-2 border-amber-500 bg-amber-500 flex items-center justify-center shrink-0 mt-0.5 text-white text-[10px] font-black"; radioNeg.textContent = '✓'; }
        if (radioFix) { radioFix.className = "w-5 h-5 rounded-full border-2 border-slate-300 bg-white flex items-center justify-center shrink-0 mt-0.5 text-white text-[10px] font-black"; radioFix.textContent = ''; }
        if (inputLabel) inputLabel.textContent = "OFFERED NEGOTIATION RATE (PER DAY / CREW)";
    } else {
        if (cardNeg) cardNeg.className = "cursor-pointer p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-slate-300 transition-all flex items-start gap-3.5";
        if (cardFix) cardFix.className = "cursor-pointer p-4 rounded-2xl border-2 border-amber-500 bg-amber-50/50 hover:bg-amber-50 transition-all flex items-start gap-3.5 shadow-xs";
        if (radioNeg) { radioNeg.className = "w-5 h-5 rounded-full border-2 border-slate-300 bg-white flex items-center justify-center shrink-0 mt-0.5 text-white text-[10px] font-black"; radioNeg.textContent = ''; }
        if (radioFix) { radioFix.className = "w-5 h-5 rounded-full border-2 border-amber-500 bg-amber-500 flex items-center justify-center shrink-0 mt-0.5 text-white text-[10px] font-black"; radioFix.textContent = '✓'; }
        if (inputLabel) inputLabel.textContent = "FIXED RATE (PER DAY / CREW)";
    }
    syncSidebarValues();
}

function setQuickRate(amount) {
    const rateInput = document.getElementById('offered_daily_rate');
    if (rateInput) {
        rateInput.value = amount;
        syncSidebarValues();
    }
}

let calculatedDaysCount = 1;

function getSelectedDaysCount() {
    if (window.fpStaffPicker && window.fpStaffPicker.selectedDates && window.fpStaffPicker.selectedDates.length > 0) {
        if (window.fpStaffPicker.selectedDates.length === 2) {
            const diffMs = Math.abs(window.fpStaffPicker.selectedDates[1].getTime() - window.fpStaffPicker.selectedDates[0].getTime());
            return Math.max(1, Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1);
        }
        return 1;
    }
    const dateInput = document.getElementById('event_date');
    if (dateInput && dateInput.value) {
        const val = dateInput.value;
        const match = val.match(/\((\d+)\s*Days?\)/i);
        if (match) {
            return parseInt(match[1]) || 1;
        }
        if (val.includes(' - ')) {
            const parts = val.split(' - ');
            const d1 = new Date(parts[0].trim());
            const d2 = new Date(parts[1].trim());
            if (!isNaN(d1.getTime()) && !isNaN(d2.getTime())) {
                const diffMs = Math.abs(d2.getTime() - d1.getTime());
                return Math.max(1, Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1);
            }
        }
    }
    return calculatedDaysCount || 1;
}

function getDailyHours() {
    const hoursSelect = document.getElementById('shift_hours_select');
    return hoursSelect ? (parseInt(hoursSelect.value) || 8) : 8;
}

function getDurationSummary() {
    const days = getSelectedDaysCount();
    const hoursPerDay = getDailyHours();
    const totalHours = days * hoursPerDay;

    if (days <= 1) {
        return {
            days: 1,
            hoursPerDay: hoursPerDay,
            totalHours: hoursPerDay,
            display: `${hoursPerDay} Hours (1 Day · Full Shift)`,
            badge: '1 Day',
            raw: `${hoursPerDay} Hours (1 Day)`
        };
    } else {
        return {
            days: days,
            hoursPerDay: hoursPerDay,
            totalHours: totalHours,
            display: `${days} Days (${hoursPerDay}h/day · ${totalHours} Hours Total)`,
            badge: `${days} Days`,
            raw: `${days} Days (${hoursPerDay}h/day · ${totalHours} Hours Total)`
        };
    }
}

function syncSidebarValues() {
    const email = document.getElementById('email_step1')?.value || 'client@example.com';
    const name = document.getElementById('full_name')?.value || 'Client';
    const eventName = document.getElementById('event_name')?.value || 'Annual Tech Summit';
    const eventType = document.getElementById('event_type')?.value || 'Corporate Gala';
    const duties = document.getElementById('crew_duties')?.value || 'Guest check-in, seating assistance';
    const dateVal = document.getElementById('event_date')?.value || '';
    const locVal = document.getElementById('location')?.value || 'Nairobi, Kenya';

    const dur = getDurationSummary();

    // Duration hidden input & display
    const hiddenDuration = document.getElementById('hidden_shift_duration');
    if (hiddenDuration) hiddenDuration.value = dur.raw;

    const eventDaysBadge = document.getElementById('event_days_badge');
    if (eventDaysBadge) eventDaysBadge.textContent = dur.badge;

    const calcDisplay = document.getElementById('calculated_duration_display');
    if (calcDisplay) calcDisplay.textContent = dur.display;

    // Pricing calculation
    const crewCount = {{ max(1, $profs->count()) }};
    const daysCount = getSelectedDaysCount();
    const dailyRateInput = document.getElementById('offered_daily_rate');
    const dailyRate = dailyRateInput ? (parseFloat(dailyRateInput.value) || {{ $baseDailyRate }}) : {{ $baseDailyRate }};
    const totalBudget = Math.round(dailyRate * daysCount * crewCount);

    // Update hidden inputs for backend submission
    const hiddenRateType = document.getElementById('hidden_rate_type');
    if (hiddenRateType) hiddenRateType.value = currentRateType;

    const hiddenBudget = document.getElementById('hidden_budget');
    if (hiddenBudget) hiddenBudget.value = totalBudget;

    const hiddenDailyRate = document.getElementById('hidden_daily_rate');
    if (hiddenDailyRate) hiddenDailyRate.value = dailyRate;

    // Step 2 Budget displays
    const calcBudgetDisplay = document.getElementById('calculated_budget_display');
    if (calcBudgetDisplay) calcBudgetDisplay.textContent = `KES ${totalBudget.toLocaleString()}`;

    const budgetBreakdownNote = document.getElementById('budget_breakdown_note');
    if (budgetBreakdownNote) {
        budgetBreakdownNote.textContent = `${daysCount} ${daysCount === 1 ? 'Day' : 'Days'} × ${crewCount} ${crewCount === 1 ? 'Crew Member' : 'Crew Members'} @ KES ${dailyRate.toLocaleString()}/day`;
    }

    // Step 4 Review elements
    const reviewEmail = document.getElementById('review-email');
    const reviewName = document.getElementById('review-name');
    const reviewEvent = document.getElementById('review-event');
    const reviewType = document.getElementById('review-type');
    const reviewDate = document.getElementById('review-date');
    const reviewDuration = document.getElementById('review-duration');
    const reviewLocation = document.getElementById('review-location');
    const reviewDuties = document.getElementById('review-duties');
    const reviewDailyRate = document.getElementById('review-daily-rate');
    const reviewRateType = document.getElementById('review-rate-type');
    const reviewTotalBudget = document.getElementById('review-total-budget');
    const reviewBudgetSub = document.getElementById('review-budget-sub');

    if (reviewEmail) reviewEmail.textContent = email;
    if (reviewName) reviewName.textContent = name;
    if (reviewEvent) reviewEvent.textContent = eventName;
    if (reviewType) reviewType.textContent = eventType;
    if (reviewDate) reviewDate.textContent = dateVal || 'Dates to be selected';
    if (reviewDuration) reviewDuration.textContent = dur.display;
    if (reviewLocation) reviewLocation.textContent = locVal;
    if (reviewDuties) reviewDuties.textContent = duties;

    if (reviewDailyRate) reviewDailyRate.textContent = `KES ${dailyRate.toLocaleString()} / Day`;
    if (reviewRateType) {
        reviewRateType.textContent = currentRateType === 'negotiable' ? 'Negotiable Price' : 'Fixed Rate';
        reviewRateType.className = currentRateType === 'negotiable' 
            ? 'text-[10px] font-black text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-md border border-amber-200' 
            : 'text-[10px] font-black text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-md border border-emerald-200';
    }
    if (reviewTotalBudget) reviewTotalBudget.textContent = `KES ${totalBudget.toLocaleString()} Total`;
    if (reviewBudgetSub) {
        reviewBudgetSub.textContent = `${daysCount} ${daysCount === 1 ? 'Day' : 'Days'} × ${crewCount} ${crewCount === 1 ? 'Crew Member' : 'Crew Members'}`;
    }

    // Sidebar elements
    const sidebarEmail = document.getElementById('sidebar2-email');
    const sidebarEvent = document.getElementById('sidebar3-event');
    const sidebarDuties = document.getElementById('sidebar3-duties');
    const sidebarDuration = document.getElementById('sidebar-duration');
    const sidebar2Duration = document.getElementById('sidebar2-duration');
    const sidebar3Duration = document.getElementById('sidebar3-duration');
    const sidebarBudget = document.getElementById('sidebar-budget');
    const sidebar2Budget = document.getElementById('sidebar2-budget');
    const sidebar3Budget = document.getElementById('sidebar3-budget');

    if (sidebarEmail) sidebarEmail.textContent = email;
    if (sidebarEvent) sidebarEvent.textContent = eventName;
    if (sidebarDuties) sidebarDuties.textContent = duties;
    if (sidebarDuration) sidebarDuration.textContent = dur.display;
    if (sidebar2Duration) sidebar2Duration.textContent = dur.display;
    if (sidebar3Duration) sidebar3Duration.textContent = dur.display;

    if (sidebarBudget) sidebarBudget.textContent = `KES ${totalBudget.toLocaleString()} (${currentRateType === 'negotiable' ? 'Negotiable' : 'Fixed'})`;
    if (sidebar2Budget) sidebar2Budget.textContent = `KES ${totalBudget.toLocaleString()}`;
    if (sidebar3Budget) sidebar3Budget.textContent = `KES ${totalBudget.toLocaleString()}`;

    if (dateVal) {
        const sidebarDate = document.getElementById('sidebar-date');
        const sidebar2Date = document.getElementById('sidebar2-date');
        if (sidebarDate) sidebarDate.textContent = dateVal;
        if (sidebar2Date) sidebar2Date.textContent = dateVal;
    }

    if (locVal) {
        const sidebarLoc = document.getElementById('sidebar-location');
        if (sidebarLoc) sidebarLoc.textContent = locVal;
    }
}

function handleFileSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const preview = document.getElementById('file-name-preview');
        const text = document.getElementById('file-name-text');
        const reviewFile = document.getElementById('review-file-name');

        if (text) text.textContent = file.name;
        if (reviewFile) reviewFile.textContent = file.name;
        if (preview) preview.classList.remove('hidden');
    }
}
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof flatpickr !== 'undefined') {
        window.fpStaffPicker = flatpickr('#event_date', {
            mode: 'range',
            dateFormat: 'd M Y',
            minDate: new Date(Date.now() + 6 * 60 * 60 * 1000),
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const diffMs = Math.abs(selectedDates[1].getTime() - selectedDates[0].getTime());
                    calculatedDaysCount = Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1;
                    const startStr = instance.formatDate(selectedDates[0], 'd M Y');
                    const endStr = instance.formatDate(selectedDates[1], 'd M Y');
                    instance.input.value = `${startStr} - ${endStr} (${calculatedDaysCount} ${calculatedDaysCount === 1 ? 'Day' : 'Days'})`;
                } else if (selectedDates.length === 1) {
                    calculatedDaysCount = 1;
                    const startStr = instance.formatDate(selectedDates[0], 'd M Y');
                    instance.input.value = `${startStr} (1 Day)`;
                }
                syncSidebarValues();
            }
        });
    }
    syncSidebarValues();
});
</script>
@endsection
