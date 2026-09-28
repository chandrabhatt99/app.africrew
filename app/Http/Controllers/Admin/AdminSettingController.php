<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'platform_commission_percent' => SystemSetting::get('platform_commission_percent', '15'),
            'currency_symbol' => SystemSetting::get('currency_symbol', '$'),
            'support_email' => SystemSetting::get('support_email', 'ops@africrew.com'),
            'minimum_withdrawal_amount' => SystemSetting::get('minimum_withdrawal_amount', '20'),
            'auto_approve_crew' => SystemSetting::get('auto_approve_crew', '0'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'platform_commission_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'support_email' => ['required', 'email', 'max:150'],
            'minimum_withdrawal_amount' => ['required', 'numeric', 'min:1'],
            'auto_approve_crew' => ['required', 'in:0,1'],
        ]);

        foreach ($data as $key => $val) {
            SystemSetting::set($key, $val);
        }

        return back()->with('success', 'System platform settings updated successfully!');
    }
}
