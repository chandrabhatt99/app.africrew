@extends('layouts.staff')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-16 px-2 sm:px-0">
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-black text-slate-900">Client Reviews & Feedback</h1>
                <p class="text-xs text-slate-500 mt-1">Verified feedback from clients who booked your services</p>
            </div>
            <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 px-4 py-2 rounded-2xl">
                <span class="text-amber-500 text-lg">⭐</span>
                <span class="text-sm font-black text-slate-900">{{ number_format($professional->average_rating, 1) }}</span>
                <span class="text-xs font-bold text-slate-500">({{ $reviews->count() }} reviews)</span>
            </div>
        </div>

        @if($reviews->count() > 0)
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <div class="p-5 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900">{{ $review->client_name }}</h3>
                                @if($review->event_name)
                                    <p class="text-xs text-slate-500">Event: {{ $review->event_name }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-1 text-amber-500 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                @endfor
                                <span class="text-slate-400 text-[10px] ml-2">{{ $review->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <p class="text-xs text-slate-700 leading-relaxed font-normal bg-white p-3 rounded-xl border border-slate-200/80">
                            "{{ $review->comment }}"
                        </p>

                        <!-- Existing Response -->
                        @if(!empty($review->response_text))
                            <div class="ml-4 p-3 bg-amber-50/80 border-l-4 border-amber-500 rounded-r-xl text-xs space-y-1">
                                <span class="font-extrabold text-slate-900 flex items-center gap-1">💬 Your Response:</span>
                                <p class="text-slate-700 font-normal">{{ $review->response_text }}</p>
                            </div>
                        @else
                            <!-- Inline Form to Respond -->
                            <form method="POST" action="{{ route('professional.reviews.respond', $review->id) }}" class="flex gap-2">
                                @csrf
                                <input type="text" name="response_text" required placeholder="Write a professional response to this client..." class="flex-1 bg-white border border-slate-200 text-xs rounded-xl px-3 py-2 outline-none focus:border-amber-500">
                                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-amber-400 font-bold text-xs rounded-xl transition-colors">
                                    Respond
                                </button>
                            </form>
                        @endif

                        <!-- Report Review Option -->
                        <div class="flex items-center justify-end text-[11px] pt-1">
                            @if($review->is_reported)
                                <span class="text-rose-600 font-bold">⚠️ Review reported to admin moderation</span>
                            @else
                                <form method="POST" action="{{ route('professional.reviews.report', $review->id) }}" onsubmit="return confirm('Report this review for admin moderation review?');">
                                    @csrf
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 font-bold transition-colors">
                                        🚩 Report Review
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center text-slate-500 space-y-3 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <span class="text-3xl">⭐</span>
                <p class="text-sm font-extrabold text-slate-800">No Client Reviews Yet</p>
                <p class="text-xs max-w-sm mx-auto text-slate-500">Reviews will appear here after clients book your services and complete event gigs.</p>
            </div>
        @endif
    </div>
</div>
@endsection
