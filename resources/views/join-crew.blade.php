@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.12.2/build/css/intlTelInput.css">
<style>
    .iti {
        display: block;
        width: 100%;
    }

    #crew-phone-input {
        box-sizing: border-box;
        height: 48px;
        padding-left: 112px !important;
        padding-right: 16px !important;
    }

    .iti__country-container {
        padding: 4px;
    }

    .iti__selected-country {
        height: 100%;
        border-right: 1px solid #e2e8f0;
        border-radius: 0.55rem 0 0 0.55rem;
    }
</style>

<section class="max-w-xl mx-auto px-6 py-16">
    <!-- Header -->
    <div class="mb-8 text-center">
        <span class="inline-block px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-widest">
            Work With AfriCrew
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mt-3">Create Crew Account</h1>
        <p class="text-slate-600 mt-2 text-sm">Register your crew account in seconds to begin your application.</p>
    </div>

    <!-- Error Summary -->
    @if($errors->any())
        <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-5 text-rose-800 text-xs shadow-sm">
            <div class="font-bold mb-1.5 text-rose-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Please fix the following issues:</span>
            </div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Registration Card -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 shadow-xl space-y-6">

        <!-- Social Sign Up Options -->
        <div class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('social.redirect', ['provider' => 'google', 'type' => 'staff']) }}" class="w-full bg-white hover:bg-slate-50 text-slate-700 font-extrabold text-xs border border-slate-200/90 rounded-2xl py-3.5 px-4 flex items-center justify-center gap-2.5 shadow-xs hover:shadow-sm transition-all cursor-pointer text-decoration-none">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Sign up with Google</span>
                </a>

                <a href="{{ route('social.redirect', ['provider' => 'facebook', 'type' => 'staff']) }}" class="w-full bg-[#1877F2] hover:bg-[#166FE5] text-white font-extrabold text-xs border border-transparent rounded-2xl py-3.5 px-4 flex items-center justify-center gap-2.5 shadow-xs hover:shadow-sm transition-all cursor-pointer text-decoration-none">
                    <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>Sign up with Facebook</span>
                </a>
            </div>

            <div class="relative py-1">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200/80"></div>
                </div>
                <div class="relative flex justify-center text-[10px] uppercase font-extrabold tracking-widest text-slate-400">
                    <span class="bg-white px-3">or register with email below</span>
                </div>
            </div>
        </div>

        <form id="crew-registration-form" method="POST" action="{{ route('crew.store') }}" class="space-y-5">
            @csrf

            <!-- Full Name -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                    Full Name <span class="text-amber-600">*</span>
                </label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Jane Doe"
                    class="w-full bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
            </div>

            <!-- Username / Handle with Live Short-Link Preview -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                    Username / Handle <span class="text-amber-600">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="username-input" name="username" value="{{ old('username') }}" required placeholder="e.g. janedoe"
                        oninput="onUsernameInput(this.value)"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                    <div id="username-status-spinner" class="hidden absolute right-3 top-3.5 text-xs text-slate-400">
                        ⏳
                    </div>
                </div>

                <!-- Live Short-Link Preview Badge & Tooltip -->
                <div class="mt-2 p-3 bg-amber-50/80 border border-amber-200/80 rounded-xl flex items-start gap-2.5 text-xs">
                    <span class="text-base leading-none">🔗</span>
                    <div class="flex-1 min-w-0">
                        <div class="font-extrabold text-slate-800 flex items-center gap-1.5 flex-wrap">
                            <span>Your Short Link Preview:</span>
                            <span id="shortlink-preview" class="font-mono text-amber-700 bg-white px-2 py-0.5 rounded border border-amber-200">
                                mycv.ac/<span id="shortlink-handle">{{ old('username') ?: 'username' }}</span>
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-1 leading-snug group relative cursor-help inline-block">
                            ℹ️ Hover to learn when your link becomes active
                            <span class="hidden group-hover:block absolute left-0 bottom-full mb-1 w-64 bg-slate-900 text-white text-[11px] p-2.5 rounded-lg shadow-xl z-20">
                                Your Digital CV link becomes active after AfriCrew approves your profile.
                            </span>
                        </p>
                        <div id="username-validation-msg" class="text-[11px] font-bold mt-1"></div>
                    </div>
                </div>
            </div>

            <!-- Primary Category -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                    Primary Category <span class="text-amber-600">*</span>
                </label>
                <select name="category" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                    @if(isset($categories) && $categories->count() > 0)
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>
                                {{ $cat->icon ? $cat->icon . ' ' : '' }}{{ $cat->name }}
                            </option>
                        @endforeach
                    @else
                        <option value="Event Ushers">⚡ Event Ushers</option>
                        <option value="VIP Hostesses">👑 VIP Hostesses</option>
                        <option value="Security & Bouncers">🛡️ Security & Bouncers</option>
                        <option value="Mixologists & Bartenders">🍸 Mixologists & Bartenders</option>
                        <option value="Protocol Officers">🏛️ Protocol Officers</option>
                        <option value="Brand Ambassadors">🌟 Brand Ambassadors</option>
                        <option value="Sound & Stage Technicians">🔊 Sound & Stage Technicians</option>
                        <option value="Event Photographers">📷 Event Photographers</option>
                    @endif
                </select>
            </div>

            <!-- Email Address & Phone Number -->
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Email Address <span class="text-amber-600">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. jane@example.com"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Phone Number <span class="text-amber-600">*</span>
                    </label>
                    <input type="tel" id="crew-phone-input" required inputmode="tel" autocomplete="tel" placeholder="Enter your phone number"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-sm rounded-xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                    <input type="hidden" id="crew-phone-number" name="phone" value="{{ old('phone') }}">
                    <input type="hidden" id="crew-phone-country" name="phone_country_iso2" value="{{ old('phone_country_iso2', 'KE') }}">
                    <p id="crew-phone-error" class="mt-1 text-xs text-rose-600" aria-live="polite"></p>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                    Password <span class="text-amber-600">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="password" name="password" data-password-strength required minlength="8" placeholder="At least 8 characters"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-sm rounded-xl px-4 pr-12 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                    @include('components.password-visibility', ['inputId' => 'password'])
                </div>
                @include('components.password-strength-checklist', ['inputId' => 'password'])
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                    Confirm Password <span class="text-amber-600">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Re-enter your password"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-sm rounded-xl px-4 pr-12 py-3 outline-none focus:border-amber-500 focus:bg-white transition-colors">
                    @include('components.password-visibility', ['inputId' => 'password_confirmation', 'label' => 'password confirmation'])
                </div>
            </div>

            <!-- Accessible Terms & Conditions Scrollable Box -->
            <div class="space-y-2 pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between text-xs font-extrabold text-slate-700">
                    <span>📜 Terms & Conditions Summary</span>
                    <span id="scroll-hint-badge" class="text-[10px] text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">
                        Scroll to bottom to unlock checkbox
                    </span>
                </div>

                <div id="terms-scroll-container" onscroll="checkTermsScroll(this)"
                    class="h-32 overflow-y-auto bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs text-slate-600 space-y-2 leading-relaxed">
                    <p class="font-bold text-slate-800">AfriCrew Member Platform Agreement Summary:</p>
                    <p>1. <strong>Verification & Approval:</strong> Registering an account allows you to build your profile & resume. Profiles are reviewed by AfriCrew administrators before public discovery and booking enablement.</p>
                    <p>2. <strong>Professional Conduct:</strong> You agree to provide accurate qualifications, work history, and referee details. False information may lead to account suspension.</p>
                    <p>3. <strong>Quotations & Fees:</strong> Your rates serve as reference figures when preparing event quotations. Final fees depend on agreed client contracts.</p>
                    <p>4. <strong>Privacy:</strong> Contact information, Date of Birth, and referee contact details remain strictly private and protected under our Privacy Policy.</p>
                    <p class="font-bold text-amber-700">--- End of Terms Summary. Thank you for reviewing our terms! ---</p>
                </div>

                <div class="pt-2">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" id="terms-checkbox" name="terms" value="1" disabled onchange="toggleSubmitButton()"
                            class="mt-1 w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-400 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="text-xs text-slate-700 leading-relaxed font-semibold">
                            I have read and accept the <a href="{{ route('terms-conditions') }}" target="_blank" class="text-amber-600 font-bold hover:underline">Terms & Conditions</a> and <a href="{{ route('privacy-policy') }}" target="_blank" class="text-amber-600 font-bold hover:underline">Privacy Policy</a>.
                        </span>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submit-btn" disabled class="w-full py-4 px-6 bg-slate-900 text-white font-extrabold text-base rounded-2xl shadow-xl transition-all flex items-center justify-center gap-2 mt-4 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-800">
                <span>Create Account</span>
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
            Already have a crew account?
            <a href="{{ route('staff.login') }}" class="font-bold text-amber-600 hover:underline ml-1">Log in here</a>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.12.2/build/js/intlTelInput.min.js"></script>
<script>
    let usernameTimeout = null;

    function onUsernameInput(val) {
        const handleSpan = document.getElementById('shortlink-handle');
        const validationMsg = document.getElementById('username-validation-msg');
        const spinner = document.getElementById('username-status-spinner');
        
        const cleanVal = val.trim().toLowerCase().replace(/[^a-z0-9_\-]/g, '');
        handleSpan.innerText = cleanVal || 'username';

        if (!val.trim()) {
            validationMsg.innerText = '';
            return;
        }

        spinner.classList.remove('hidden');
        clearTimeout(usernameTimeout);

        usernameTimeout = setTimeout(() => {
            fetch(`/api/check-username?username=${encodeURIComponent(val.trim())}`)
                .then(res => res.json())
                .then(data => {
                    spinner.classList.add('hidden');
                    if (data.valid) {
                        validationMsg.className = 'text-[11px] font-bold text-emerald-600 mt-1';
                        validationMsg.innerText = '✓ ' + data.message;
                    } else {
                        validationMsg.className = 'text-[11px] font-bold text-rose-600 mt-1';
                        validationMsg.innerText = '✕ ' + data.message;
                    }
                })
                .catch(() => {
                    spinner.classList.add('hidden');
                });
        }, 400);
    }

    function checkTermsScroll(elem) {
        const scrollBottom = elem.scrollHeight - elem.scrollTop - elem.clientHeight;
        const checkbox = document.getElementById('terms-checkbox');
        const hintBadge = document.getElementById('scroll-hint-badge');

        if (scrollBottom <= 10) {
            checkbox.disabled = false;
            hintBadge.className = 'text-[10px] text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full font-bold';
            hintBadge.innerText = '✓ Checkbox unlocked! Please tick to agree';
        }
    }

    function toggleSubmitButton() {
        const checkbox = document.getElementById('terms-checkbox');
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = !checkbox.checked;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const phoneInput = document.getElementById('crew-phone-input');
        const phoneNumber = document.getElementById('crew-phone-number');
        const phoneCountry = document.getElementById('crew-phone-country');
        const phoneError = document.getElementById('crew-phone-error');
        const form = document.getElementById('crew-registration-form');

        if (!window.intlTelInput) {
            phoneError.textContent = 'The country selector could not be loaded. Please refresh the page and try again.';
            form.querySelector('[type="submit"]').disabled = true;
            return;
        }

        const phoneInputInstance = window.intlTelInput(phoneInput, {
            initialCountry: phoneCountry.value.toLowerCase(),
            separateDialCode: true,
            nationalMode: true,
            countrySearch: true,
            showFlags: true,
            validationNumberType: null,
            loadUtils: () => import('https://cdn.jsdelivr.net/npm/intl-tel-input@25.12.2/build/js/utils.js'),
        });

        phoneInputInstance.promise.then(() => {
            if (phoneNumber.value) {
                phoneInputInstance.setNumber(phoneNumber.value);
            }
        });

        phoneInput.addEventListener('countrychange', () => {
            phoneCountry.value = phoneInputInstance.getSelectedCountryData().iso2.toUpperCase();
        });

        phoneInput.addEventListener('input', () => {
            phoneInput.setCustomValidity('');
            phoneError.textContent = '';
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            await phoneInputInstance.promise;

            if (!phoneInput.value.trim() || !phoneInputInstance.isValidNumber()) {
                phoneInput.setCustomValidity('Enter a valid phone number for the selected country.');
                phoneInput.reportValidity();
                phoneError.textContent = 'Enter a valid phone number for the selected country.';
                return;
            }

            phoneNumber.value = phoneInputInstance.getNumber();
            phoneCountry.value = phoneInputInstance.getSelectedCountryData().iso2.toUpperCase();
            form.submit();
        });
    });
</script>
@endsection
