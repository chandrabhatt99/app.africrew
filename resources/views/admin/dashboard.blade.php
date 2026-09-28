@extends('admin.layout')

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Executive Operations Dashboard</h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">Real-time overview of client staffing requests and crew management.</p>
    </div>
    
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.professionals.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 text-amber-400 font-extrabold text-xs shadow-md hover:bg-slate-800 transition-all flex items-center gap-2">
            <span>🛡️ Manage Staff Directory</span>
        </a>
        <a href="{{ route('admin.professionals.create') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-800 font-bold text-xs shadow-xs hover:bg-slate-50 transition-all flex items-center gap-2">
            <span>+ Onboard Staff</span>
        </a>
    </div>
</div>

<!-- Metric Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Requests</span>
        <div class="text-2xl font-black text-slate-900 mt-2">{{ number_format($totalRequests) }}</div>
        <div class="text-[10px] text-slate-400 mt-0.5">Submitted by clients</div>
    </div>
    
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-amber-600 tracking-wider">New Requests</span>
        <div class="text-2xl font-black text-amber-600 mt-2">{{ number_format($newRequests) }}</div>
        <div class="text-[10px] text-slate-400 mt-0.5">Awaiting matching</div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-slate-600 tracking-wider">🏢 Registered Clients</span>
        <div class="text-2xl font-black text-slate-900 mt-2">{{ number_format($totalClients) }}</div>
        <div class="text-[10px] text-slate-400 mt-0.5"><a href="{{ route('admin.clients.index') }}" class="text-amber-700 hover:underline font-bold">View Client Directory →</a></div>
    </div>

    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-4 sm:p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-amber-800 tracking-wider">🔒 Escrow Held</span>
        <div class="text-xl font-black text-amber-950 mt-2">KES {{ number_format($totalEscrowHeld, 2) }}</div>
        <div class="text-[10px] text-amber-700 mt-0.5">Locked until shift done</div>
    </div>

    <div class="bg-emerald-50 rounded-2xl border border-emerald-200 p-4 sm:p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-emerald-800 tracking-wider">✓ Approved Staff</span>
        <div class="text-2xl font-black text-emerald-950 mt-2">{{ number_format($approvedProfessionals) }}</div>
        <div class="text-[10px] text-emerald-700 mt-0.5">Active on catalog</div>
    </div>

    <div class="bg-rose-50 rounded-2xl border border-rose-200 p-4 sm:p-5 shadow-xs">
        <span class="text-[10px] font-bold uppercase text-rose-800 tracking-wider">Withdrawal Requests</span>
        <div class="text-2xl font-black text-rose-950 mt-2">{{ number_format($pendingWithdrawalsCount) }}</div>
        <div class="text-[10px] text-rose-700 mt-0.5"><a href="{{ route('admin.payments.index') }}" class="font-bold underline">Manage Payments →</a></div>
    </div>
</div>

<!-- Recent Staffing Requests Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 mt-8 shadow-sm overflow-hidden">
    <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="font-extrabold text-slate-900 text-base">Recent Client Bookings & Requests</h2>
            <p class="text-xs text-slate-500 mt-0.5">Real-time timeline of client bookings and escrow payment statuses</p>
        </div>
        <a href="{{ route('admin.requests.index') }}" class="text-xs font-bold text-amber-700 hover:underline">
            View All Requests →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[700px]">
            <thead>
                <tr class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <th class="py-3.5 px-6">Booking Date</th>
                    <th class="py-3.5 px-6">Client Details</th>
                    <th class="py-3.5 px-6">Event Name</th>
                    <th class="py-3.5 px-6">Category</th>
                    <th class="py-3.5 px-6">Payment Status</th>
                    <th class="py-3.5 px-6">Project Status</th>
                    <th class="py-3.5 px-6 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($requests as $r)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-6 font-bold text-slate-900 text-xs">
                        <div>{{ $r->created_at ? $r->created_at->format('d M Y') : 'N/A' }}</div>
                        <div class="text-[10px] text-slate-400 font-normal">{{ $r->created_at ? $r->created_at->format('h:i A') : '' }}</div>
                    </td>
                    <td class="py-3.5 px-6 font-bold text-slate-900">
                        <div>{{ $r->full_name }}</div>
                        <div class="text-xs font-normal text-slate-400">{{ $r->company_name ?: $r->email }}</div>
                    </td>
                    <td class="py-3.5 px-6 text-slate-700 font-medium">{{ $r->event_name }}</td>
                    <td class="py-3.5 px-6">
                        <span class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-xs font-bold">
                            {{ $r->category }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-xs">
                        @if($r->payment_status === 'paid_to_admin')
                            <span class="inline-flex px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-bold border border-amber-200">
                                🔒 Paid to Admin
                            </span>
                        @elseif($r->payment_status === 'released_to_crew')
                            <span class="inline-flex px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 font-bold border border-emerald-200">
                                ✓ Released to Crew
                            </span>
                        @else
                            <span class="inline-flex px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold">
                                ⏳ Unpaid
                            </span>
                        @endif
                    </td>
                    <td class="py-3.5 px-6">
                        @php
                            $statusClasses = [
                                'new' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'assigned' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'completed' => 'bg-slate-100 text-slate-800 border-slate-200',
                            ][$r->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                        @endphp
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border {{ $statusClasses }}">
                            {{ str_replace('_', ' ', ucfirst($r->status)) }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-right">
                        <a href="{{ route('admin.requests.show', $r) }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-900 hover:text-amber-700 bg-slate-100 px-3 py-1 rounded-xl border border-slate-200">
                            <span>Manage</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-slate-500 text-xs italic">No staffing requests submitted yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Registered Staff Members Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 mt-8 shadow-sm overflow-hidden pb-4">
    <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="font-extrabold text-slate-900 text-base">Registered Staff Directory Quick Access</h2>
            <p class="text-xs text-slate-500 mt-0.5">Manage staff verification and approve accounts</p>
        </div>
        <a href="{{ route('admin.professionals.index') }}" class="text-xs font-bold text-amber-700 hover:underline">
            View All Staff Directory →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[700px]">
            <thead>
                <tr class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <th class="py-3.5 px-6">Staff Member</th>
                    <th class="py-3.5 px-6">Category</th>
                    <th class="py-3.5 px-6">Location</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentStaff as $sp)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-6 font-bold text-slate-900">
                        <div>{{ $sp->full_name }}</div>
                        <div class="text-xs font-normal text-slate-400">{{ $sp->email }}</div>
                    </td>
                    <td class="py-3.5 px-6">
                        <span class="inline-flex px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-800 text-xs font-bold">
                            {{ $sp->category }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-slate-600 text-xs font-medium">{{ $sp->city }}, {{ $sp->country }}</td>
                    <td class="py-3.5 px-6">
                        <span class="inline-flex px-3 py-0.5 rounded-full text-xs font-bold uppercase {{ $sp->status==='approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $sp->status }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-right">
                        <a href="{{ route('admin.professionals.show', $sp) }}" class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 px-3 py-1 rounded-xl border border-amber-200">
                            <span>Inspect & Manage</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-slate-500 text-xs italic">No registered staff members found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
