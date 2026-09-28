@extends('admin.layout')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.professionals.show', $professional) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition-colors">
        <span>← Back to Staff Details</span>
    </a>
    <a href="{{ route('crew.show', $professional->id) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-xs shadow-xs transition-all flex items-center gap-1 text-decoration-none">
        <span>👁️ View Live Public Profile</span>
    </a>
</div>

<div class="mb-8">
    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Edit Crew Profile & Capabilities</h1>
    <p class="text-slate-500 text-sm mt-1">Update details for <span class="font-extrabold text-slate-800">{{ $professional->full_name }}</span>. Changes made here will immediately sync to the public profile at <code class="text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded font-bold">/crew/{{ $professional->id }}</code>.</p>
</div>

<!-- Errors Alert -->
@if($errors->any())
    <div class="mb-8 rounded-2xl bg-rose-50 border border-rose-200 p-6 text-rose-800 text-sm shadow-sm">
        <div class="font-bold mb-2 text-rose-900 flex items-center gap-2">
            <span>⚠️ Validation errors detected:</span>
        </div>
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.professionals.update', $professional) }}" enctype="multipart/form-data" class="space-y-8 max-w-5xl pb-16">
    @csrf
    @method('PUT')

    <!-- Section 1: Basic Information -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
        <h2 class="text-lg font-black text-slate-900 flex items-center gap-3">
            <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-800 text-xs flex items-center justify-center font-black">1</span>
            <span>Personal & Contact Information</span>
        </h2>
        
        <div class="grid sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Full Name *</label>
                <input type="text" name="full_name" value="{{ old('full_name', $professional->full_name) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email Address *</label>
                <input type="email" name="email" value="{{ old('email', $professional->email) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Phone Number *</label>
                <input type="text" name="phone" value="{{ old('phone', $professional->phone) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Primary Category *</label>
                <select name="category" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}" @selected(old('category', $professional->category) == $cat->name)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">City *</label>
                <input type="text" name="city" value="{{ old('city', $professional->city) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Country *</label>
                <input type="text" name="country" value="{{ old('country', $professional->country) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Years of Experience *</label>
                <input type="number" name="experience_years" value="{{ old('experience_years', $professional->experience_years) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Verification Status *</label>
                <select name="status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
                    <option value="approved" @selected(old('status', $professional->status)=='approved')>Approved</option>
                    <option value="pending" @selected(old('status', $professional->status)=='pending')>Pending</option>
                    <option value="suspended" @selected(old('status', $professional->status)=='suspended')>Suspended</option>
                    <option value="deactivated" @selected(old('status', $professional->status)=='deactivated')>Deactivated</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Section 2: Key Skills & What I Can Help You With -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-lg font-black text-slate-900 flex items-center gap-3">
                <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-800 text-xs flex items-center justify-center font-black">2</span>
                <span>Key Skills & What I Can Help You With</span>
            </h2>
            <span class="text-[11px] font-black uppercase text-rose-500 bg-rose-50 px-3 py-1 rounded-full border border-rose-200">Public Frontend Section</span>
        </div>

        <!-- Key Skills Input -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Key Skills (Comma Separated)</label>
            <p class="text-xs text-slate-500 mb-2">Enter skills displayed as badges (e.g., VIP Protocol, Guest Registration, Bilingual, Stage Hosting)</p>
            @php
                $skillsVal = is_array($professional->skills) ? implode(', ', $professional->skills) : $professional->skills;
            @endphp
            <input type="text" name="skills" value="{{ old('skills', $skillsVal) }}" placeholder="e.g. VIP Protocol, Registration, Crowd Control" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
        </div>

        <!-- What I Can Help You With (Services Cards) -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">What I Can Help You With (Capabilities / Service Cards)</label>
                <p class="text-xs text-slate-500 mb-3">Add service cards displayed under "What I Can Help You With" on the public profile <code class="text-amber-600 bg-amber-50 px-1 py-0.5 rounded font-bold">/crew/{{ $professional->id }}</code>.</p>
            </div>

            <div id="admin-services-container" class="space-y-4">
                @php $servicesList = $professional->services ?: []; @endphp
                @if(!empty($servicesList) && count($servicesList) > 0)
                    @foreach($servicesList as $index => $svc)
                        @php $sItem = is_string($svc) ? json_decode($svc, true) : $svc; @endphp
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 relative">
                            <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                                <span class="text-[10px] font-black uppercase text-slate-500">Service Card #{{ $index + 1 }}</span>
                                <button type="button" onclick="this.closest('.p-4').remove()" class="text-rose-600 font-extrabold text-xs hover:underline">✕ Remove Card</button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                <div class="sm:col-span-3">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Emoji Icon</label>
                                    <input type="text" name="services[{{ $index }}][icon]" value="{{ $sItem['icon'] ?? '👥' }}" placeholder="👥" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-center text-slate-900 font-bold">
                                </div>
                                <div class="sm:col-span-5">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Service Title *</label>
                                    <input type="text" name="services[{{ $index }}][title]" value="{{ $sItem['title'] ?? ($sItem['name'] ?? '') }}" placeholder="e.g. Corporate Guest Check-in" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-900 font-bold">
                                </div>
                                <div class="sm:col-span-4">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Tagline / Badge</label>
                                    <input type="text" name="services[{{ $index }}][tagline]" value="{{ $sItem['tagline'] ?? '' }}" placeholder="e.g. VIP Protocol" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-900 font-bold">
                                </div>
                                <div class="sm:col-span-12">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Description</label>
                                    <textarea name="services[{{ $index }}][description]" rows="2" placeholder="Describe the capabilities provided for this service..." class="w-full bg-white border border-slate-200 rounded-lg p-2.5 text-slate-900 text-xs font-medium">{{ $sItem['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <button type="button" onclick="addAdminServiceCard()" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs transition-all flex items-center gap-2">
                <span>➕ Add Capabilities Service Card</span>
            </button>
        </div>

        <!-- Biography & Highlight Quote -->
        <div class="grid sm:grid-cols-1 gap-6 pt-4 border-t border-slate-100">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Highlight Quote</label>
                <input type="text" name="highlight_quote" value="{{ old('highlight_quote', $professional->highlight_quote) }}" placeholder='e.g. "Guests always remember how they were treated."' class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Biography / Summary (About)</label>
                <textarea name="about" rows="5" placeholder="Detailed professional bio..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-slate-900 text-sm font-medium focus:outline-none focus:border-amber-500">{{ old('about', $professional->about) }}</textarea>
            </div>
        </div>
    </div>

    <!-- Section 3: Rates & Pricing Packages -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
        <h2 class="text-lg font-black text-slate-900 flex items-center gap-3">
            <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-800 text-xs flex items-center justify-center font-black">3</span>
            <span>Labour Charges & Rates</span>
        </h2>

        <div class="grid sm:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Hourly Rate ($ / KES)</label>
                <input type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', $professional->hourly_rate) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">1 Day Event Rate (KES)</label>
                <input type="number" step="0.01" name="one_day_rate" value="{{ old('one_day_rate', $professional->one_day_rate) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">2 Day Package Rate (KES)</label>
                <input type="number" step="0.01" name="two_day_rate" value="{{ old('two_day_rate', $professional->two_day_rate) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm font-bold focus:outline-none focus:border-amber-500">
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-4">
        <button type="submit" class="px-8 py-4 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm rounded-2xl shadow-lg transition-all">
            ✓ Save Profile & Capabilities
        </button>
        <a href="{{ route('admin.professionals.show', $professional) }}" class="px-6 py-4 bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-2xl hover:bg-slate-50 transition-all text-decoration-none">
            Cancel
        </a>
    </div>
</form>

<script>
    let adminServiceIndex = {{ !empty($servicesList) ? count($servicesList) : 0 }};

    function addAdminServiceCard() {
        const container = document.getElementById('admin-services-container');
        const cardHtml = `
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 relative">
                <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                    <span class="text-[10px] font-black uppercase text-slate-500">Service Card #${adminServiceIndex + 1}</span>
                    <button type="button" onclick="this.closest('.p-4').remove()" class="text-rose-600 font-extrabold text-xs hover:underline">✕ Remove Card</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">Emoji Icon</label>
                        <input type="text" name="services[${adminServiceIndex}][icon]" value="👑" placeholder="👑" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-center text-slate-900 font-bold">
                    </div>
                    <div class="sm:col-span-5">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">Service Title *</label>
                        <input type="text" name="services[${adminServiceIndex}][title]" placeholder="e.g. VIP Ushering Protocol" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-900 font-bold">
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">Tagline / Badge</label>
                        <input type="text" name="services[${adminServiceIndex}][tagline]" placeholder="e.g. Premium Hospitality" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-900 font-bold">
                    </div>
                    <div class="sm:col-span-12">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">Description</label>
                        <textarea name="services[${adminServiceIndex}][description]" rows="2" placeholder="Describe what is provided in this service..." class="w-full bg-white border border-slate-200 rounded-lg p-2.5 text-slate-900 text-xs font-medium"></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', cardHtml);
        adminServiceIndex++;
    }
</script>
@endsection
