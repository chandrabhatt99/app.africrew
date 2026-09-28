<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\StaffingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function show(Contract $contract): View
    {
        $contract->load(['staffingRequest', 'client', 'professional']);
        return view('contracts.show', compact('contract'));
    }

    public function createOrViewForRequest(StaffingRequest $staffingRequest): RedirectResponse
    {
        $contract = Contract::where('staffing_request_id', $staffingRequest->id)->first();

        if (!$contract) {
            $termsText = "OFFICIAL AFRICREW TALENT HIRING & SERVICES AGREEMENT\n\n" .
                "1. OBLIGATIONS OF PARTIES: The Client agrees to pay all escrow funds directly to AfriCrew Admin. The Crew Member agrees to arrive on-time at the designated venue on the event date ({$staffingRequest->event_date?->format('Y-m-d')}) and perform duties diligently.\n" .
                "2. ESCROW & PAYMENT POLICY: Funds are held safely in Escrow by AfriCrew Admin and released to Crew Wallet upon shift completion.\n" .
                "3. CANCELLATION: Cancellations within 24 hours of event date are subject to a 20% processing fee.\n" .
                "4. CODE OF CONDUCT: Professional behavior and adherence to venue safety policies is strictly required.";

            $contract = Contract::create([
                'staffing_request_id' => $staffingRequest->id,
                'user_id' => $staffingRequest->user_id,
                'contract_number' => 'AGR-' . strtoupper(uniqid()),
                'terms' => $termsText,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('contracts.show', $contract->id);
    }

    public function sign(Request $request, Contract $contract): RedirectResponse
    {
        $data = $request->validate([
            'signature_name' => ['required', 'string', 'max:150'],
            'sign_type' => ['required', 'in:client,crew'],
        ]);

        if ($data['sign_type'] === 'client') {
            $contract->update([
                'client_signature' => $data['signature_name'],
                'client_signed_at' => now(),
                'status' => $contract->crew_signed_at ? 'completed' : 'client_signed',
            ]);
            $msg = "Agreement successfully digitally signed by Client!";
        } else {
            $contract->update([
                'crew_signature' => $data['signature_name'],
                'crew_signed_at' => now(),
                'status' => $contract->client_signed_at ? 'completed' : 'crew_signed',
            ]);
            $msg = "Agreement successfully digitally signed by Crew Member!";
        }

        return back()->with('success', $msg);
    }
}
