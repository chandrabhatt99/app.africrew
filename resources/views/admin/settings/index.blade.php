@extends('admin.layout', ['title' => 'Platform System Settings'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-950">System Settings & Platform Variables</h1>
            <p class="text-xs text-slate-500 font-semibold mt-1">Configure global operational parameters, commission rates, and notification email targets.</p>
        </div>
        <span class="px-3 py-1.5 rounded-full bg-amber-500 text-slate-950 font-black text-xs">
            ⚙️ Operational Control
        </span>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 font-bold text-xs">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6 text-xs font-bold">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-slate-700 mb-1">Platform Escrow Commission Rate (%)</label>
                    <input type="number" name="platform_commission_percent" value="{{ $settings['platform_commission_percent'] }}" min="0" max="100" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                    <span class="text-[10px] text-slate-400 font-normal">Standard commission retained by platform on booking payouts.</span>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1">Default Platform Currency Symbol</label>
                    <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                    <span class="text-[10px] text-slate-400 font-normal">Primary currency symbol displayed across rates and invoices (e.g. $, ₦, KSh).</span>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1">Operations & Support Email</label>
                    <input type="email" name="support_email" value="{{ $settings['support_email'] }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                    <span class="text-[10px] text-slate-400 font-normal">Receives withdrawal notifications and dispute alerts.</span>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1">Minimum Crew Withdrawal Limit ($)</label>
                    <input type="number" name="minimum_withdrawal_amount" value="{{ $settings['minimum_withdrawal_amount'] }}" min="1" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                    <span class="text-[10px] text-slate-400 font-normal">Minimum wallet balance required before crew can request payout.</span>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-slate-700 mb-1">Automated Crew Profile Approval</label>
                    <select name="auto_approve_crew" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none">
                        <option value="0" {{ $settings['auto_approve_crew'] == '0' ? 'selected' : '' }}>Manual Verification Required (Recommended - Strict Quality Control)</option>
                        <option value="1" {{ $settings['auto_approve_crew'] == '1' ? 'selected' : '' }}>Auto-Approve All Registered Crew Profiles</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black rounded-xl text-xs shadow-md">
                    Save System Settings 💾
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
