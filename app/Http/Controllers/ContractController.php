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
            $termsText = "AFRICREW - GENERAL CONTRACT FOR SERVICE\n" .
                "Service Agreement Between Client and Crew Member\n" .
                "Facilitated and Managed via AfriCrew Limited Platform\n\n" .
                "THIS AGREEMENT is made and entered into on the date set out in Schedule A (the \"Contract Date\"), by and between the parties detailed below. AfriCrew Limited (\"AfriCrew\", \"Platform\", \"System\", or \"Company\") facilitates this Agreement as an intermediary agent and booking manager.\n\n" .
                "1. PARTIES AND PLATFORM ROLE\n" .
                "A. THE CLIENT:\n" .
                "Full Name / Company: " . ($staffingRequest->full_name ?: 'Client') . "\n" .
                "ID / Registration No: CLT-" . ($staffingRequest->user_id ?: 'GUEST') . "\n" .
                "Phone: " . ($staffingRequest->phone ?: 'N/A') . " | Email: " . ($staffingRequest->email ?: 'N/A') . "\n\n" .
                "B. THE CREW MEMBER:\n" .
                "Full Name: Assigned AfriCrew Professional\n" .
                "AfriCrew Profile ID: CREW-" . ($staffingRequest->id) . "\n" .
                "Service Category: " . ($staffingRequest->category ?: 'Event Services') . "\n\n" .
                "C. THE FACILITATOR - AfriCrew Limited:\n" .
                "Company Registration No: PVT-VQ1EBZPZ | Contact: support@africrew.com\n" .
                "Role: AfriCrew Limited is NOT the direct service provider or employer. AfriCrew operates the platform that connects Client and Crew Member, manages bookings, manages secure escrow payments, and disburses payments in instalments per agreed milestones. Both Client and Crew Member appoint AfriCrew as their limited payment agent and escrow holder for this booking.\n\n" .
                "2. BOOKING SUMMARY - SYSTEM GENERATED DETAILS (SCHEDULE A)\n" .
                "Contract Date: " . date('Y-m-d') . "\n" .
                "Booking ID: AFR-" . str_pad($staffingRequest->id, 6, '0', STR_PAD_LEFT) . "\n" .
                "Event Date(s): " . ($staffingRequest->event_date?->format('Y-m-d') ?: 'TBD') . "\n" .
                "Event Location / Venue: " . ($staffingRequest->location ?: 'N/A') . "\n" .
                "Working Hours Per Day: " . ($staffingRequest->shift_duration ?: '8 Hours') . " (" . ($staffingRequest->start_time ?: '08:00') . " - " . ($staffingRequest->end_time ?: '17:00') . ")\n" .
                "TOTAL CONTRACT AMOUNT (Paid 100% Upfront): " . ($staffingRequest->currency ?: 'KES') . " " . number_format($staffingRequest->budget ?: 0, 2) . "\n" .
                "Platform Service Fee (Included): 15% of Total\n" .
                "MILESTONE 1 - Booking Secured / Deposit Release: 30% - Released on booking confirmation\n" .
                "MILESTONE 2 - Mid-progress / Event Day Start: 40% - Released on Event Start / Check-in\n" .
                "MILESTONE 3 - Final Delivery & Client Approval: 30% - Released on approval of final deliverables\n" .
                "Deliverables Summary: " . ($staffingRequest->requirements ?: 'As described in booking request') . "\n" .
                "Delivery Deadline: Within 72 hours of event completion\n\n" .
                "3. SCOPE OF WORK\n3.1 The Crew Member agrees to provide the services described in Schedule A and the Client's booking request (\"Services\") with professional skill, care, and diligence.\n3.2 This is a generic agreement designed to cover all categories listed on AfriCrew. The Deliverables section defines the specific tasks, equipment to be provided by Crew (if any), and expected outcomes.\n3.3 Any work outside the agreed scope shall require a written variation via the AfriCrew platform and may trigger a new milestone or additional fee.\n\n" .
                "4. TERM, LOCATION & WORKING HOURS\n4.1 Term: Commences on Contract Date and remains in force until full delivery, acceptance, and final milestone disbursement.\n4.2 Location: Services shall be performed at the Location specified in Schedule A.\n4.3 Working Hours: As per Schedule A. Any time beyond the agreed hours is Overtime and billable at the Overtime Rate, to be added as an extra milestone or charged via the platform if pre-approved.\n\n" .
                "5. DELIVERABLES & ACCEPTANCE\n5.1 Deliverables: The Crew Member shall deliver exactly what is listed in Schedule A.\n5.2 Standard of Work: Professional standard customary to the trade or business.\n5.3 Proof of Service: For time-based services, delivery is confirmed by on-site completion and sign-off. For creative services, delivery is via upload/link as per parties' agreement.\n5.4 Client Review Period: For each milestone that requires approval, Client has 72 hours (or as stated in Schedule A) from notification to review and request reasonable revisions. If no objection is raised within that period, that milestone Deliverable shall be deemed Accepted.\n5.5 Revisions: One round of reasonable revisions is included (where applicable) per milestone if the deliverable does not match Schedule A. Materially new requests are a variation.\n5.6 Final Acceptance: Upon acceptance (explicit or deemed) of a milestone, Client authorises AfriCrew Limited to release that milestone payment. This authorisation is irrevocable once that milestone is accepted.\n\n" .
                "6. PAYMENT TERMS, ESCROW & MILESTONE DISBURSEMENT\n6.1 FULL UPFRONT PAYMENT: Upon confirmation of booking, Client shall pay ONE HUNDRED PERCENT (100%) of the Total Contract Amount as stated in Schedule A directly to AfriCrew Limited via the AfriCrew Platform. The booking is secured only once full payment is received.\n6.2 ESCROW HOLD: AfriCrew Limited shall hold the full amount in escrow as a licensed facilitator / payment agent. AfriCrew does not own these funds; it holds them for disbursement per this Agreement. Funds are not released to Crew Member in a lump sum.\n6.3 MILESTONE-BASED RELEASE: The Total Amount is divided into milestones as recorded in Schedule A.\n6.4 AUTHORITY TO DISBURSE PER MILESTONE: Client irrevocably instructs and authorises AfriCrew Limited to hold upfront payment in escrow and disburse each milestone upon Client approval or deemed approval after 72 hours.\n6.5 CREW PAYMENT TIMELINE: AfriCrew shall disburse each approved milestone within 12-48 business hours after Client approval.\n6.6 DEPOSIT LOGIC: Milestone 1 is released from escrow to secure Crew commitment and is non-refundable once released unless Crew fails to perform.\n6.7 NO CASH PAYMENTS: All payments must go through AfriCrew Platform.\n6.8 OVERTIME & EXPENSES: Pre-approved overtime shall be added as an additional milestone.\n6.9 TAXES & RECORDS: Each party is responsible for their own tax obligations under Kenyan law.\n\n" .
                "7. AUTHORITY & INDEPENDENT CONTRACTOR STATUS\n7.1 Crew Member is an independent contractor, not an employee.\n7.2 Both parties appoint AfriCrew Limited as limited agent for escrow, communication, and mediation.\n\n" .
                "8. CLIENT RESPONSIBILITIES\n8.1 Provide accurate event details, venue access, power, safety, and refreshments for shifts >5 hours.\n\n" .
                "9. CREW MEMBER RESPONSIBILITIES & WARRANTIES\n9.1 Arrive on time, professionally equipped, and comply with Kenyan law.\n\n" .
                "10. CANCELLATION, RESCHEDULING & NO-SHOW\n10.1 By Client: >7 days = full refund less Milestone 1 + 20% admin fee; 3-7 days = 50% refund less released milestones; <72 hrs = no refund of released milestones.\n10.2 By Crew: Immediate notification required; replacement attempted or full refund of unreleased funds.\n10.3 Rescheduling: 1 free reschedule >72 hrs prior subject to availability.\n10.4 No-show by Crew = full refund of unreleased funds + potential de-platforming.\n\n" .
                "11. FORCE MAJEURE: Neither party liable for unforeseen events beyond control. Rescheduling attempted or unreleased funds refunded.\n\n" .
                "12. INTELLECTUAL PROPERTY: Non-exclusive perpetual license granted upon full payment. Raw files remain property of Crew unless specified.\n\n" .
                "13. CONFIDENTIALITY & DATA PROTECTION: Complies with Kenya Data Protection Act, 2019.\n\n" .
                "14. LIMITATION OF LIABILITY & INDEMNIFICATION OF AFRICREW LIMITED: AfriCrew total liability capped at Platform Fee. Client & Crew indemnify AfriCrew.\n\n" .
                "15. INSURANCE, SAFETY & CONDUCT: Respectful conduct required.\n\n" .
                "16. DISPUTE RESOLUTION: Internal mediation via AfriCrew within 7 days, then NCIA or competent court in Kenya.\n\n" .
                "17. GENERAL PROVISIONS: Governed by laws of the Republic of Kenya. Electronic acceptance binding under KICA.\n\n" .
                "18. EXECUTION: Electronic acceptance via AfriCrew platform constitutes binding agreement.";

            $contract = Contract::create([
                'staffing_request_id' => $staffingRequest->id,
                'user_id' => $staffingRequest->user_id,
                'contract_number' => 'AFR-CTR-' . str_pad($staffingRequest->id, 6, '0', STR_PAD_LEFT),
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
