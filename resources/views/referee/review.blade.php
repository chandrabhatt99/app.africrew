@extends('layouts.app')

@section('content')
@php
    $refName = $referee['name'] ?? 'Referee';
    $refFirstName = strtok($refName, ' ') ?: $refName;
    $crewFullName = $professional->full_name;
    $crewFirstName = strtok($crewFullName, ' ') ?: $crewFullName;
    $companyOrProject = $referee['organization'] ?? ($referee['relationship'] ?? 'Company / Project');
    $crewRole = $referee['crew_role'] ?? ($professional->category ?: 'Crew Member');
    $period = $referee['period'] ?? 'recent projects';
    $currentStatus = strtolower($referee['status'] ?? 'requested');
@endphp

<div class="min-h-screen bg-slate-50 py-8 sm:py-12 px-4 sm:px-6">
    <div class="max-w-2xl mx-auto space-y-6">

        <!-- Top Brand Card -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-5">
            <div class="flex items-center justify-between flex-wrap gap-3 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500 to-rose-500 text-white font-black flex items-center justify-center text-lg shadow-sm">
                        🛡️
                    </span>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 block">AfriCrew Verification Hub</span>
                        <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight m-0">Reference Verification Request</h1>
                    </div>
                </div>

                @if($currentStatus === 'verified')
                    <span class="px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-black flex items-center gap-1.5">
                        <span>✓</span> Verified Reference
                    </span>
                @elseif($currentStatus === 'unverified')
                    <span class="px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-black flex items-center gap-1.5">
                        <span>✕</span> Unverified
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-black flex items-center gap-1.5">
                        <span>⏳</span> Pending Review
                    </span>
                @endif
            </div>

            <div class="space-y-2">
                <p class="text-sm sm:text-base text-slate-700 font-semibold m-0 leading-relaxed">
                    Hello <span class="text-slate-950 font-black">{{ $refFirstName }}</span>,
                </p>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed m-0">
                    <strong class="text-slate-900">{{ $crewFullName }}</strong> has added you as a professional referee on their <strong>AfriCrew Digital CV</strong> and indicated that you worked together.
                </p>
            </div>

            <!-- Stated Details Box -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 m-0">
                    Stated Experience Details:
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-white border border-slate-200">
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Candidate</span>
                        <span class="font-black text-slate-900 block truncate">{{ $crewFullName }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-white border border-slate-200">
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Company / Project</span>
                        <span class="font-black text-slate-900 block truncate">{{ $companyOrProject }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-white border border-slate-200">
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">{{ $crewFirstName }}’s Role</span>
                        <span class="font-black text-slate-900 block truncate">{{ $crewRole }}</span>
                    </div>
                </div>
                @if(!empty($period) && $period !== 'recent projects')
                    <div class="text-[11px] text-slate-500 font-medium pt-1">
                        ⏱️ Indicated Period: <strong class="text-slate-800">{{ $period }}</strong>
                    </div>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
                @foreach($errors->all() as $err)
                    <div>• {{ $err }}</div>
                @endforeach
            </div>
        @endif

        <!-- Action Form Container -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

            <div class="space-y-1">
                <h2 class="text-base sm:text-lg font-black text-slate-950 tracking-tight m-0">
                    How would you like to respond?
                </h2>
                <p class="text-xs text-slate-500 m-0">
                    Please confirm the details, suggest a correction, or let us know if you cannot verify this work experience.
                </p>
            </div>

            <!-- Decision Switcher Tabs -->
            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-2xl">
                <button type="button" id="tab-btn-verify" onclick="switchDecisionTab('verify')" class="flex-1 py-2.5 px-4 rounded-xl font-black text-xs transition-all flex items-center justify-center gap-2 bg-white text-slate-900 shadow-xs">
                    <span>✓</span> Confirm & Verify Reference
                </button>
                <button type="button" id="tab-btn-unverify" onclick="switchDecisionTab('unverify')" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900">
                    <span>✕</span> Unable to Verify / Decline
                </button>
            </div>

            <!-- FORM 1: VERIFY -->
            <form id="form-verify" method="POST" action="{{ route('referee.review.submit', ['token' => $token]) }}" class="space-y-5">
                @csrf
                <input type="hidden" name="action" value="verify">

                <!-- Correction / Confirmation details -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1">
                        Correction or notes on role / dates <span class="text-slate-400 font-normal lowercase">(Optional)</span>
                    </label>
                    <input type="text" name="suggested_correction" value="{{ old('suggested_correction', $referee['suggested_correction'] ?? '') }}" placeholder="e.g. Worked as Stage Tech from Feb 2014 to Dec 2015" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-semibold outline-none focus:border-amber-500 focus:bg-white transition-all">
                </div>

                <!-- Star Rating -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1">
                        Rate {{ $crewFirstName }}’s Work & Reliability <span class="text-slate-400 font-normal lowercase">(Optional)</span>
                    </label>
                    <div class="flex items-center gap-2 pt-1" id="star-rating-container">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setRating({{ $i }})" class="star-btn text-2xl text-slate-300 hover:text-amber-400 transition-colors cursor-pointer" data-star="{{ $i }}" title="{{ $i }} of 5 stars">
                                ★
                            </button>
                        @endfor
                        <span id="rating-label" class="text-xs font-bold text-slate-600 ml-2">Select a rating</span>
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', $referee['rating'] ?? '') }}">
                </div>

                <!-- Short Comment / Feedback -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1">
                        Short Comment / Feedback <span class="text-slate-400 font-normal lowercase">(Optional)</span>
                    </label>
                    <textarea name="comments" rows="3" placeholder="Share a few words about your experience working with {{ $crewFirstName }} (e.g. punctual, dependable, proactive under pressure)..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs font-semibold outline-none focus:border-amber-500 focus:bg-white leading-relaxed">{{ old('comments', $referee['comments'] ?? '') }}</textarea>
                </div>

                <!-- PUBLIC DISPLAY DISCLAIMER CHECKBOX -->
                <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/70 border border-amber-200/90 space-y-3">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="show_on_cv" value="1" {{ old('show_on_cv', !isset($referee['show_on_cv']) || !empty($referee['show_on_cv'])) ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded text-[#FF7A00] border-slate-300 focus:ring-[#FF7A00] cursor-pointer">
                        <div class="space-y-1">
                            <span class="text-xs font-black text-slate-900 block">
                                Show my reference on {{ $crewFirstName }}’s digital CV
                            </span>
                            <p class="text-[11px] text-slate-600 font-normal leading-relaxed m-0">
                                I allow AfriCrew to publish my name, professional title, organisation, rating and comments, where provided, on {{ $crewFullName }}’s public digital CV to support this work reference. I understand that anyone viewing the profile can see these details. My email address and phone number will remain private. I can withdraw permission for public display by contacting <a href="mailto:mail@africrew.com" class="text-amber-800 font-bold underline">mail@africrew.com</a>.
                            </p>
                        </div>
                    </label>
                    <div class="text-[10px] text-slate-500 italic pl-7">
                        💡 If left unchecked, your verification remains strictly private for AfriCrew Admin vetting and will not appear publicly.
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-6 rounded-full bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-500 hover:to-teal-600 text-white font-black text-xs sm:text-sm tracking-wide shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <span>✓</span>
                        <span>Confirm & Verify Reference</span>
                    </button>
                </div>
            </form>

            <!-- FORM 2: UNVERIFIED -->
            <form id="form-unverify" method="POST" action="{{ route('referee.review.submit', ['token' => $token]) }}" class="space-y-5 hidden">
                @csrf
                <input type="hidden" name="action" value="unverify">

                <div class="p-4 rounded-2xl bg-rose-50/80 border border-rose-200 text-xs text-rose-950 font-medium space-y-1">
                    <span class="font-black text-rose-900">⚠️ Unable to Verify Reference:</span>
                    <p class="m-0 leading-relaxed text-rose-800">
                        Please indicate why you are unable to confirm this reference. This ensures our records remain transparent and compliant with our vetting guidelines while respecting your preferences.
                    </p>
                </div>

                <!-- Unverified Reason Selection (2 Mandatory Options) -->
                <div class="space-y-3">
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-900">
                        Please select a reason:
                    </label>

                    <!-- Option 1: Employment Not Confirmed -->
                    <label class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:border-rose-300 hover:bg-rose-50/30 transition-all cursor-pointer">
                        <input type="radio" name="unverified_reason" value="employment_not_confirmed" {{ old('unverified_reason', $referee['unverified_reason'] ?? '') === 'employment_not_confirmed' ? 'checked' : '' }} required class="mt-1 w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500 cursor-pointer">
                        <div class="space-y-0.5">
                            <span class="text-xs font-black text-slate-900 block">
                                1. Employment Not Confirmed
                            </span>
                            <span class="text-xs text-slate-600 block leading-relaxed">
                                The referee confirms that the crew member did not work with them or at their organization.
                            </span>
                        </div>
                    </label>

                    <!-- Option 2: Referee Name Removal Request -->
                    <label class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:border-rose-300 hover:bg-rose-50/30 transition-all cursor-pointer">
                        <input type="radio" name="unverified_reason" value="name_removal_request" {{ old('unverified_reason', $referee['unverified_reason'] ?? '') === 'name_removal_request' ? 'checked' : '' }} class="mt-1 w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500 cursor-pointer">
                        <div class="space-y-0.5">
                            <span class="text-xs font-black text-slate-900 block">
                                2. Referee Name Removal Request
                            </span>
                            <span class="text-xs text-slate-600 block leading-relaxed">
                                The referee does not wish to have their name included in the crew member’s CV/resume as a referee.
                            </span>
                        </div>
                    </label>
                </div>

                <!-- Optional Details / Notes -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1">
                        Additional Notes <span class="text-slate-400 font-normal lowercase">(Optional)</span>
                    </label>
                    <textarea name="unverified_notes" rows="2" placeholder="Provide any additional context for the AfriCrew admin team..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs font-semibold outline-none focus:border-rose-500 focus:bg-white leading-relaxed">{{ old('unverified_notes', $referee['unverified_notes'] ?? '') }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-6 rounded-full bg-slate-900 hover:bg-rose-950 text-white font-black text-xs sm:text-sm tracking-wide shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <span>✕</span>
                        <span>Submit as Unverified</span>
                    </button>
                </div>
            </form>

        </div>

        <div class="text-center text-xs text-slate-400 space-y-1">
            <p class="m-0">© {{ date('Y') }} AfriCrew. Vetted Professional Event Crew Platform.</p>
            <p class="m-0">Questions? Contact <a href="mailto:mail@africrew.com" class="text-slate-600 underline">mail@africrew.com</a></p>
        </div>

    </div>
</div>

<script>
function switchDecisionTab(type) {
    const btnVerify = document.getElementById('tab-btn-verify');
    const btnUnverify = document.getElementById('tab-btn-unverify');
    const formVerify = document.getElementById('form-verify');
    const formUnverify = document.getElementById('form-unverify');

    if (type === 'verify') {
        btnVerify.className = 'flex-1 py-2.5 px-4 rounded-xl font-black text-xs transition-all flex items-center justify-center gap-2 bg-white text-slate-900 shadow-xs';
        btnUnverify.className = 'flex-1 py-2.5 px-4 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900';
        formVerify.classList.remove('hidden');
        formUnverify.classList.add('hidden');
    } else {
        btnUnverify.className = 'flex-1 py-2.5 px-4 rounded-xl font-black text-xs transition-all flex items-center justify-center gap-2 bg-white text-rose-700 shadow-xs';
        btnVerify.className = 'flex-1 py-2.5 px-4 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900';
        formUnverify.classList.remove('hidden');
        formVerify.classList.add('hidden');
    }
}

function setRating(rating) {
    document.getElementById('rating-input').value = rating;
    const stars = document.querySelectorAll('.star-btn');
    stars.forEach(s => {
        const val = parseInt(s.getAttribute('data-star'));
        if (val <= rating) {
            s.classList.remove('text-slate-300');
            s.classList.add('text-amber-400');
        } else {
            s.classList.remove('text-amber-400');
            s.classList.add('text-slate-300');
        }
    });

    const labels = {
        1: '1 Star — Needs improvement',
        2: '2 Stars — Fair performance',
        3: '3 Stars — Good work',
        4: '4 Stars — Highly recommended',
        5: '5 Stars — Outstanding & Exceptional'
    };
    const labelEl = document.getElementById('rating-label');
    if (labelEl && labels[rating]) {
        labelEl.innerText = labels[rating];
        labelEl.className = 'text-xs font-black text-amber-800 ml-2';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const initialRating = parseInt(document.getElementById('rating-input').value || 0);
    if (initialRating > 0) {
        setRating(initialRating);
    }
});
</script>
@endsection
