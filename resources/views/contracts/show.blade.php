@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 sm:px-6">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Header Controls -->
        <div class="flex items-center justify-between">
            <a href="javascript:history.back()" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1">
                ← Back to Portal
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 text-amber-400 font-bold text-xs hover:bg-slate-800 shadow-sm flex items-center gap-2">
                🖨️ Print / Save PDF Agreement
            </button>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs">
                ✓ {{ session('success') }}
            </div>
        @endif

        <!-- Document Sheet Card -->
        <div class="bg-white border border-slate-200 rounded-3xl p-8 sm:p-12 shadow-lg space-y-8 print:shadow-none print:border-none">
            
            <!-- Contract Header -->
            <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <img src="{{ asset('images/africrew_logo.jpg') }}" alt="AfriCrew" class="h-10 w-auto mb-2">
                    <h1 class="text-2xl font-black text-slate-950 uppercase tracking-tight">AFRICREW - GENERAL CONTRACT FOR SERVICE</h1>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Service Agreement Between Client and Crew Member (Facilitated via AfriCrew Limited Platform)</p>
                    <p class="text-xs text-amber-600 font-bold mt-1">Agreement Reference: <strong>{{ $contract->contract_number }}</strong></p>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase {{ $contract->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        Status: {{ str_replace('_', ' ', strtoupper($contract->status)) }}
                    </span>
                    <div class="text-[11px] text-slate-400 mt-1">Contract Date: {{ $contract->created_at->format('Y-m-d') }}</div>
                </div>
            </div>

            <!-- Preamble -->
            <div class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200">
                THIS AGREEMENT is made and entered into on {{ $contract->created_at->format('Y-m-d') }} (the "Contract Date"), by and between the parties detailed below. <strong>AfriCrew Limited</strong> ("AfriCrew", "Platform", "System", or "Company") facilitates this Agreement as an intermediary agent and booking manager.
            </div>

            <!-- Section 1: Parties & Platform Role -->
            <div class="space-y-3 text-xs">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">1. PARTIES AND PLATFORM ROLE</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                        <div class="font-extrabold text-slate-900 text-xs uppercase tracking-wider text-amber-600">A. THE CLIENT</div>
                        <div class="font-bold text-slate-900">{{ $contract->staffingRequest->full_name ?: 'Client' }}</div>
                        <div class="text-slate-600">Company: {{ $contract->staffingRequest->company_name ?: 'N/A' }}</div>
                        <div class="text-slate-600">ID / Ref: CLT-{{ $contract->staffingRequest->user_id ?: 'GUEST' }}</div>
                        <div class="text-slate-600">Phone: {{ $contract->staffingRequest->phone ?: 'N/A' }}</div>
                        <div class="text-slate-600">Email: {{ $contract->staffingRequest->email ?: 'N/A' }}</div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                        <div class="font-extrabold text-slate-900 text-xs uppercase tracking-wider text-amber-600">B. THE CREW MEMBER</div>
                        <div class="font-bold text-slate-900">{{ $contract->professional->full_name ?? 'Assigned AfriCrew Professional' }}</div>
                        <div class="text-slate-600">Profile ID: AFR-CREW-{{ $contract->professional_id ?: $contract->staffing_request_id }}</div>
                        <div class="text-slate-600">Phone: {{ $contract->professional->phone ?? 'Via Platform' }}</div>
                        <div class="text-slate-600">Email: {{ $contract->professional->email ?? 'Via Platform' }}</div>
                        <div class="text-slate-600">Category: {{ $contract->staffingRequest->category ?: 'Event Services' }}</div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                        <div class="font-extrabold text-slate-900 text-xs uppercase tracking-wider text-amber-600">C. THE FACILITATOR</div>
                        <div class="font-bold text-slate-900">AfriCrew Limited</div>
                        <div class="text-slate-600">Reg No: PVT-VQ1EBZPZ</div>
                        <div class="text-slate-600">Contact: support@africrew.com</div>
                        <div class="text-slate-500 text-[11px] leading-tight pt-1">
                            AfriCrew Limited is NOT the direct employer. AfriCrew operates the platform, manages secure escrow, and disburses milestone payments.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Booking Summary (Schedule A Table) -->
            <div class="space-y-3 text-xs">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">2. BOOKING SUMMARY - SYSTEM GENERATED DETAILS (SCHEDULE A)</h3>
                
                <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-900 text-white font-extrabold uppercase text-[11px] tracking-wider">
                                <th class="p-3 border-b border-slate-800 w-1/3">Field</th>
                                <th class="p-3 border-b border-slate-800">Details (Auto-Filled by System)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Contract Date</td>
                                <td class="p-3 font-mono text-slate-700">{{ $contract->created_at->format('Y-m-d') }}</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Booking ID</td>
                                <td class="p-3 font-mono font-bold text-amber-600">{{ $contract->contract_number }}</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Event Date(s)</td>
                                <td class="p-3 text-slate-800 font-semibold">{{ $contract->staffingRequest->event_date?->format('Y-m-d') ?: 'Per Schedule' }}</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Event Location / Venue</td>
                                <td class="p-3 text-slate-800">{{ $contract->staffingRequest->location ?: 'Venue Address Specified in Booking' }}</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Working Hours Per Day</td>
                                <td class="p-3 text-slate-800">{{ $contract->staffingRequest->shift_duration ?: '8 Hours' }} ({{ $contract->staffingRequest->start_time ?: '08:00' }} - {{ $contract->staffingRequest->end_time ?: '17:00' }})</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Reporting Time</td>
                                <td class="p-3 text-slate-800">30 minutes before shift start (07:30 AM)</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">TOTAL CONTRACT AMOUNT (100% Upfront)</td>
                                <td class="p-3 font-extrabold text-slate-900 text-sm">{{ $contract->staffingRequest->currency ?: 'KES' }} {{ number_format($contract->staffingRequest->budget ?: 0, 2) }}</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Platform Service Fee (Included)</td>
                                <td class="p-3 text-slate-700 font-semibold">Included in Total Contract Amount</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">MILESTONE 1 - Booking Secured / Deposit Release</td>
                                <td class="p-3 text-slate-800">30% = {{ $contract->staffingRequest->currency ?: 'KES' }} {{ number_format(($contract->staffingRequest->budget ?: 0) * 0.30, 2) }} - Released on booking confirmation</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">MILESTONE 2 - Mid-progress / Event Day Start</td>
                                <td class="p-3 text-slate-800">40% = {{ $contract->staffingRequest->currency ?: 'KES' }} {{ number_format(($contract->staffingRequest->budget ?: 0) * 0.40, 2) }} - Released on Event Check-in / Raw Delivery</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">MILESTONE 3 - Final Delivery & Client Approval</td>
                                <td class="p-3 text-slate-800">30% = {{ $contract->staffingRequest->currency ?: 'KES' }} {{ number_format(($contract->staffingRequest->budget ?: 0) * 0.30, 2) }} - Released on approval of final deliverables</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Deliverables Summary</td>
                                <td class="p-3 text-slate-800">{{ $contract->staffingRequest->requirements ?: 'As described by Client at booking' }}</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Delivery Deadline</td>
                                <td class="p-3 text-slate-800">Within 72 hours of event conclusion</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-[11px] text-slate-500 italic">Schedule A is generated per booking from the milestone agreement between Client and Crew Member. In case of conflict between this main Agreement and Schedule A, Schedule A shall prevail for booking-specific facts, while this Agreement shall prevail for legal terms.</p>
            </div>

            <!-- Clauses 3 to 18 formatted nicely -->
            <div class="space-y-6 text-xs text-slate-700 leading-relaxed pt-4 border-t border-slate-200">
                
                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">3. SCOPE OF WORK</h4>
                    <p>3.1 The Crew Member agrees to provide the services described in Schedule A and the Client's booking request ("Services") with professional skill, care, and diligence.</p>
                    <p>3.2 This is a generic agreement designed to cover all categories listed on AfriCrew. The Deliverables section defines the specific tasks, equipment to be provided by Crew (if any), and expected outcomes.</p>
                    <p>3.3 Any work outside the agreed scope shall require a written variation via the AfriCrew platform and may trigger a new milestone or additional fee.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">4. TERM, LOCATION & WORKING HOURS</h4>
                    <p>4.1 Term: Commences on Contract Date and remains in force until full delivery, acceptance, and final milestone disbursement.</p>
                    <p>4.2 Location: Services shall be performed at the Location specified in Schedule A.</p>
                    <p>4.3 Working Hours: As per Schedule A. Any time beyond the agreed hours is Overtime and billable at the Overtime Rate, to be added as an extra milestone or charged via the platform if pre-approved.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">5. DELIVERABLES & ACCEPTANCE</h4>
                    <p>5.1 Deliverables: The Crew Member shall deliver exactly what is listed in Schedule A.</p>
                    <p>5.2 Standard of Work: Professional standard customary to the trade or business.</p>
                    <p>5.3 Proof of Service: For time-based services, delivery is confirmed by on-site completion and sign-off. For creative services, delivery is via upload/link as per parties' agreement.</p>
                    <p>5.4 Client Review Period: For each milestone that requires approval, Client has 72 hours (or as stated in Schedule A) from notification to review and request reasonable revisions. If no objection is raised within that period, that milestone Deliverable shall be deemed Accepted.</p>
                    <p>5.5 Revisions: One round of reasonable revisions is included (where applicable) per milestone if the deliverable does not match Schedule A. Materially new requests are a variation.</p>
                    <p>5.6 Final Acceptance: Upon acceptance (explicit or deemed) of a milestone, Client authorises AfriCrew Limited to release that milestone payment. This authorisation is irrevocable once that milestone is accepted.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">6. PAYMENT TERMS, ESCROW & MILESTONE DISBURSEMENT</h4>
                    <p>6.1 FULL UPFRONT PAYMENT: Upon confirmation of booking, Client shall pay ONE HUNDRED PERCENT (100%) of the Total Contract Amount as stated in Schedule A directly to AfriCrew Limited via the AfriCrew Platform. The booking is secured only once full payment is received.</p>
                    <p>6.2 ESCROW HOLD: AfriCrew Limited shall hold the full amount in escrow as a licensed facilitator / payment agent. AfriCrew does not own these funds; it holds them for disbursement per this Agreement. Funds are not released to Crew Member in a lump sum.</p>
                    <p>6.3 MILESTONE-BASED RELEASE: The Total Amount is divided into milestones as agreed by Client and Crew Member at booking and recorded in Schedule A.</p>
                    <p>6.4 AUTHORITY TO DISBURSE PER MILESTONE: Client irrevocably instructs and authorises AfriCrew Limited to hold the 100% upfront payment in escrow and disburse per milestones upon Client approval (or deemed approval after 72 hours).</p>
                    <p>6.5 CREW PAYMENT TIMELINE: AfriCrew shall disburse each approved milestone to Crew Member within 12-48 business hours after Client approval (or deemed approval).</p>
                    <p>6.6 DEPOSIT LOGIC: The "Deposit" is Milestone 1 - the first release from the already-paid escrow to secure Crew Member's commitment. It becomes non-refundable once released, except where Crew Member fails to perform.</p>
                    <p>6.7 NO CASH PAYMENTS: All payments must go through AfriCrew Platform. Off-platform payments void AfriCrew protection, escrow, and indemnity.</p>
                    <p>6.8 OVERTIME & EXPENSES: Pre-approved overtime and expenses shall be added as an additional milestone or billed via the platform.</p>
                    <p>6.9 TAXES & RECORDS: Each party is responsible for their own tax obligations under Kenyan law.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">7. AUTHORITY & INDEPENDENT CONTRACTOR STATUS</h4>
                    <p>7.1 Crew Member is an independent contractor, not an employee, agent, or partner of AfriCrew Limited or Client.</p>
                    <p>7.2 Client grants temporary authority to Crew Member to be present at Location and make minor operational decisions required for professional delivery.</p>
                    <p>7.3 Both parties appoint AfriCrew Limited as limited agent to facilitate communication, hold funds in escrow, disburse per milestones, mediate disputes, and enforce delivery rules.</p>
                    <p>7.4 Neither Client nor Crew may bind AfriCrew Limited to any external contract or liability.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">8. CLIENT RESPONSIBILITIES</h4>
                    <p>8.1 Provide accurate event details, access, permits, and safety conditions.</p>
                    <p>8.2 Ensure venue access, working space, power, and security for Crew and equipment.</p>
                    <p>8.3 Provide meals/refreshments for bookings exceeding 5 hours, unless otherwise agreed.</p>
                    <p>8.4 Do not request illegal, unsafe, or unethical tasks.</p>
                    <p>8.5 Be available or appoint a representative for milestone approvals.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">9. CREW MEMBER RESPONSIBILITIES & WARRANTIES</h4>
                    <p>9.1 Arrive on time, professionally dressed and equipped.</p>
                    <p>9.2 Carry out Services professionally and in compliance with Kenyan law.</p>
                    <p>9.3 Maintain own tools/equipment and care for Client property.</p>
                    <p>9.4 Not subcontract without prior written consent via AfriCrew Platform.</p>
                    <p>9.5 Warrants work is original (where creative) and does not infringe third-party rights.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">10. CANCELLATION, RESCHEDULING & NO-SHOW (Under 100% Escrow Model)</h4>
                    <p>10.1 By Client: >7 days before event = full refund less Milestone 1 if released + 20% admin fee; 3-7 days = 50% refund less released milestones; <72 hours = no refund of released milestones. If event is cancelled after Crew has reported on-site, Client is liable for full payment.</p>
                    <p>10.2 By Crew Member: Must notify AfriCrew immediately (min 24 hrs). Replacement attempted or full refund of unreleased milestones.</p>
                    <p>10.3 Rescheduling: One free reschedule if requested >72 hours before, subject to Crew availability.</p>
                    <p>10.4 No-Show: Crew no-show = full refund of unreleased milestones + potential de-platforming. Client no-show = all milestones deemed earned and released to Crew.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">11. FORCE MAJEURE & UNFORESEEN ISSUES</h4>
                    <p>Neither party is liable for failure due to events beyond reasonable control. Affected party must notify AfriCrew immediately. If rescheduling is impossible, unreleased escrowed funds will be refunded less costs already incurred.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">12. INTELLECTUAL PROPERTY</h4>
                    <p>12.1 For creative services: Upon final milestone payment, Client receives a non-exclusive perpetual license. Crew retains portfolio rights unless confidentiality add-on purchased.</p>
                    <p>12.2 Raw files remain property of Crew unless Schedule A states Raw Files Included.</p>
                    <p>12.3 For non-creative services, no IP transfer.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">13. CONFIDENTIALITY & DATA PROTECTION</h4>
                    <p>Both parties keep private information confidential. AfriCrew complies with the Kenya Data Protection Act, 2019.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">14. LIMITATION OF LIABILITY AND INDEMNIFICATION OF AFRICREW LIMITED</h4>
                    <p>14.1 AfriCrew provides a venue for connection and escrow only and does not supervise day-to-day work.</p>
                    <p>14.2 AfriCrew's total liability shall not exceed the Platform Service Fee earned on that booking.</p>
                    <p>14.3 Client and Crew jointly and severally INDEMNIFY, DEFEND, HOLD HARMLESS AfriCrew Limited, its directors, officers, employees, and agents from all claims, losses, damages, liabilities, costs, and expenses.</p>
                    <p>14.4 Once milestone funds are disbursed per Client approval, Client waives claims against AfriCrew for that disbursement.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">15. INSURANCE, SAFETY & CONDUCT</h4>
                    <p>15.1 Crew is responsible for own health/equipment insurance. 15.2 Client is responsible for public liability where required. 15.3 Respectful conduct required; harassment or illegal activity results in immediate termination.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">16. DISPUTE RESOLUTION</h4>
                    <p>16.1 Internal Mediation via AfriCrew Platform within 7 days. 16.2 Refer unresolved disputes to Nairobi Centre for International Arbitration (NCIA) or competent court in Kenya.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">17. GENERAL PROVISIONS</h4>
                    <p>17.1 Governing Law: Laws of the Republic of Kenya. 17.5 Electronic signature via AfriCrew Platform (click to accept, OTP, e-signature) is legally binding under the Kenya Information and Communication Act.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-xs">18. EXECUTION</h4>
                    <p>By accepting on the AfriCrew Platform, both parties confirm they have read, understood, and agree to this General Contract for Service (Escrow Milestone Model).</p>
                </div>

            </div>

            <!-- Signatures Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pt-6 border-t border-slate-200">
                
                <!-- Client Signature Box -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase text-slate-500">CLIENT EXECUTION</h4>
                    @if($contract->client_signed_at)
                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 space-y-2">
                            <div class="font-serif italic text-lg font-bold text-emerald-950">{{ $contract->client_signature }}</div>
                            <div class="text-[10px] text-emerald-700 font-semibold">Digitally Signed on {{ $contract->client_signed_at->format('M j, Y g:i A') }}</div>
                            <div class="text-[9px] text-emerald-600 font-mono">IP & Electronic Acceptance Recorded</div>
                            
                            <div class="pt-2 border-t border-emerald-200">
                                <a href="{{ route('client.dashboard') }}#payment-section" class="w-full inline-block text-center py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition-all">
                                    Proceed to Escrow Payment 💳 →
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center space-y-3">
                            <p class="text-xs text-slate-400 italic">Pending Client Digital Signature</p>
                            <form method="POST" action="{{ route('contracts.sign', $contract->id) }}" class="space-y-2">
                                @csrf
                                <input type="hidden" name="sign_type" value="client">
                                <input type="text" name="signature_name" placeholder="Type full legal name to accept contract" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                                <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-amber-400 font-bold text-xs rounded-xl shadow-xs">
                                    e-Accept as Client
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Crew Signature Box -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase text-slate-500">CREW MEMBER EXECUTION</h4>
                    @if($contract->crew_signed_at)
                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 space-y-1">
                            <div class="font-serif italic text-lg font-bold text-emerald-950">{{ $contract->crew_signature }}</div>
                            <div class="text-[10px] text-emerald-700 font-semibold">Digitally Signed on {{ $contract->crew_signed_at->format('M j, Y g:i A') }}</div>
                            <div class="text-[9px] text-emerald-600 font-mono">IP & Electronic Acceptance Recorded</div>
                        </div>
                    @else
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center space-y-3">
                            <p class="text-xs text-slate-400 italic">Pending Crew Member Digital Signature</p>
                            <form method="POST" action="{{ route('contracts.sign', $contract->id) }}" class="space-y-2">
                                @csrf
                                <input type="hidden" name="sign_type" value="crew">
                                <input type="text" name="signature_name" placeholder="Type full legal name to accept contract" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500">
                                <button type="submit" class="w-full py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs rounded-xl shadow-xs">
                                    e-Accept as Crew Member
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Facilitator Witness -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center text-xs text-slate-500 space-y-1">
                <div class="font-bold text-slate-800">WITNESSED / FACILITATED BY: AfriCrew Limited (System Automated Approval)</div>
                <div>Booking ID: {{ $contract->contract_number }} | Timestamp: {{ $contract->created_at->toIso8601String() }}</div>
            </div>

        </div>
    </div>
</div>
@endsection
