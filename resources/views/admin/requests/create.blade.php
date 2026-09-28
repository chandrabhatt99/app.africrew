@extends('admin.layout')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.requests.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        <span>Back to Crew Requests</span>
    </a>
</div>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Create Crew Request</h1>
    <p class="text-slate-500 text-sm mt-1">Directly add client event details and crew requirements into the system.</p>
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

<form method="POST" action="{{ route('admin.requests.store') }}" enctype="multipart/form-data" class="space-y-8 max-w-4xl">
    @csrf

    <!-- Client Info -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 md:p-8 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-900 mb-6 flex items-center gap-3">
            <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-800 text-xs flex items-center justify-center font-black">1</span>
            <span>Client Contact Details</span>
        </h2>
        <div class="grid sm:grid-cols-2 gap-6">
            @include('partials.input', ['name'=>'full_name','label'=>'Client Full Name','required'=>true])
            @include('partials.input', ['name'=>'company_name','label'=>'Company / Organization'])
            @include('partials.input', ['name'=>'email','label'=>'Client Email','type'=>'email','required'=>true])
            @include('partials.input', ['name'=>'phone','label'=>'Client Phone','required'=>true])
        </div>
    </div>

    <!-- Event & Staffing Specs -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 md:p-8 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-900 mb-6 flex items-center gap-3">
            <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-800 text-xs flex items-center justify-center font-black">2</span>
            <span>Event Specification & Category</span>
        </h2>

        <div class="grid sm:grid-cols-2 gap-6">
            @include('partials.input', ['name'=>'event_name','label'=>'Event Name','required'=>true])

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                    Crew Category <span class="text-amber-600">*</span>
                </label>
                <select name="category" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                    <option value="">Select Category</option>
                    <option value="Ushers" @selected(old('category')=='Ushers')>Ushers</option>
                    <option value="Host / Hostess" @selected(old('category')=='Host / Hostess')>Host / Hostess</option>
                    <option value="Brand Ambassadors" @selected(old('category')=='Brand Ambassadors')>Brand Ambassadors</option>
                    <option value="Event Security" @selected(old('category')=='Event Security')>Event Security</option>
                    <option value="Wait Crew" @selected(old('category')=='Wait Crew')>Wait Crew</option>
                    <option value="Promotional Crew" @selected(old('category')=='Promotional Crew')>Promotional Crew</option>
                    <option value="Other" @selected(old('category')=='Other')>Other</option>
                </select>
            </div>

            @include('partials.input', ['name'=>'staff_count','label'=>'Crew Count Required','type'=>'number','required'=>true])
            @include('partials.input', ['name'=>'event_date','label'=>'Event Date','type'=>'date','required'=>true])
            
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                    Shift Duration <span class="text-amber-600">*</span>
                </label>
                <select name="shift_duration" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                    <option value="1_day" @selected(old('shift_duration')=='1_day')>1 Day</option>
                    <option value="2_days" @selected(old('shift_duration')=='2_days')>2 Days</option>
                    <option value="3_days" @selected(old('shift_duration')=='3_days')>3 Days</option>
                    <option value="4_days" @selected(old('shift_duration')=='4_days')>4 Days</option>
                    <option value="5_days" @selected(old('shift_duration')=='5_days')>5 Days</option>
                    <option value="multi_day" @selected(old('shift_duration')=='multi_day')>6+ Days (Multi-Day)</option>
                </select>
            </div>

            @include('partials.input', ['name'=>'location','label'=>'Venue / Location','required'=>true])
            @include('partials.input', ['name'=>'budget','label'=>'Budget (KES / USD)','type'=>'number'])

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                    Initial Request Status <span class="text-amber-600">*</span>
                </label>
                <select name="status" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                    <option value="new" @selected(old('status')=='new')>New</option>
                    <option value="under_review" @selected(old('status')=='under_review')>Under Review</option>
                    <option value="staff_matching" @selected(old('status')=='staff_matching')>Crew Matching</option>
                    <option value="shortlisted" @selected(old('status')=='shortlisted')>Shortlisted</option>
                    <option value="assigned" @selected(old('status')=='assigned')>Assigned</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Requirements & Notes</label>
                <textarea name="requirements" rows="4" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all" placeholder="Enter special instructions or dress code...">{{ old('requirements') }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Upload Brief Document (Optional)</label>
                <input type="file" name="attachment" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 text-sm cursor-pointer">
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex items-center gap-4">
        <button type="submit" class="px-8 py-4 bg-slate-900 text-gold-400 font-extrabold text-sm rounded-2xl shadow-lg shadow-slate-900/10 hover:bg-slate-800 transition-all">
            Create Crew Request
        </button>
        <a href="{{ route('admin.requests.index') }}" class="px-6 py-4 bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-2xl hover:bg-slate-50 transition-all">
            Cancel
        </a>
    </div>
</form>
@endsection
