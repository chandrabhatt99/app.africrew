@extends('admin.layout')

@section('content')
<div class="space-y-6 pb-12">

    <!-- Page Header & Onboard CTA -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Registered Crew & Talent Directory</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage registered crew members, verify identities, approve accounts, or manage accounts.</p>
        </div>
        
        <a href="{{ route('admin.professionals.create') }}" class="px-5 py-3 rounded-2xl bg-slate-900 text-amber-400 font-extrabold text-xs shadow-md hover:bg-slate-800 transition-all flex items-center justify-center gap-2 w-full sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span>+ Onboard New Crew</span>
        </a>
    </div>

    <!-- Metric Filter Cards Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('admin.professionals.index', ['status' => 'all']) }}" class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-amber-400 transition-all">
            <span class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider block">Total Registered</span>
            <span class="text-2xl font-black text-slate-900 mt-1 block">{{ number_format($counts['total']) }}</span>
        </a>
        <a href="{{ route('admin.professionals.index', ['status' => 'approved']) }}" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 shadow-xs hover:border-emerald-400 transition-all">
            <span class="text-[10px] uppercase font-extrabold text-emerald-700 tracking-wider block">✓ Approved Crew</span>
            <span class="text-2xl font-black text-emerald-950 mt-1 block">{{ number_format($counts['approved']) }}</span>
        </a>
        <a href="{{ route('admin.professionals.index', ['status' => 'pending']) }}" class="p-4 rounded-2xl bg-amber-50 border border-amber-200 shadow-xs hover:border-amber-400 transition-all">
            <span class="text-[10px] uppercase font-extrabold text-amber-800 tracking-wider block">⏳ Pending Review</span>
            <span class="text-2xl font-black text-amber-950 mt-1 block">{{ number_format($counts['pending']) }}</span>
        </a>
        <a href="{{ route('admin.professionals.index', ['status' => 'deactivated']) }}" class="p-4 rounded-2xl bg-rose-50 border border-rose-200 shadow-xs hover:border-rose-400 transition-all">
            <span class="text-[10px] uppercase font-extrabold text-rose-800 tracking-wider block">🚫 Deactivated</span>
            <span class="text-2xl font-black text-rose-950 mt-1 block">{{ number_format($counts['deactivated']) }}</span>
        </a>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.professionals.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
            <div class="relative flex-grow w-full">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, email, phone, city, category..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-4 py-2.5 text-xs font-semibold text-slate-900 outline-none focus:border-amber-500">
                <span class="absolute left-3.5 top-3 text-slate-400">🔍</span>
            </div>

            <select name="status" onchange="this.form.submit()" class="w-full sm:w-48 bg-slate-50 border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-slate-900 outline-none focus:border-amber-500">
                <option value="all" @selected($status=='all' || !$status)>All Statuses</option>
                <option value="approved" @selected($status=='approved')>Approved</option>
                <option value="pending" @selected($status=='pending')>Pending</option>
                <option value="deactivated" @selected($status=='deactivated')>Deactivated</option>
                <option value="suspended" @selected($status=='suspended')>Suspended</option>
            </select>

            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-slate-900 text-amber-400 font-extrabold text-xs shrink-0 w-full sm:w-auto">
                Filter Results
            </button>
        </form>
    </div>

    <!-- Staff Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[800px]">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold text-[11px] uppercase tracking-wider border-b border-slate-200/80">
                        <th class="py-4 px-6">Crew Member</th>
                        <th class="py-4 px-6">Category</th>
                        <th class="py-4 px-6">Contact & Location</th>
                        <th class="py-4 px-6">Experience & Rates</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Admin Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($professionals as $p)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                @php
                                    $avatar = get_storage_url($p->profile_photo);
                                @endphp
                                @if($avatar)
                                    <img src="{{ $avatar }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-amber-500/20 text-slate-950 font-black flex items-center justify-center text-sm border border-amber-300 shrink-0">
                                        {{ strtoupper(substr($p->full_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('admin.professionals.show', $p) }}" class="font-extrabold text-slate-900 hover:text-amber-600 transition-colors block">
                                        {{ $p->full_name }}
                                    </a>
                                    <span class="text-[11px] text-slate-400 font-semibold">Registered {{ $p->created_at ? $p->created_at->format('M j, Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex px-3 py-1 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold">
                                {{ $p->category }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-600">
                            <div class="font-bold text-slate-900">{{ $p->phone }}</div>
                            <div class="text-[11px] text-slate-400">{{ $p->email }}</div>
                            <div class="text-[10px] font-bold text-slate-500 mt-0.5">📍 {{ $p->city }}, {{ $p->country }}</div>
                        </td>
                        <td class="py-4 px-6 text-xs">
                            <div class="font-black text-slate-900">{{ $p->experience_years }} Yrs Exp</div>
                            <div class="text-amber-700 font-extrabold text-[11px]">${{ number_format($p->hourly_rate ?? 25, 2) }}/hr</div>
                        </td>
                        <td class="py-4 px-6">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-100 text-amber-900 border-amber-300',
                                    'approved' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                                    'rejected' => 'bg-rose-100 text-rose-900 border-rose-300',
                                    'deactivated' => 'bg-rose-100 text-rose-900 border-rose-300',
                                    'suspended' => 'bg-slate-200 text-slate-900 border-slate-300',
                                ][$p->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-black border uppercase tracking-wider {{ $statusClasses }}">
                                {{ $p->status === 'deactivated' ? '🚫 Deactivated' : ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.professionals.show', $p) }}" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-xs shadow-xs" title="View Full Details">
                                    <span>Review</span>
                                </a>

                                @if($p->status !== 'approved')
                                    <form method="POST" action="{{ route('admin.professionals.status', $p) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs" title="Approve Crew">
                                            ✓ Approve
                                        </button>
                                    </form>
                                @endif

                                @if($p->status !== 'deactivated')
                                    <form method="POST" action="{{ route('admin.professionals.status', $p) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="deactivated">
                                        <button type="submit" onclick="return confirm('Deactivate this crew account?')" class="px-2.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-xs" title="Deactivate Account">
                                            🚫 Deactivate
                                        </button>
                                    </form>
                                @endif

                                <!-- Delete Staff Action -->
                                <button type="button" onclick="confirmDeleteStaff('{{ $p->id }}', '{{ addslashes($p->full_name) }}')" class="px-2.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs" title="Delete Crew Account">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 text-xs italic">No registered crew records match your criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($professionals->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $professionals->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Hidden Form for Staff Deletion -->
<form id="admin-delete-staff-form" method="POST" action="" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDeleteStaff(staffId, staffName) {
    Swal.fire({
        title: 'Delete Crew Member?',
        text: `Are you sure you want to permanently delete '${staffName}' from the database? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Delete Crew Member 🗑️'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('admin-delete-staff-form');
            form.action = `/admin/professionals/${staffId}`;
            form.submit();
        }
    });
}
</script>
@endsection
