@extends('admin.layout')

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Client Accounts Directory</h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage registered event organizers, corporate clients, and their booking histories.</p>
    </div>
</div>

<!-- Search Form -->
<div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs mb-8">
    <form method="GET" action="{{ route('admin.clients.index') }}" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by client name, email, company, or phone..."
            class="flex-1 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl px-4 py-3 outline-none focus:border-amber-500 focus:bg-white transition-all">
        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-xs px-6 py-3 rounded-2xl shadow-sm transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <span>Filter Clients</span>
        </button>
        @if(request('search'))
            <a href="{{ route('admin.clients.index') }}" class="bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-xs px-4 py-3 rounded-2xl transition-all flex items-center justify-center">
                Clear
            </a>
        @endif
    </form>
</div>

<!-- Clients Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[700px]">
            <thead>
                <tr class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <th class="py-3.5 px-6">Client Name</th>
                    <th class="py-3.5 px-6">Company / Organization</th>
                    <th class="py-3.5 px-6">Contact Info</th>
                    <th class="py-3.5 px-6">Total Bookings</th>
                    <th class="py-3.5 px-6">Total Payments to Admin</th>
                    <th class="py-3.5 px-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($clients as $c)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-6 font-extrabold text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-900 text-amber-400 font-black flex items-center justify-center text-xs shadow-xs">
                                {{ strtoupper(substr($c->name, 0, 1)) }}
                            </div>
                            <div>
                                <div>{{ $c->name }}</div>
                                <div class="text-[11px] font-medium text-slate-400">Joined {{ $c->created_at ? $c->created_at->format('d M Y') : 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5 px-6 text-slate-700 font-medium">
                        {{ $c->company_name ?: 'Individual Event Client' }}
                    </td>
                    <td class="py-3.5 px-6 text-xs text-slate-600 font-medium">
                        <div>{{ $c->email }}</div>
                        <div class="text-slate-400 mt-0.5">{{ $c->phone ?: 'N/A' }}</div>
                    </td>
                    <td class="py-3.5 px-6">
                        <span class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-900 font-black text-xs border border-slate-200">
                            {{ number_format($c->total_requests_count) }} Bookings
                        </span>
                    </td>
                    <td class="py-3.5 px-6 font-black text-emerald-700">
                        KES {{ number_format($c->total_spent, 2) }}
                    </td>
                    <td class="py-3.5 px-6 text-right">
                        <a href="{{ route('admin.clients.show', $c) }}" class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 px-3.5 py-1.5 rounded-xl border border-amber-200 hover:bg-amber-100 transition-all">
                            <span>Inspect Client & History →</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500 text-xs italic">No client accounts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clients->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $clients->links() }}
        </div>
    @endif
</div>
@endsection
