@extends('admin.layout', ['title' => 'Client Review Moderation Desk'])

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-950">Review & Rating Moderation Desk</h1>
            <p class="text-xs text-slate-500 font-semibold mt-1">Audit and moderate public star ratings submitted by clients for vetted crew members.</p>
        </div>
        <span class="px-3 py-1.5 rounded-full bg-slate-900 text-amber-400 font-black text-xs">
            Total Reviews: {{ $reviews->count() }}
        </span>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 font-bold text-xs">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-[10px] font-black uppercase text-slate-400 border-b border-slate-200">
                    <th class="pb-3">Reviewer / Client</th>
                    <th class="pb-3">Crew Member</th>
                    <th class="pb-3">Rating</th>
                    <th class="pb-3">Comment / Review</th>
                    <th class="pb-3">Date</th>
                    <th class="pb-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                @forelse($reviews as $rev)
                    <tr>
                        <td class="py-3 font-bold text-slate-900">{{ $rev->reviewer_name }}</td>
                        <td class="py-3 text-amber-800 font-bold">{{ $rev->professional?->full_name ?: 'N/A' }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-black text-[10px]">
                                ⭐ {{ $rev->rating }}.0
                            </span>
                        </td>
                        <td class="py-3 max-w-xs truncate">{{ $rev->comment ?: 'No written comment' }}</td>
                        <td class="py-3 text-slate-400 text-[10px]">{{ $rev->created_at->format('M j, Y') }}</td>
                        <td class="py-3 text-right">
                            <form method="POST" action="{{ route('admin.reviews.destroy', $rev->id) }}" onsubmit="return confirm('Delete this client review?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[10px] font-bold">
                                    🗑️ Delete Review
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400 italic">No reviews recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
