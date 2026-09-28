@extends('layouts.staff')

@section('content')
<div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
    <div class="border-b border-slate-200 pb-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">Support Desk & Dispute Center</h1>
            <p class="text-xs text-slate-500 mt-1">Submit tickets for payment queries, shift issues, or account assistance.</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-800 font-black text-xs">
            🎫 Ticket Desk
        </span>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 font-bold text-xs">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Ticket Submission Form (Col Span 5) -->
        <div class="lg:col-span-5 bg-slate-50 border border-slate-200 p-6 rounded-2xl space-y-4">
            <h3 class="text-sm font-black text-slate-900">Submit New Support Ticket</h3>
            <form method="POST" action="{{ route('professional.support.store') }}" class="space-y-4 text-xs font-bold">
                @csrf
                <div>
                    <label class="block text-slate-600 mb-1">Subject</label>
                    <input type="text" name="subject" placeholder="e.g. Payment inquiry for Shift #12" required class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-600 mb-1">Category</label>
                        <select name="category" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none">
                            <option value="Payments">Payments & Wallet</option>
                            <option value="Shifts">Shifts & Venue</option>
                            <option value="Account">Account Verification</option>
                            <option value="Dispute">Booking Dispute</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1">Priority</label>
                        <select name="priority" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-600 mb-1">Description / Details</label>
                    <textarea name="description" rows="4" placeholder="Describe your issue or question in detail..." required class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500"></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl shadow-xs">
                    Submit Ticket
                </button>
            </form>
        </div>

        <!-- Right: Ticket History List (Col Span 7) -->
        <div class="lg:col-span-7 space-y-4">
            <h3 class="text-sm font-black text-slate-900">My Support Tickets</h3>
            
            <div class="space-y-3">
                @forelse($tickets as $tck)
                    <div class="border border-slate-200 rounded-2xl p-4 space-y-2 bg-white shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-900">#{{ $tck->ticket_number }} - {{ $tck->subject }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $tck->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">
                                {{ $tck->status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 font-semibold">{{ $tck->description }}</p>
                        @if($tck->admin_response)
                            <div class="bg-amber-50 border border-amber-200 p-3 rounded-xl text-xs text-amber-950 mt-2 font-semibold">
                                <strong>Admin Response:</strong> {{ $tck->admin_response }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-10 bg-slate-50 rounded-2xl border border-slate-200 p-6 text-xs text-slate-400 italic">
                        No support tickets submitted yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
