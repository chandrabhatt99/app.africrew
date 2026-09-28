@extends('admin.layout')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.requests.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        <span>Back to Crew Requests</span>
    </a>
</div>

<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $staffingRequest->event_name }}</h1>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                {{ $staffingRequest->category }}
            </span>
        </div>
        <p class="text-slate-500 text-sm mt-1">Submitted by <strong>{{ $staffingRequest->full_name }}</strong> · Location: {{ $staffingRequest->location }}</p>
    </div>

    <!-- Status Change Form -->
    <form method="POST" action="{{ route('admin.requests.status', $staffingRequest) }}" class="flex items-center gap-3 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm">
        @csrf
        @method('PATCH')
        <label class="text-xs font-bold uppercase tracking-wider text-slate-500 pl-3">Status:</label>
        <select name="status" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-semibold rounded-xl px-3 py-2 focus:outline-none focus:border-amber-500">
            @foreach(['new','under_review','staff_matching','shortlisted','assigned','completed','cancelled'] as $status)
                <option value="{{ $status }}" @selected($staffingRequest->status===$status)>
                    {{ str_replace('_', ' ', ucfirst($status)) }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-slate-900 text-gold-400 font-bold text-xs px-4 py-2 rounded-xl hover:bg-slate-800 transition-all">
            Update
        </button>
    </form>
</div>

<div class="grid lg:grid-cols-3 gap-8">
    
    <!-- Details Panel -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-slate-200/80 rounded-3xl p-7 shadow-sm">
            <h2 class="font-extrabold text-slate-900 text-xl mb-6 pb-4 border-b border-slate-100">Client & Event Overview</h2>
            
            <div class="grid sm:grid-cols-2 gap-6 text-sm">
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Client Name</span>
                    <span class="font-bold text-slate-900">{{ $staffingRequest->full_name }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Company / Organization</span>
                    <span class="font-bold text-slate-900">{{ $staffingRequest->company_name ?: 'Individual Client' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Email Address</span>
                    <a href="mailto:{{ $staffingRequest->email }}" class="font-bold text-amber-700 hover:underline">{{ $staffingRequest->email }}</a>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Phone Number</span>
                    <a href="tel:{{ $staffingRequest->phone }}" class="font-bold text-slate-900 hover:underline">{{ $staffingRequest->phone }}</a>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Event Date</span>
                    <span class="font-bold text-slate-900">{{ $staffingRequest->event_date ? $staffingRequest->event_date->format('d M Y (D)') : 'N/A' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Shift Duration</span>
                    <span class="font-bold text-slate-900">{{ str_replace('_', ' ', ucfirst($staffingRequest->shift_duration ?? '1 Day')) }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Crew Required</span>
                    <span class="font-bold text-slate-900 text-base">{{ $staffingRequest->staff_count }} {{ $staffingRequest->category }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Budget</span>
                    <span class="font-bold text-slate-900">{{ $staffingRequest->budget ? '$'.number_format($staffingRequest->budget, 2) : 'Flexible' }}</span>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-100">
                <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Requirements & Brief Notes</span>
                <p class="text-slate-700 text-sm leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-2xl border border-slate-200/60">
                    {{ $staffingRequest->requirements ?: 'No additional requirements specified.' }}
                </p>
            </div>

            <!-- Client Payment to Admin Box -->
            <div class="mt-6 pt-6 border-t border-slate-100 bg-amber-50/50 p-5 rounded-2xl border border-amber-200/80">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-base font-black text-slate-900">💳 Client Payment to Admin</span>
                            @if($staffingRequest->payment_status === 'paid_to_admin')
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-200 text-amber-900 text-xs font-black">
                                    🔒 Paid to Admin (In Escrow)
                                </span>
                            @elseif($staffingRequest->payment_status === 'released_to_crew')
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-200 text-emerald-900 text-xs font-black">
                                    ✓ Funds Released to Crew Wallet
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-rose-200 text-rose-900 text-xs font-black">
                                    ⏳ Unpaid
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-600 mt-1">
                            @if($staffingRequest->payment_status === 'paid_to_admin')
                                Payment of <strong>KES {{ number_format($staffingRequest->payment_amount, 2) }}</strong> received by Admin via {{ strtoupper($staffingRequest->payment_method) }}. Funds held in Escrow until project is marked <strong>COMPLETED ("Done")</strong>.
                            @elseif($staffingRequest->payment_status === 'released_to_crew')
                                Project completed! Escrow funds have been released into assigned crew available wallets.
                            @else
                                Client has not yet completed payment to Admin. Client should pay directly to Admin (no direct payment to crew).
                            @endif
                        </p>
                    </div>

                    @if($staffingRequest->payment_status === 'unpaid')
                        <form method="POST" action="{{ route('admin.payments.markPaid', $staffingRequest) }}" class="flex items-center gap-2 shrink-0">
                            @csrf
                            <input type="number" step="0.01" name="payment_amount" value="{{ $staffingRequest->budget ?: 5000 }}" class="w-28 text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 font-bold focus:outline-none">
                            <select name="payment_method" class="text-xs bg-white border border-slate-300 rounded-xl px-2.5 py-2 font-bold focus:outline-none">
                                <option value="mpesa">M-Pesa</option>
                                <option value="bank_transfer">Bank</option>
                                <option value="card">Card</option>
                                <option value="cash_admin">Cash</option>
                            </select>
                            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-xs px-4 py-2 rounded-xl shadow-sm transition-all whitespace-nowrap">
                                Record Payment Received
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Currently Assigned Crew Members -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-7 shadow-sm">
            <h2 class="font-extrabold text-slate-900 text-xl mb-4">Assigned Crew Members ({{ $staffingRequest->assignments->count() }}/{{ $staffingRequest->staff_count }})</h2>
            
            @if($staffingRequest->assignments->isEmpty())
                <p class="text-xs text-slate-400 italic">No crew members have been assigned to this event request yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($staffingRequest->assignments as $assignment)
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div>
                                <div class="font-extrabold text-slate-900 text-sm">{{ $assignment->professional->full_name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $assignment->professional->email }} · {{ $assignment->professional->phone }} · {{ $assignment->professional->city }}</div>
                                @if($assignment->notes)
                                    <div class="text-xs text-amber-800 bg-amber-50 p-2 rounded-xl border border-amber-200 mt-2">
                                        Note: {{ $assignment->notes }}
                                    </div>
                                @endif
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold border capitalize 
                                {{ $assignment->status === 'accepted' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-amber-100 text-amber-800 border-amber-200' }}">
                                {{ $assignment->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Match Vetted Crew Panel -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-7 shadow-sm h-fit">
        <h2 class="font-extrabold text-slate-900 text-xl mb-1">Match & Assign Crew</h2>
        <p class="text-slate-500 text-xs mb-6">Verified professionals available in <strong>{{ $staffingRequest->category }}</strong></p>

        <div class="space-y-4">
            @forelse($professionals as $p)
                <form method="POST" action="{{ route('admin.requests.assign', $staffingRequest) }}" class="border border-slate-200 rounded-2xl p-4 hover:border-amber-400 transition-all bg-slate-50/50">
                    @csrf
                    <input type="hidden" name="professional_id" value="{{ $p->id }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">{{ $p->full_name }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $p->category }} · {{ $p->experience_years }} yrs exp · {{ $p->city }}</div>
                            <div class="text-xs text-amber-700 font-bold mt-1">${{ number_format($p->hourly_rate ?? 25, 2) }}/hr</div>
                        </div>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-bold">Approved</span>
                    </div>

                    <div class="mt-3">
                        <input type="text" name="notes" placeholder="Optional shift assignment note..." class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3 py-1.5 focus:outline-none focus:border-amber-500 mb-2">
                    </div>

                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-gold-400 rounded-xl py-2 font-bold text-xs shadow-sm transition-all">
                        Assign To Event Request
                    </button>
                </form>
            @empty
                <div class="text-center py-8 text-slate-500 text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-200 p-4">
                    No approved professionals in category "{{ $staffingRequest->category }}" yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
