<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        if ($profId = session('professional_id')) {
            $prof = \App\Models\Professional::find($profId);
            if ($prof && !$prof->is_onboarded) {
                return redirect()->route('professional.profile')->with('warning', 'Please complete all 5 profile onboarding sections before accessing Support Desk.');
            }
        }

        $tickets = SupportTicket::where('user_id', $user->id)->latest()->get();

        return view('professional.support', compact('user', 'tickets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:100'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'description' => ['required', 'string', 'max:3000'],
        ]);

        SupportTicket::create([
            'ticket_number' => 'TCK-' . strtoupper(uniqid()),
            'user_id' => $user->id,
            'subject' => $data['subject'],
            'category' => $data['category'],
            'priority' => $data['priority'],
            'status' => 'open',
            'description' => $data['description'],
        ]);

        return back()->with('success', 'Support ticket submitted! Operations team will respond shortly.');
    }
}
