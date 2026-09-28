@extends('admin.layout')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.professionals.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        <span>Back to Vetted Crew Database</span>
    </a>
</div>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Onboard New Crew Member</h1>
    <p class="text-slate-500 text-sm mt-1">Register and verify a new professional directly into the AfriCrew talent pool.</p>
</div>

<!-- Errors Alert -->
@if($errors->any())
    <div class="mb-8 rounded-2xl bg-rose-50 border border-rose-200 p-6 text-rose-800 text-sm shadow-sm">
        <div class="font-bold mb-2 text-rose-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Validation errors detected:</span>
        </div>
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.professionals.store') }}" enctype="multipart/form-data" class="space-y-8 max-w-4xl">
    @csrf

    <!-- Personal Info -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 md:p-8 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-900 mb-6 flex items-center gap-3">
            <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-800 text-xs flex items-center justify-center font-black">1</span>
            <span>Personal & Account Information</span>
        </h2>
        <div class="grid sm:grid-cols-2 gap-6">
            @include('partials.input', ['name'=>'full_name','label'=>'Full Name','required'=>true])
            @include('partials.input', ['name'=>'email','label'=>'Email Address','type'=>'email','required'=>true])
            @include('partials.input', ['name'=>'phone','label'=>'Phone Number','required'=>true])
            @include('partials.input', ['name'=>'password','label'=>'Initial Password','type'=>'password','required'=>true])
            @include('partials.input', ['name'=>'date_of_birth','label'=>'Date of Birth','type'=>'date'])
            @include('partials.input', ['name'=>'gender','label'=>'Gender'])
            @include('partials.input', ['name'=>'city','label'=>'City','required'=>true])
            @include('partials.input', ['name'=>'state','label'=>'State'])
            @include('partials.input', ['name'=>'country','label'=>'Country','required'=>true])
            @include('partials.input', ['name'=>'languages','label'=>'Languages Spoken (comma separated)'])
        </div>
    </div>

    <!-- Experience & Status -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 md:p-8 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-900 mb-6 flex items-center gap-3">
            <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-800 text-xs flex items-center justify-center font-black">2</span>
            <span>Professional Category & Onboarding Status</span>
        </h2>

        <div class="grid sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                    Primary Crew Category <span class="text-amber-600">*</span>
                </label>
                <select name="category" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                    <option value="">Select Category</option>
                    <option value="Ushers" @selected(old('category')=='Ushers')>Ushers</option>
                    <option value="Host / Hostess" @selected(old('category')=='Host / Hostess')>Host / Hostess</option>
                    <option value="Anchor" @selected(old('category')=='Anchor')>Anchor</option>
                    <option value="DJ" @selected(old('category')=='DJ')>DJ</option>
                    <option value="Security Crew" @selected(old('category')=='Security Crew')>Security Crew</option>
                    <option value="Brand Ambassador" @selected(old('category')=='Brand Ambassador')>Brand Ambassador</option>
                    <option value="Wait Crew" @selected(old('category')=='Wait Crew')>Wait Crew</option>
                    <option value="Other" @selected(old('category')=='Other')>Other</option>
                </select>
            </div>

            @include('partials.input', ['name'=>'experience_years','label'=>'Years of Experience','type'=>'number','required'=>true])
            @include('partials.input', ['name'=>'hourly_rate','label'=>'Hourly Rate ($)','type'=>'number'])
            @include('partials.input', ['name'=>'full_day_rate','label'=>'Full-Day Rate ($)','type'=>'number'])

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                    Availability Status <span class="text-amber-600">*</span>
                </label>
                <select name="availability" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                    <option value="available" @selected(old('availability')=='available')>Immediately Available</option>
                    <option value="this_week" @selected(old('availability')=='this_week')>Available This Week</option>
                    <option value="this_month" @selected(old('availability')=='this_month')>Available This Month</option>
                    <option value="unavailable" @selected(old('availability')=='unavailable')>Currently Unavailable</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                    Verification Approval Status <span class="text-amber-600">*</span>
                </label>
                <select name="status" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                    <option value="approved" @selected(old('status', 'approved')=='approved')>Approved (Ready to assign)</option>
                    <option value="pending" @selected(old('status')=='pending')>Pending Verification</option>
                    <option value="suspended" @selected(old('status')=='suspended')>Suspended</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Key Skills & What I Can Help You With (Comma Separated)</label>
                <textarea name="skills" rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all" placeholder="Enter skills displayed on public profile, e.g., VIP Protocol, Guest Registration, Bilingual, Stage MC">{{ old('skills') }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">About / Bio</label>
                <textarea name="about" rows="4" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all" placeholder="Enter crew member background summary...">{{ old('about') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Upload Verification Files -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 md:p-8 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-900 mb-6 flex items-center gap-3">
            <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-800 text-xs flex items-center justify-center font-black">3</span>
            <span>Attachments & Verification Files (Optional)</span>
        </h2>

        <div class="grid sm:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Profile Photo</label>
                <input type="file" name="profile_photo" accept="image/*" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 text-sm cursor-pointer">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Resume / CV</label>
                <input type="file" name="resume" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 text-sm cursor-pointer">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Government ID</label>
                <input type="file" name="government_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 text-sm cursor-pointer">
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex items-center gap-4">
        <button type="submit" class="px-8 py-4 bg-slate-900 text-gold-400 font-extrabold text-sm rounded-2xl shadow-lg shadow-slate-900/10 hover:bg-slate-800 transition-all">
            Onboard Crew Member
        </button>
        <a href="{{ route('admin.professionals.index') }}" class="px-6 py-4 bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-2xl hover:bg-slate-50 transition-all">
            Cancel
        </a>
    </div>
</form>
@endsection
