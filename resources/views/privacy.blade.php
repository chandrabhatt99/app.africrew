@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-8">
        
        <!-- Document Header Card -->
        <div class="bg-slate-900 text-white p-8 sm:p-12 rounded-3xl shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-xs tracking-wider uppercase border border-emerald-500/30">
                        Privacy Notice
                    </span>
                    <span class="text-slate-400 text-xs font-semibold">Version 2.0 • Effective Date: September 28, 2026</span>
                </div>
                
                <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                    Privacy Policy
                </h1>
                
                <p class="text-slate-300 text-sm sm:text-base max-w-3xl leading-relaxed">
                    AfriCrew connects Clients with event professionals and helps Crew showcase their work. This Policy explains how AfriCrew Limited collects, uses, shares and protects personal data, and the choices and rights available to you.
                </p>
                
                <div class="pt-4 flex flex-wrap items-center gap-6 text-xs text-slate-400 border-t border-slate-800">
                    <div><strong>Data Controller:</strong> AfriCrew Limited</div>
                    <div><strong>Company Reg:</strong> PVT-VQ1EBZPZ</div>
                    <div><strong>Privacy Requests:</strong> legal@africrew.com</div>
                    <div><strong>Support:</strong> support@africrew.com</div>
                </div>
            </div>
        </div>

        <!-- Main Body Card -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-12 shadow-sm text-slate-800 space-y-10 leading-relaxed text-sm">
            
            <!-- Quick TOC -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 space-y-3">
                <h3 class="font-extrabold text-slate-900 uppercase text-xs tracking-wider">Policy Navigation</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-xs font-semibold text-slate-600">
                    <a href="#p-1" class="hover:text-emerald-600 transition-colors">1. Who is responsible for your data</a>
                    <a href="#p-2" class="hover:text-emerald-600 transition-colors">2. Who this Policy covers</a>
                    <a href="#p-3" class="hover:text-emerald-600 transition-colors">3. How we obtain & collect data</a>
                    <a href="#p-4" class="hover:text-emerald-600 transition-colors">4. Sensitive data & legal grounds</a>
                    <a href="#p-5" class="hover:text-emerald-600 transition-colors">5. Application reviews & interviews</a>
                    <a href="#p-6" class="hover:text-emerald-600 transition-colors">6. Public profiles & visibility</a>
                    <a href="#p-7" class="hover:text-emerald-600 transition-colors">7. Messages, reviews & events</a>
                    <a href="#p-8" class="hover:text-emerald-600 transition-colors">8. Payments & service notices</a>
                    <a href="#p-9" class="hover:text-emerald-600 transition-colors">9. Crew images & promotion</a>
                    <a href="#p-10" class="hover:text-emerald-600 transition-colors">10. Cookies & permissions</a>
                    <a href="#p-11" class="hover:text-emerald-600 transition-colors">11. Who may receive personal data</a>
                    <a href="#p-12" class="hover:text-emerald-600 transition-colors">12. Data outside Kenya</a>
                    <a href="#p-13" class="hover:text-emerald-600 transition-colors">13. Search AI & automated decisions</a>
                    <a href="#p-14" class="hover:text-emerald-600 transition-colors">14. How long data is kept</a>
                    <a href="#p-15" class="hover:text-emerald-600 transition-colors">15. Security & incident response</a>
                    <a href="#p-16" class="hover:text-emerald-600 transition-colors">16. Children & attendee information</a>
                    <a href="#p-17" class="hover:text-emerald-600 transition-colors">17. Rights available to you</a>
                    <a href="#p-18" class="hover:text-emerald-600 transition-colors">18. How to make a request</a>
                    <a href="#p-19" class="hover:text-emerald-600 transition-colors">19. Complaints & ODPC remedies</a>
                    <a href="#p-20" class="hover:text-emerald-600 transition-colors">20. Updates to this Policy</a>
                </div>
            </div>

            <!-- Sections -->
            <section id="p-1" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">1</span>
                    Who is responsible for your data
                </h2>
                <p>AfriCrew Limited is the data controller for personal data where we decide why and how it is processed, including account administration, Crew assessment, platform security and our own marketing. Company registration number: PVT-VQ1EBZPZ. Registered address: GB16, 5th Floor, Phoenix Point House, Eastern Bypass – Membley, RWQV+QW Ruiru, Kenya. Website: <a href="https://africrew.com" class="text-emerald-600 underline font-bold" target="_blank">https://africrew.com</a>.</p>
                <p>Privacy enquiries and rights requests: <a href="mailto:legal@africrew.com" class="text-emerald-600 underline font-bold">legal@africrew.com</a>. Account support and security reports: <a href="mailto:support@africrew.com" class="text-emerald-600 underline font-bold">support@africrew.com</a>. Address your privacy request to “AfriCrew Privacy Team”. If we appoint a designated data protection officer, we will provide their contact details through this privacy contact; this notice does not claim that an officer or regulatory registration already exists.</p>
                <p>Clients, Crew, payment providers and other organisations may separately decide how to use data for their own activities and act as independent controllers. For example, an organiser controls its guest list, and a payment provider may perform its own legally required checks. Where we process information solely on another organisation’s documented instructions, our processor duties are governed by applicable law and the relevant data-processing agreement.</p>
            </section>

            <section id="p-2" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">2</span>
                    Who this Policy covers
                </h2>
                <p>This Policy applies to visitors, Clients and their representatives, Crew applicants and members, referees, invited contacts, complainants and people identifiable in material submitted to AfriCrew. A person can have both Client and Crew roles on one identity. We link those roles for account administration and relevant security checks, while limiting disclosures to what the particular purpose requires.</p>
                <p>It does not replace another controller’s privacy notice, an employer’s staff notice or a client’s event privacy notice. Follow any additional notice shown for a particular feature. If a feature such as location sharing, AI assistance or a social integration is not enabled, mentioning it here does not mean we collect its data.</p>
            </section>

            <section id="p-3" class="space-y-4 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">3</span>
                    How we obtain information & The information we collect
                </h2>
                <p>We receive information directly when you register, apply, attend an interview, create a profile, enquire, book, pay, message, review or contact support. Other sources include a referrer, referee, booking counterparty, authorised business representative, payment provider, identity or qualification verification source, and a person submitting a complaint or permitted work photograph.</p>

                <!-- Data Categories Table -->
                <div class="overflow-x-auto my-4 border border-slate-200 rounded-2xl">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white font-extrabold uppercase tracking-wider text-[11px]">
                                <th class="p-3.5 border-b border-slate-800 w-1/3">Category</th>
                                <th class="p-3.5 border-b border-slate-800">Examples and Limits</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Account and contact</td>
                                <td class="p-3">Name, email, phone, username, account identifier, age or date of birth where needed, sign-in credentials or provider identifiers, role and communication preferences. Business contacts may include an organisation name and authorised representative.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Crew profile and digital CV</td>
                                <td class="p-3">Professional role, bio, location or service area, languages, skills, qualifications, education, experience, rates where supplied, availability, profile photo, cover image, portfolio and reviews. Public visibility follows section 6.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Verification and interviews</td>
                                <td class="p-3">Identity evidence requested for a stated purpose, qualification and licence evidence, referee responses, interview scheduling, assessment notes, outcome and reasons. Audio or video recordings require the additional notice and choice in section 5.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Bookings and contracts</td>
                                <td class="p-3">Brief, event location and dates, call times, scope, deliverables, quotation and negotiation history, agreement version, signature or acceptance evidence, milestones, attendance, progress and completion records.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Payments and billing</td>
                                <td class="p-3">Amounts, transaction references, provider status, invoices, subscription and fee records, refund or dispute information, and payout or tax details where necessary. See section 8 for payment-provider boundaries.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Messages and support</td>
                                <td class="p-3">In-app messages, attachments, voice notes you send, support correspondence, complaints and evidence supplied by the parties. These are not public profile content.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Referrals and third parties</td>
                                <td class="p-3">An invitee’s name and selected contact detail, referral source and response; a referee’s professional contact details and relevant feedback; limited event or participant data included in an authorised brief or image.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Device and usage</td>
                                <td class="p-3">IP address, browser or app version, device type, session identifiers, access times, pages or features used, crash and security logs, and cookie data where used as explained in section 10.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Location and promotion choices</td>
                                <td class="p-3">Chosen service area, approximate location inferred from IP where used, and precise device location only for a disclosed feature with permission. Promotional-image consent, approved materials, permitted channels and withdrawal records.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="p-4" class="space-y-4 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">4</span>
                    Sensitive information and legal grounds
                </h2>
                <p>Identity documents and event briefs can reveal more than is needed. Provide only requested fields and use a secure upload method. Limit access and remove or redact unnecessary details. Do not post ID numbers, financial credentials, home addresses, health information or other private data in public profiles or reviews.</p>
                
                <!-- Legal Grounds Table -->
                <div class="overflow-x-auto my-4 border border-slate-200 rounded-2xl">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white font-extrabold uppercase tracking-wider text-[11px]">
                                <th class="p-3.5 border-b border-slate-800 w-1/4">Purpose</th>
                                <th class="p-3.5 border-b border-slate-800 w-1/3">Relevant Data</th>
                                <th class="p-3.5 border-b border-slate-800">Legal Ground & Limits</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Create & manage accounts</td>
                                <td class="p-3">Account, contact, roles & authentication</td>
                                <td class="p-3">Contract or pre-contract steps. Legitimate interests for security and administration.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Assess Crew applications</td>
                                <td class="p-3">Profile, identity, qualifications, interview notes, references</td>
                                <td class="p-3">Pre-contract steps; legitimate interests in verification and integrity.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Publish digital CV & discovery</td>
                                <td class="p-3">Selected profile, portfolio, service area & availability</td>
                                <td class="p-3">Contract for public profile service you choose. Required consent for optional disclosures.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Administer gigs</td>
                                <td class="p-3">Enquiries, quotes, messages, contracts, progress & acceptance</td>
                                <td class="p-3">Contract performance or requested pre-contract steps.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Process payments</td>
                                <td class="p-3">Billing, payout, transaction & refund records</td>
                                <td class="p-3">Contract; accounting, tax & legal obligations; fraud prevention.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Support & resolve disputes</td>
                                <td class="p-3">Support records, messages, evidence & audit logs</td>
                                <td class="p-3">Contract; legitimate interests in resolving complaints and defending legal claims.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Protect platform</td>
                                <td class="p-3">Limited logs, errors & security events</td>
                                <td class="p-3">Legitimate interests in reliable and secure services.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Optional marketing</td>
                                <td class="p-3">Selected contact channel & preferences</td>
                                <td class="p-3">Express consent for specified marketing purpose.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Promote Crew & AfriCrew</td>
                                <td class="p-3">Approved name, public profile, images & scope</td>
                                <td class="p-3">Separate express consent under clause 16A of Terms.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="p-5" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">5</span>
                    Application reviews, interviews and referrals
                </h2>
                <p>You may apply directly or through a referral. AfriCrew reviews account details and supporting information, decides whom to invite for an interview, and determines approval after assessment. Applicants selected to proceed must complete an interview before approval to operate. Participation does not guarantee approval.</p>
                <p>An interview may take place through an identified online meeting provider. Attending a live interview is not consent to an audio or video recording, transcription or AI analysis. Before using those functions, we will explain the purpose, provider, access, and retention, and obtain any required separate consent. Where recording is optional, declining it will not itself disqualify an applicant; a live unrecorded interview or reasonable alternative should be offered.</p>
            </section>

            <section id="p-6" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">6</span>
                    Public profiles and visibility
                </h2>
                <p>When you choose to publish a digital CV, selected professional information becomes visible to visitors, potential clients, and search engines. It may include your name, professional photo, bio, skills, work history, portfolio, service area and reviews. You should be shown what will be public before publication. Do not include private verification documents, interview records, payment details, or private messages in the public profile.</p>
            </section>

            <section id="p-7" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">7</span>
                    Messages, reviews and event information
                </h2>
                <p>Messages, briefs and files are shared with the intended participants and authorised representatives involved in the booking. Authorised AfriCrew personnel may access relevant records for a support request, a reported breach, a payment dispute, a security investigation, or a legal obligation, with access limited to the purpose.</p>
                <p>Reviews and responses may appear publicly with the displayed reviewer identity and booking context. Do not include attendee lists, sensitive allegations, personal phone numbers or private financial records in reviews.</p>
            </section>

            <section id="p-8" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">8</span>
                    Payments and service notifications
                </h2>
                <p>Card, mobile-money and bank transactions involve the provider identified in the payment flow. AfriCrew uses the necessary transaction references, amounts, status, billing and payout details to administer funding, releases, withdrawals, fees and refunds. Never send card security codes, mobile-money PINs or passwords through chat or support.</p>
            </section>

            <section id="p-9" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">9</span>
                    Crew images and platform promotion
                </h2>
                <p>With your separately recorded express consent, AfriCrew may use your professional name, selected public profile, profile link, likeness, portfolio and permitted event-site work photographs to showcase your skills, attract Clients and promote AfriCrew. Withdraw promotional consent anytime via <a href="mailto:legal@africrew.com" class="text-emerald-600 underline font-bold">legal@africrew.com</a>.</p>
            </section>

            <section id="p-10" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">10</span>
                    Cookies, analytics and device permissions
                </h2>
                <p>Cookies, local storage, app identifiers or similar tools may maintain sign-in, security, preferences or optional analytics. Essential tools are limited to what the requested service requires. Non-essential analytics and tracking remain off until consent is obtained.</p>
            </section>

            <section id="p-11" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">11</span>
                    Who may receive personal data
                </h2>
                <p>We share only what is necessary for the stated purpose. Recipients may include booking counterparties, providers of hosting, authentication, payment, support, and professional advisers. We do not sell personal data to third parties.</p>
            </section>

            <section id="p-12" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">12</span>
                    Data outside Kenya
                </h2>
                <p>Cloud hosting, support, communications, payment, or other providers may access or store data outside Kenya under strict statutory protection safeguards, data transfer agreements, and applicable Kenyan Data Protection Act standards.</p>
            </section>

            <section id="p-13" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">13</span>
                    Search AI and automated decisions
                </h2>
                <p>Search recommendations use role, location, availability, and skills. Applications and vetting involve direct human assessment. Automated logic does not execute unconfirmed financial charges or contract approvals without user authorization.</p>
            </section>

            <section id="p-14" class="space-y-4 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">14</span>
                    How long data is kept
                </h2>
                
                <!-- Retention Table -->
                <div class="overflow-x-auto my-4 border border-slate-200 rounded-2xl">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white font-extrabold uppercase tracking-wider text-[11px]">
                                <th class="p-3.5 border-b border-slate-800 w-1/3">Record Category</th>
                                <th class="p-3.5 border-b border-slate-800">Retention Criteria and Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Active account & profile</td>
                                <td class="p-3">Kept while active. After closure, public display is removed and unnecessary fields deleted/anonymised.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Applications & verification</td>
                                <td class="p-3">Kept for assessment, appeal window, and fraud prevention. Raw ID copies deleted when no longer needed.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Bookings, contracts & payments</td>
                                <td class="p-3">Kept as required for statutory accounting, tax compliance, and legal claim limitation periods.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Messages & support</td>
                                <td class="p-3">Retained for relevant service, complaint or dispute resolution. Unnecessary attachments removed early.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Invitations & marketing</td>
                                <td class="p-3">Invitation data kept during delivery window. Minimal opt-out records kept to respect user choices.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="p-15" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">15</span>
                    Security and incident response
                </h2>
                <p>We apply appropriate technical and organizational safeguards including encryption, access controls, and auditing. Where Kenyan law requires notification of a breach, we notify the ODPC without delay and within 72 hours of awareness.</p>
            </section>

            <section id="p-16" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">16</span>
                    Children and incidental attendee information
                </h2>
                <p>AfriCrew accounts and booked professionals must be at least 18 years of age. Underage accounts will be restricted. Event photos containing children require applicable parent or guardian consent.</p>
            </section>

            <section id="p-17" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">17</span>
                    Rights available to you
                </h2>
                <p>Under the Kenya Data Protection Act 2019, you have rights to access, rectify, erase, object to processing, request data portability, and withdraw consent. Direct marketing objections are honoured immediately.</p>
            </section>

            <section id="p-18" class="space-y-4 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">18</span>
                    How to make a request
                </h2>
                <p>Email <a href="mailto:legal@africrew.com" class="text-emerald-600 underline font-bold">legal@africrew.com</a> with your request details. Statutory calendar-day response timelines under Kenyan regulations:</p>
                
                <!-- Request Timelines Table -->
                <div class="overflow-x-auto my-4 border border-slate-200 rounded-2xl">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white font-extrabold uppercase tracking-wider text-[11px]">
                                <th class="p-3.5 border-b border-slate-800 w-1/4">Request Type</th>
                                <th class="p-3.5 border-b border-slate-800">Kenyan Response Requirement</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Access</td>
                                <td class="p-3">Provide access within 7 days of request, free of charge.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Correction</td>
                                <td class="p-3">Make necessary corrections within 14 days; written reasons within 7 days if declined.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Erasure</td>
                                <td class="p-3">Respond within 14 days; explain any statutory retention requirement.</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="p-3 font-bold text-slate-900">Objection</td>
                                <td class="p-3">Comply within 14 days free of charge. Direct marketing stops immediately.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="p-3 font-bold text-slate-900">Portability</td>
                                <td class="p-3">Complete eligible request within 30 days.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="p-19" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">19</span>
                    Complaints and independent remedies
                </h2>
                <p>If you believe your data has been handled improperly, contact <a href="mailto:legal@africrew.com" class="text-emerald-600 underline font-bold">legal@africrew.com</a>. You may also lodge a complaint directly with the Office of the Data Protection Commissioner (ODPC) at <a href="https://www.odpc.go.ke/file-a-complaint/" class="text-emerald-600 underline font-bold" target="_blank">https://www.odpc.go.ke/file-a-complaint/</a>.</p>
            </section>

            <section id="p-20" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">20</span>
                    Updates to this Policy
                </h2>
                <p>We will keep this Policy aligned with actual practices and state its version and effective date. For material changes, appropriate notice will be given before new processing begins.</p>
            </section>

        </div>

    </div>
</div>
@endsection
