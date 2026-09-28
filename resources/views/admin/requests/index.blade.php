@extends('admin.layout')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Crew Requests</h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage client orders, update statuses, and match vetted crew members.</p>
    </div>
    
    <a href="{{ route('admin.requests.create') }}" class="px-5 py-3 rounded-2xl bg-slate-900 text-gold-400 font-extrabold text-xs shadow-md hover:bg-slate-800 transition-all flex items-center justify-center gap-2 w-full sm:w-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
        <span>Add Crew Request</span>
    </a>
</div>

<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[640px]">
            <thead>
                <tr class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <th class="py-4 px-6">Client</th>
                    <th class="py-4 px-6">Event Details</th>
                    <th class="py-4 px-6">Category</th>
                    <th class="py-4 px-6">Required Crew</th>
                    <th class="py-4 px-6">Event Date</th>
                    <th class="py-4 px-6">Status</th>
                    <th class="py-4 px-6 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($requests as $r)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-4 px-6 font-bold text-slate-900">
                        <div>{{ $r->full_name }}</div>
                        <div class="text-xs font-normal text-slate-400">{{ $r->email }}</div>
                    </td>
                    <td class="py-4 px-6 text-slate-700 font-medium">
                        <div>{{ $r->event_name }}</div>
                        <div class="text-xs font-normal text-slate-400">{{ $r->location }}</div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-xs font-bold">
                            {{ $r->category }}
                        </span>
                    </td>
                    <td class="py-4 px-6 font-bold text-slate-900">{{ $r->staff_count }} crew</td>
                    <td class="py-4 px-6 text-slate-600 text-xs font-medium">{{ $r->event_date->format('d M Y') }}</td>
                    <td class="py-4 px-6">
                        @php
                            $statusClasses = [
                                'new' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'staff_matching' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'shortlisted' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                'assigned' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'completed' => 'bg-slate-100 text-slate-800 border-slate-200',
                                'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                            ][$r->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                        @endphp
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border {{ $statusClasses }}">
                            {{ str_replace('_', ' ', ucfirst($r->status)) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <a href="{{ route('admin.requests.show', $r) }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-900 hover:text-amber-700 bg-slate-100 hover:bg-amber-50 px-3.5 py-2 rounded-xl border border-slate-200 transition-all">
                            <span>Manage</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-slate-500 text-sm">No crew requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
