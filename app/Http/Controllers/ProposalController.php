<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\Proposal;
use App\Models\StaffingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalController extends Controller
{
    public function store(Request $request, StaffingRequest $staffingRequest): RedirectResponse
    {
        $profId = $request->session()->get('professional_id');
        $prof = $profId ? Professional::find($profId) : Professional::where('email', Auth::user()?->email)->first();

        if (!$prof) {
            return back()->with('error', 'Crew professional account not found. Please log in as staff.');
        }

        $data = $request->validate([
            'custom_quote_amount' => ['required', 'numeric', 'min:1'],
            'travel_fee' => ['nullable', 'numeric', 'min:0'],
            'accommodation_fee' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Proposal::updateOrCreate(
            [
                'staffing_request_id' => $staffingRequest->id,
                'professional_id' => $prof->id,
            ],
            [
                'custom_quote_amount' => $data['custom_quote_amount'],
                'travel_fee' => $data['travel_fee'] ?? 0,
                'accommodation_fee' => $data['accommodation_fee'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]
        );

        return back()->with('success', 'Quotation proposal submitted to client successfully!');
    }

    public function respond(Request $request, Proposal $proposal): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:accept,counter,reject'],
            'counter_amount' => ['required_if:action,counter', 'nullable', 'numeric', 'min:1'],
        ]);

        if ($data['action'] === 'accept') {
            $proposal->update(['status' => 'accepted']);
            $msg = "Proposal accepted!";
        } elseif ($data['action'] === 'counter') {
            $proposal->update([
                'status' => 'countered',
                'counter_amount' => $data['counter_amount'],
            ]);
            $msg = "Counter-offer of \${$data['counter_amount']} sent to crew member!";
        } else {
            $proposal->update(['status' => 'rejected']);
            $msg = "Proposal declined.";
        }

        return back()->with('success', $msg);
    }
}
