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
            <input type="hidden" name="currency" value="KES">
            <input type="hidden" name="rate_type" value="fixed">
            <input type="hidden" name="budget" value="3500">


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
                        </div>
                    </div>
                </div>

            </div>


            <!-- ========================================================= -->
            <!-- STEP 4: REVIEW & CONFIRM (PDF PAGE 7 & 8 SPECIFICATIONS) -->
            <!-- ========================================================= -->
            <div id="step-container-4" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Main Form Card -->
                <div class="lg:col-span-8 bg-white border border-slate-100 rounded-[32px] p-8 sm:p-12 shadow-xl shadow-slate-200/40 space-y-8">
                    
                    <div>
                        <span class="inline-block px-3.5 py-1 rounded-full bg-amber-50 border border-amber-200/60 text-amber-800 text-[11px] font-extrabold uppercase tracking-wider mb-3">
                            STEP 04 OF 04
                        </span>
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            Review & Send Order Request
                        </h1>
                        <p class="text-slate-500 text-sm sm:text-base font-normal mt-2 leading-relaxed">
                                <span class="text-slate-400 font-bold">Project</span>
                                <span id="review-event" class="font-extrabold text-slate-900">Annual Tech Summit</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-400 font-bold">Duration</span>
                                <span class="font-extrabold text-slate-900">8 Hours (Full Day)</span>
                            </div>
                        </div>

                        <!-- Talent Selection Box (Single or Multi-Crew List) -->
                        <div class="p-6 rounded-2xl bg-[#FAF9F6] border border-slate-100 space-y-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">TALENT SELECTION</span>
                            <div class="space-y-2 pt-1 max-h-36 overflow-y-auto">
                                @forelse($profs as $p)
                                    @php
                                        $pAvatar = $p->profile_photo ? get_storage_url($p->profile_photo) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400';
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $pAvatar }}" alt="{{ $p->full_name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                                        <div>
                                            <div class="text-xs font-extrabold text-slate-900">{{ $p->full_name }}</div>
                                            <div class="text-[10px] text-slate-400 font-medium uppercase">{{ $p->category ?: 'Event Usher' }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-xs font-bold text-slate-700">General Event Crew</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Attachments Box -->
                        <div class="p-6 rounded-2xl bg-[#FAF9F6] border border-slate-100 space-y-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">ATTACHMENTS</span>
                            <div class="flex items-center gap-3 pt-1">
                                <div class="w-8 h-8 rounded-lg bg-rose-500 text-white font-bold text-xs flex items-center justify-center">FILE</div>
                                <div>
                                    <div id="review-file-name" class="text-xs font-extrabold text-slate-900">No file attached</div>
                                    <div class="text-[10px] text-slate-400 font-medium">Optional</div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Final Action Button & Disclaimer -->
                    <div class="text-center pt-6 space-y-4">
                        <button type="submit" class="w-full sm:w-auto px-12 py-4 rounded-full bg-gradient-to-r from-orange-500 via-pink-500 to-rose-500 text-white font-extrabold text-base shadow-2xl shadow-pink-500/30 hover:scale-105 transition-all flex items-center justify-center gap-2 mx-auto uppercase">
                            <span>SEND HIRING REQUEST</span>
                            <span>🪄</span>
                        </button>

                        <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
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

function syncSidebarValues() {
    const email = document.getElementById('email_step1')?.value || 'client@example.com';
    const name = document.getElementById('full_name')?.value || 'Client';
    const eventName = document.getElementById('event_name')?.value || 'Annual Tech Summit';
    const duties = document.getElementById('crew_duties')?.value || 'Registration Lead';
    const dateVal = document.getElementById('event_date')?.value || '';
    const locVal = document.getElementById('location')?.value || '';

    const reviewEmail = document.getElementById('review-email');
    const reviewName = document.getElementById('review-name');
    const reviewEvent = document.getElementById('review-event');
    const sidebarEmail = document.getElementById('sidebar2-email');
    const sidebarEvent = document.getElementById('sidebar3-event');
    const sidebarDuties = document.getElementById('sidebar3-duties');

    if (reviewEmail) reviewEmail.textContent = email;
    if (reviewName) reviewName.textContent = name;
    if (reviewEvent) reviewEvent.textContent = eventName;
    if (sidebarEmail) sidebarEmail.textContent = email;
    if (sidebarEvent) sidebarEvent.textContent = eventName;
    if (sidebarDuties) sidebarDuties.textContent = duties;

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
        flatpickr('#event_date', {
            mode: 'range',
            dateFormat: 'd M Y',
            minDate: 'today',
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const diffMs = Math.abs(selectedDates[1].getTime() - selectedDates[0].getTime());
                    const daysCount = Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1;
                    const startStr = instance.formatDate(selectedDates[0], 'd M Y');
                    const endStr = instance.formatDate(selectedDates[1], 'd M Y');
                    instance.input.value = `${startStr} - ${endStr} (${daysCount} ${daysCount === 1 ? 'Day' : 'Days'})`;
                    const badge = document.getElementById('event_days_badge');
                    if (badge) badge.textContent = `${daysCount} ${daysCount === 1 ? 'Day' : 'Days'}`;
                } else if (selectedDates.length === 1) {
                    const startStr = instance.formatDate(selectedDates[0], 'd M Y');
                    instance.input.value = `${startStr} (1 Day)`;
                    const badge = document.getElementById('event_days_badge');
                    if (badge) badge.textContent = '1 Day';
                }
                syncSidebarValues();
            }
        });
    }
    syncSidebarValues();
});
</script>
@endsection
