@extends('admin.layout', ['title' => 'Promotional Coupons & Discounts'])

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-950">Promotional Coupons & Offers Desk</h1>
            <p class="text-xs text-slate-500 font-semibold mt-1">Create discount promo codes for clients during event crewing booking checkout.</p>
        </div>
        <span class="px-3 py-1.5 rounded-full bg-slate-900 text-amber-400 font-black text-xs">
            Active Coupons: {{ $coupons->count() }}
        </span>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 font-bold text-xs">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Create Coupon Form (Col Span 5) -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-black text-slate-900">Create New Promo Code</h3>
            <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-4 text-xs font-bold">
                @csrf
                <div>
                    <label class="block text-slate-600 mb-1">Coupon Code</label>
                    <input type="text" name="code" placeholder="e.g. WELCOME10 or EVENT20" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500 uppercase">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-600 mb-1">Discount Type</label>
                        <select name="type" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none">
                            <option value="percent">Percentage (%)</option>
                            <option value="fixed">Fixed Amount ($)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-600 mb-1">Discount Value</label>
                        <input type="number" name="value" placeholder="10" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-600 mb-1">Min Order Amount ($)</label>
                    <input type="number" name="min_order_amount" placeholder="0" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-slate-600 mb-1">Expiry Date (Optional)</label>
                    <input type="date" name="expires_at" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                </div>

                <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-xs shadow-xs">
                    Create Discount Coupon 🏷️
                </button>
            </form>
        </div>

        <!-- Coupons Table (Col Span 7) -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-black text-slate-900">Platform Active Coupons</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] font-black uppercase text-slate-400 border-b border-slate-200">
                            <th class="pb-3">Code</th>
                            <th class="pb-3">Discount</th>
                            <th class="pb-3">Min Order</th>
                            <th class="pb-3">Expires</th>
                            <th class="pb-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @forelse($coupons as $coup)
                            <tr>
                                <td class="py-3 font-extrabold text-amber-800 font-mono">{{ $coup->code }}</td>
                                <td class="py-3 font-bold text-slate-900">
                                    {{ $coup->type === 'percent' ? $coup->value . '%' : '$' . number_format($coup->value, 2) }}
                                </td>
                                <td class="py-3">${{ number_format($coup->min_order_amount, 2) }}</td>
                                <td class="py-3 text-[10px] text-slate-400">
                                    {{ $coup->expires_at ? $coup->expires_at->format('M j, Y') : 'No Expiry' }}
                                </td>
                                <td class="py-3 text-right">
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coup->id) }}" onsubmit="return confirm('Delete coupon?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[10px] font-bold">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400 italic">No promotional coupons created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
