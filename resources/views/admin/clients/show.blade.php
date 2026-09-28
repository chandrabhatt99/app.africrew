@extends('admin.layout')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        <span>Back to Client Directory</span>
    </a>
</div>

<!-- Header Card -->
<div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-sm mb-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-slate-900 text-amber-400 font-black flex items-center justify-center text-xl shadow-md">
                {{ strtoupper(substr($client->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $client->name }}</h1>
                <p class="text-slate-500 text-xs mt-1">Client Profile & Booking History Timeline</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-500/10 text-amber-800 border border-amber-500/20 uppercase tracking-wider">
                REGISTERED CLIENT
            </span>
        </div>
    </div>

    <!-- Client Metrics & Contact Overview -->
    <div class="grid sm:grid-cols-4 gap-6 text-sm">
        <div>
            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Company / Organization</span>
            <span class="font-extrabold text-slate-900">{{ $client->company_name ?: 'Individual Client' }}</span>
        </div>
        <div>
            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Email Address</span>
            <a href="mailto:{{ $client->email }}" class="font-extrabold text-amber-700 hover:underline">{{ $client->email }}</a>
        </div>
        <div>
            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Phone Number</span>
            <span class="font-extrabold text-slate-900">{{ $client->phone ?: 'N/A' }}</span>
        </div>
        <div>
            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Total Payments to Admin</span>
            <span class="font-black text-emerald-700 text-base">KES {{ number_format($totalSpent, 2) }}</span>
        </div>
    </div>
</div>

<!-- Client Booking History ("kis client ne kab ki booking ki h") -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="font-extrabold text-slate-900 text-lg">Client Booking History</h2>
            <p class="text-xs text-slate-500 mt-0.5">All event crewing requests placed by {{ $client->name }}</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-800 font-extrabold text-xs">
            {{ $requests->count() }} Total Requests
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[750px]">
            <thead>
                <tr class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <th class="py-3.5 px-6">Booking Date</th>
                    <th class="py-3.5 px-6">Event Name & Location</th>
                    <th class="py-3.5 px-6">Category / Crew Count</th>
                    <th class="py-3.5 px-6">Event Date</th>
                    <th class="py-3.5 px-6">Assigned Crew</th>
                    <th class="py-3.5 px-6">Payment to Admin</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($requests as $r)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <!-- Booking Date -->
                    <td class="py-3.5 px-6 font-bold text-slate-900 text-xs">
                        <div>{{ $r->created_at ? $r->created_at->format('d M Y') : 'N/A' }}</div>
                        <div class="text-[10px] text-slate-400 font-normal">{{ $r->created_at ? $r->created_at->format('h:i A') : '' }}</div>
                    </td>

                    <!-- Event Name -->
                    <td class="py-3.5 px-6 font-extrabold text-slate-900">
                        <div>{{ $r->event_name }}</div>
                        <div class="text-xs font-normal text-slate-500">📍 {{ $r->location }}</div>
                    </td>

                    <!-- Category -->
                    <td class="py-3.5 px-6">
                        <span class="inline-flex px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200">
                            {{ $r->staff_count }}x {{ $r->category }}
                        </span>
                    </td>

                    <!-- Event Date -->
                    <td class="py-3.5 px-6 text-xs text-slate-700 font-medium">
                        {{ $r->event_date ? $r->event_date->format('d M Y') : 'N/A' }}
                    </td>

                    <!-- Assigned Crew -->
                    <td class="py-3.5 px-6 text-xs">
                        @if($r->assignments->isEmpty())
                            <span class="text-slate-400 italic">None assigned</span>
                        @else
                            <div class="space-y-1">
                                @foreach($r->assignments as $asg)
                                    <div class="font-bold text-slate-900 flex items-center gap-1">
                                        <span>👤 {{ $asg->professional->full_name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>

                    <!-- Payment Status -->
                    <td class="py-3.5 px-6 text-xs">
                        @if($r->payment_status === 'paid_to_admin')
                            <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold border border-emerald-200">
                                ✓ Paid to Admin (KES {{ number_format($r->payment_amount, 2) }})
                            </span>
                        @elseif($r->payment_status === 'released_to_crew')
                            <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 font-bold border border-blue-200">
                                ✓ Released to Crew Wallet
                            </span>
                        @else
                            <span class="inline-flex px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-bold border border-amber-200">
                                ⏳ Unpaid (Pay Admin)
                            </span>
                        @endif
                    </td>

                    <!-- Job Status -->
                    <td class="py-3.5 px-6">
                        @php
                            $statusClasses = [
                                'new' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'assigned' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                'completed' => 'bg-emerald-100 text-emerald-900 border-emerald-200',
                            ][$r->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusClasses }}">
                            {{ str_replace('_', ' ', ucfirst($r->status)) }}
                        </span>
                    </td>

                    <td class="py-3.5 px-6 text-right">
                        <a href="{{ route('admin.requests.show', $r) }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl border border-slate-200 transition-all">
                            <span>Manage Request</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-8 text-center text-slate-500 text-xs italic">No bookings submitted by this client yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
