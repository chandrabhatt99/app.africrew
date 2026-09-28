@extends('admin.layout', ['title' => 'Support Desk & Ticket Manager'])

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-950">Support & Dispute Desk</h1>
            <p class="text-xs text-slate-500 font-semibold mt-1">Review user support tickets, answer questions, and resolve booking disputes.</p>
        </div>
        <span class="px-3 py-1.5 rounded-full bg-slate-900 text-amber-400 font-black text-xs">
            Total Tickets: {{ $tickets->count() }}
        </span>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 font-bold text-xs">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($tickets as $tck)
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-slate-900 text-sm">#{{ $tck->ticket_number }} - {{ $tck->subject }}</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-amber-100 text-amber-900">
                                Category: {{ $tck->category }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-500 font-semibold mt-1">
                            User: <strong>{{ $tck->user?->name ?: 'Crew Member' }}</strong> ({{ $tck->user?->email }}) • Priority: {{ strtoupper($tck->priority) }}
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase {{ $tck->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-500 text-slate-950' }}">
                        {{ $tck->status }}
                    </span>
                </div>

                <div class="text-xs text-slate-700 bg-slate-50 p-4 rounded-2xl border border-slate-200 leading-relaxed font-semibold">
                    {{ $tck->description }}
                </div>

                <form method="POST" action="{{ route('admin.tickets.respond', $tck->id) }}" class="space-y-3 pt-2">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Admin Response</label>
                        <textarea name="admin_response" rows="2" placeholder="Write official admin response..." required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">{{ $tck->admin_response }}</textarea>
                    </div>
                    <div class="flex items-center gap-3">
                        <select name="status" class="text-xs px-3 py-2 border border-slate-300 rounded-xl font-bold">
                            <option value="open" {{ $tck->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $tck->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $tck->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $tck->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs rounded-xl">
                            Save Response
                        </button>
                    </div>
                </form>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-3xl border border-slate-200 p-6 text-xs text-slate-400 italic">
                No support tickets found.
            </div>
        @endforelse
    </div>
</div>
@endsection
