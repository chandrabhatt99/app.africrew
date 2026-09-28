@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-8">
        
        <!-- Document Header Card -->
        <div class="bg-slate-900 text-white p-8 sm:p-12 rounded-3xl shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-400 font-extrabold text-xs tracking-wider uppercase border border-amber-500/30">
                        Legal Document
                    </span>
                    <span class="text-slate-400 text-xs font-semibold">Version 2.1 • Effective Date: September 28, 2026</span>
                </div>
                
                <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                    Platform Terms & Conditions
                </h1>
                
                <p class="text-slate-300 text-sm sm:text-base max-w-3xl leading-relaxed">
                    These Terms govern your use of AfriCrew’s website, applications, digital CV profiles and related platform features. They form an agreement between you and AfriCrew Limited. A separate Client and Crew Services Agreement governs each confirmed booking.
                </p>
                
                <div class="pt-4 flex flex-wrap items-center gap-6 text-xs text-slate-400 border-t border-slate-800">
                    <div><strong>Company Reg:</strong> PVT-VQ1EBZPZ</div>
                    <div><strong>Support:</strong> support@africrew.com</div>
                    <div><strong>Legal Notices:</strong> legal@africrew.com</div>
                    <div><strong>Location:</strong> Ruiru, Kenya</div>
                </div>
            </div>
        </div>

        <!-- Main Body Card -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-12 shadow-sm text-slate-800 space-y-10 leading-relaxed text-sm">
            
            <!-- Quick Index / TOC -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 space-y-3">
                <h3 class="font-extrabold text-slate-900 uppercase text-xs tracking-wider">Table of Contents</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-xs font-semibold text-slate-600">
                    <a href="#sec-1" class="hover:text-amber-600 transition-colors">1. Who we are & what we provide</a>
                    <a href="#sec-2" class="hover:text-amber-600 transition-colors">2. Acceptance & related documents</a>
                    <a href="#sec-3" class="hover:text-amber-600 transition-colors">3. Eligibility & account roles</a>
                    <a href="#sec-4" class="hover:text-amber-600 transition-colors">4. Account security</a>
                    <a href="#sec-5" class="hover:text-amber-600 transition-colors">5. Referral & approval process</a>
                    <a href="#sec-6" class="hover:text-amber-600 transition-colors">6. Digital CV & verification labels</a>
                    <a href="#sec-7" class="hover:text-amber-600 transition-colors">7. Subscriptions & paid features</a>
                    <a href="#sec-8" class="hover:text-amber-600 transition-colors">8. Enquiries, quotes & contracts</a>
                    <a href="#sec-9" class="hover:text-amber-600 transition-colors">9. Prices, fees & taxes</a>
                    <a href="#sec-10" class="hover:text-amber-600 transition-colors">10. Funding & provider arrangements</a>
                    <a href="#sec-11" class="hover:text-amber-600 transition-colors">11. Payment authority & approval</a>
                    <a href="#sec-12" class="hover:text-amber-600 transition-colors">12. Changes, cancellations & disputes</a>
                    <a href="#sec-13" class="hover:text-amber-600 transition-colors">13. Conduct & prohibited use</a>
                    <a href="#sec-14" class="hover:text-amber-600 transition-colors">14. Keeping bookings on platform</a>
                    <a href="#sec-15" class="hover:text-amber-600 transition-colors">15. Your content & permissions</a>
                    <a href="#sec-16" class="hover:text-amber-600 transition-colors">16. Reviews, complaints & promotion</a>
                    <a href="#sec-17" class="hover:text-amber-600 transition-colors">17. Personal data & communications</a>
                    <a href="#sec-18" class="hover:text-amber-600 transition-colors">18. Search recommendations & AI</a>
                    <a href="#sec-19" class="hover:text-amber-600 transition-colors">19. Availability & 3rd party services</a>
                    <a href="#sec-20" class="hover:text-amber-600 transition-colors">20. Restrictions, suspension & closure</a>
                    <a href="#sec-21" class="hover:text-amber-600 transition-colors">21. Holds, deductions & refunds</a>
                    <a href="#sec-22" class="hover:text-amber-600 transition-colors">22. Complaints & appeals</a>
                    <a href="#sec-23" class="hover:text-amber-600 transition-colors">23. Your responsibility & indemnity</a>
                    <a href="#sec-24" class="hover:text-amber-600 transition-colors">24. Liability & preserved rights</a>
                    <a href="#sec-25" class="hover:text-amber-600 transition-colors">25. Governing law & disputes</a>
                    <a href="#sec-26" class="hover:text-amber-600 transition-colors">26. Changes & discontinuation</a>
                    <a href="#sec-27" class="hover:text-amber-600 transition-colors">27. Notices & general provisions</a>
                </div>
            </div>

            <!-- Sections -->
            <section id="sec-1" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">1</span>
                    Who we are and what we provide
                </h2>
                <p>AfriCrew Limited (“AfriCrew”, “we”, “us”) operates a marketplace that connects event organisers and other hiring customers (“Clients”) with professionals and approved service businesses (“Crew”). Our services include profiles, discovery, referrals, enquiries, quotation tools, booking administration, communications, and payment coordination where available. “You” means the individual or entity accepting these Terms. “Content” includes profiles, photos, messages, reviews, files, and other material submitted via the platform.</p>
                <p class="text-slate-600 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <strong>Company registration number:</strong> PVT-VQ1EBZPZ. <strong>Registered address:</strong> GB16, 5th Floor, Phoenix Point House, Eastern Bypass – Membley, RWQV+QW Ruiru, Kenya. Website: <a href="https://africrew.com" class="text-amber-600 underline font-bold" target="_blank">africrew.com</a>. Support & legal email addresses: <a href="mailto:support@africrew.com" class="text-amber-600 underline">support@africrew.com</a> and <a href="mailto:legal@africrew.com" class="text-amber-600 underline">legal@africrew.com</a>. Features may vary by location, account role and subscription. A feature mentioned in these Terms is available only where enabled and described on the platform.
                </p>
            </section>

            <section id="sec-2" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">2</span>
                    Acceptance and related documents
                </h2>
                <p>You accept these Terms by actively selecting the acceptance control when registering or before using a feature that requires agreement. We will provide a retainable copy and record the version accepted. Receipt of an invitation alone does not create an account or authorise a charge. If you do not agree, do not register or use restricted account features; you may contact us about existing rights or obligations.</p>
                <p>Your signed booking agreement, schedules and accepted amendments govern the relevant gig and prevail over these Terms for that booking. Changes to AfriCrew’s booking obligations require our express acceptance. A separately accepted subscription or optional-feature order governs that purchase. Policies incorporated by these Terms must be identified and made available before acceptance; unpublished rules do not create hidden charges or penalties. Mandatory law always prevails. The Privacy Notice explains data processing; accepting these Terms is not blanket consent for unrelated processing.</p>
            </section>

            <section id="sec-3" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">3</span>
                    Eligibility and account roles
                </h2>
                <p>Account holders and professionals booked through the platform must be at least 18 and legally able to enter into a contract. If acting for a business, you must be authorised to bind it and identify the contracting entity. This platform does not currently provide a contracting route for child performers. Do not use another adult’s account to circumvent this rule.</p>
                <p>One verified identity may use both Client and Crew modes. Crew privileges require separate approval; switching modes does not create a new legal identity or erase obligations. Do not create duplicate or misleading accounts to evade restrictions. Approved business users must grant each authorised representative their own permitted access rather than share personal passwords.</p>
            </section>

            <section id="sec-4" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">4</span>
                    Account security
                </h2>
                <p>Provide accurate contact, identity and payment details, keep them up to date, and protect passwords and authentication codes. Report any suspected compromise promptly. You are responsible for actions you authorise and for any loss caused by your failure to take reasonable security precautions; you are not automatically liable for every unauthorised action. We may verify identity, restrict a compromised account, and require secure re-authentication before any sensitive changes or payouts.</p>
            </section>

            <section id="sec-5" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">5</span>
                    Referral and approval process
                </h2>
                <p>Crew onboarding is by invitation or referral. AfriCrew may recruit initial applicants directly, and approved Crew may refer applicants via authorised links. An applicant must complete the required profile and verification and attend an online interview with AfriCrew before activation. There is no unrestricted public Crew sign-up. Client registration does not confer Crew status.</p>
                <p>A referral is an introduction, not an approval, a job offer, or a guarantee of work. Invite only those who reasonably expect the invitation or have agreed to receive it; do not upload entire contact lists or send repeated unsolicited invitations. Do not sell referrals, offer unauthorised admission guarantees, or demand a recruitment payment from applicants. A referrer is not automatically liable for a referred member’s subsequent conduct.</p>
                <p>We assess identity, relevant experience, qualifications and suitability against the communicated criteria. We may request further evidence, decline an application or restrict a category on reasonable grounds. Where lawful and practicable, we will explain the reason and allow correction or review under clause 22. Approval may be reviewed if material information changes. Applicants must not submit forged credentials or manipulate interview or referee evidence.</p>
            </section>

            <section id="sec-6" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">6</span>
                    Digital CV and verification labels
                </h2>
                <p>You must have permission to publish portfolio work and accurately describe your role, experience, education, qualifications and availability. Do not present another person’s work as your own. Education information required for onboarding may be kept private or shared via available controls; private education records are not automatically authorised for public display. Contact referees only through an authorised verification process, and provide only information you may lawfully share.</p>
                <p>A verification label indicates the checks described alongside that label when performed. It does not guarantee conduct, competence for every assignment, continued licensing, insurance or future performance. User statements and referee confirmations may have different verification statuses. Clients should assess suitability for the particular task; Crew must maintain any licences legally required for their work.</p>
                <p>Your profile may be accessible via a public link and through search engines, depending on its visibility settings. Public content may be copied or cached by others. We will honour visibility changes and lawful removal requests, but cannot guarantee immediate removal from independent third-party caches. A username or profile address is a platform identifier, not a domain; we may change it for impersonation, trademark, safety or technical reasons, with reasonable notice where practicable.</p>
            </section>

            <section id="sec-7" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">7</span>
                    Subscriptions and paid features
                </h2>
                <p>Where a paid plan is offered, we will display its price, currency, taxes, billing period, included features, renewal basis, cancellation method and refund terms before purchase. No paid subscription begins solely because you receive a referral. Access to a plan does not guarantee enquiries, search position, bookings, minimum income or a particular return on fees.</p>
                <p>Recurring billing requires your express authorisation. Unless a specific offer lawfully states otherwise, cancel via account settings or support before renewal to prevent the next charge; access continues until the end of the paid period. No partial-period refund is due for voluntary cancellation alone, but statutory rights, duplicate charges, our breach and services not supplied remain refundable as applicable. Free trials must disclose their end date and conversion price before you opt in.</p>
                <p>We will give at least 30 days’ notice of any renewal price increase and allow cancellation before it takes effect. If shorter notice cannot be given, the increase will not apply at that renewal without your express agreement. Ending a subscription does not forfeit earned funds or cancel an existing gig. Any proposed loss of profile features must be disclosed with the plan.</p>
            </section>

            <section id="sec-8" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">8</span>
                    Enquiries, quotations and contracts
                </h2>
                <p>Search results, a shortlist, a message or an enquiry do not, by themselves, confirm a booking. Quotes must identify the scope, dates, location, hours, deliverables, charges and milestones. Clients must provide an accurate brief and assess exclusions. Both parties must accept the same completed booking agreement; the booking is confirmed when the required initial funding clears and the confirmation is recorded.</p>
                <p>Clients and Crew contract directly for services. AfriCrew joins a booking only for the limited rights and duties expressly accepted under its agreement. Providing the platform does not make us the organiser, employer or service performer. Employment status depends on the actual relationship and the law; statutory rights remain protected. Use an employment agreement where required.</p>
            </section>

            <section id="sec-9" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">9</span>
                    Prices, fees and taxes
                </h2>
                <p>Before commitment, we disclose the Crew price, Client fees, taxes, payment charges and Crew deductions. Undisclosed fees cannot be imposed later. New rates apply prospectively and do not affect an accepted booking without agreement. Each person is responsible for taxes legally allocated to them; any lawful withholding must be identified and supported by the required records. AfriCrew does not assume every user’s tax obligations.</p>
            </section>

            <section id="sec-10" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">10</span>
                    Funding and provider arrangements
                </h2>
                <p>Pay using the method specified for the booking. We will disclose the payment provider, funds holder, holding arrangement and applicable provider terms before funding. Any “wallet” display is a record of amounts and their status; it does not, by itself, create a bank deposit, credit facility or escrow. We do not promise regulated escrow protection unless the disclosed legal arrangement provides it. Customer funds must not be used to finance AfriCrew’s operating expenses.</p>
                <p>Use only payment methods you are authorised to use, and provide information reasonably required for lawful identity and fraud checks. Distinguish between pending, held and available funds. A screenshot does not confirm a cleared payment. Payout requires a verified destination; processing time, withdrawal limits and applicable charges must be disclosed. We will use reasonable care in transmitting instructions and investigating failed transfers, but do not guarantee unfunded payment or instant provider settlement.</p>
            </section>

            <section id="sec-11" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">11</span>
                    Payment authority and approval
                </h2>
                <p>You authorise AfriCrew to transmit only the payment instructions permitted by the accepted booking: expressly agreed advances or interim releases, Client-approved completion payments, disclosed lawful deductions, and authorised refunds. Client approval of a deliverable authorises the corresponding release without a second signature. It does not authorise unrelated account debits. An advance already paid to Crew is no longer held for the Client and may need to be recovered from Crew if refundable.</p>
                <p>The Client must review accessible completion evidence within the booking’s stated period, approve conforming work, and identify any specific material defect and disputed amount. Silence, a rating, or an admin completion flag is not Client approval and does not trigger automatic final release. The booking’s correction, partial approval, and dispute procedures apply. Leaving a review is never a condition of payment.</p>
            </section>

            <section id="sec-12" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">12</span>
                    Changes, cancellations and payment disputes
                </h2>
                <p>Changes to scope, dates, price or overtime require the parties’ recorded agreement. Cancellation, no-show, rescheduling, safety stoppages, unearned advances and refunds are governed by the signed booking and mandatory law. A deposit is not automatically forfeited. AfriCrew retains only disclosed, lawfully earned fees; refundable charges must be credited. A payment reversal does not, in itself, determine liability, and legitimate chargeback rights remain available. Duplicate recovery, fraudulent chargebacks and fabricated completion evidence are prohibited. Disputed funds are subject to clause 22 and the booking agreement.</p>
            </section>

            <section id="sec-13" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">13</span>
                    Professional conduct and prohibited use
                </h2>
                <p>Be respectful and honest, and comply with applicable law and lawful venue rules. Do not harass, threaten, exploit, sexually harass or unlawfully discriminate against anyone. Do not request unlawful or unsafe work, falsify attendance or qualification records, solicit bribes, launder money, impersonate another person or arrange prohibited services. Report any urgent danger to the appropriate emergency service; AfriCrew support is not an emergency response service.</p>
                <p>Do not introduce malware, defeat security controls, access another account, interfere with payment records, manipulate rankings or reviews, or overload the platform. Do not scrape private data, harvest contacts, resell account access, or use automated tools against restricted areas without permission. Legal rights and standard public-page indexing, consistent with our technical controls, are preserved. Report responsible security concerns to <a href="mailto:mail@africrew.com" class="text-amber-600 underline">mail@africrew.com</a>.</p>
            </section>

            <section id="sec-14" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">14</span>
                    Keeping a booking on the platform
                </h2>
                <p>For a booking agreed through AfriCrew, record material changes, payments and completion decisions via its authorised workflow. Do not divert an existing booking’s payment to evade its agreed fees or falsify its value. During an outage, contact support and preserve records. Never delay urgent safety communications.</p>
                <p>These Terms impose no general exclusivity, perpetual restriction on direct work, or undisclosed introduction fee. You remain free to work elsewhere and to maintain pre-existing relationships. Any future restriction on introductions would require a separate, clear and lawful agreement before it applies. For independently arranged transactions, platform payment holding and booking assistance apply only if expressly agreed; AfriCrew remains responsible for its own conduct and non-excludable duties.</p>
            </section>

            <section id="sec-15" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">15</span>
                    Your content and permissions
                </h2>
                <p>You retain ownership of Content you submit, subject to the rights of its true owners. You grant AfriCrew a non-exclusive, royalty-free licence to host, store, reproduce, format, display and transmit that Content only as reasonably necessary to operate, secure and provide the features you choose. We may permit service providers to perform those tasks under appropriate obligations. Public profiles may be displayed and indexed as selected; private messages and documents do not become public under this licence.</p>
                <p>The license ends when you delete the Content, except for limited retention required for legal obligations, disputes, security, reasonable backup cycles, or copies you have lawfully shared with another user. Retained private records must not be used as promotional material. This license does not transfer copyright ownership or grant us unrestricted rights to reuse it commercially.</p>
                <p>Upload only material you own or are authorised to share, including identifiable images and client or venue logos. Consider attendee privacy and obtain any required consent, particularly for children. The booking agreement, not the profile-content license, governs ownership and use of commissioned gig outputs. AfriCrew’s branding, software and designs remain ours or our licensors’; no ownership transfers to you.</p>
            </section>

            <section id="sec-16" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">16</span>
                    Reviews, complaints and promotion
                </h2>
                <p>Reviews must reflect a genuine interaction and distinguish facts from opinion. Do not post fabricated allegations, private financial or identity details, threats, paid-for ratings, or reviews conditioned on a refund. We may label, restrict, or remove reviews under these rules and, where practicable, provide the author with a reason and a review route. Criticism or paid membership alone does not justify removal. We must not change a review’s meaning.</p>
                <p>Report unlawful content to support, including its location, evidence and your contact details. We may temporarily restrict it while assessing the report and invite a response where safe. Featuring your identifiable portfolio, name or social handle in separate advertising or social posts requires separate permission; these Terms are not that permission. Agreed promotional content and tagging must follow your selected settings and applicable rights.</p>
            </section>

            <section id="sec-17" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">17</span>
                    Personal data and communications
                </h2>
                <p>Our Privacy Notice at <a href="{{ route('privacy') }}" class="text-amber-600 font-bold underline">Privacy Policy</a> explains the personal data collected, the purposes and legal bases, service providers, any international transfers, retention, safeguards, and how to exercise your rights. It also explains profile verification, referrals, message review, and location use. These Terms do not replace that notice or waive your rights. AfriCrew is responsible for its own processing; users who independently handle attendee, referee, or other user information remain responsible for their lawful use.</p>
                <p>We may send necessary account, security, contract and payment messages via the channels you provide and the features you enable. Marketing preferences are separate, and you may unsubscribe from marketing without losing necessary service messages. WhatsApp, contact access, optional location access and other optional integrations require the relevant permissions or lawful basis; account registration does not authorise contact with everyone in your address book.</p>
                <p>Limit access to private communications to authorised purposes, such as responding to a report, fraud prevention, support, or legal obligations, and ensure appropriate access controls are set out in the Privacy Notice. Do not share another person’s data for unrelated marketing or disclose private messages publicly without a lawful basis. Contact <a href="mailto:mail@africrew.com" class="text-amber-600 underline">mail@africrew.com</a> with any privacy rights or concerns; you may also complain to the Office of the Data Protection Commissioner.</p>
            </section>

            <section id="sec-18" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">18</span>
                    Search recommendations and optional AI features
                </h2>
                <p>Results may reflect relevance, role, location, stated availability, qualifications, profile completeness and service history. Availability and user-supplied information may change. We do not guarantee that a result is the cheapest, best or suitable for every task. Any paid placement must be clearly labelled. Payment for membership does not, by itself, guarantee a search position or verified status.</p>
                <p>Where AI-assisted suggestions, writing, design or matching tools are offered, their output may contain errors or omit context. Review facts, permissions, deliverables and recipients before adopting or publishing any output. Do not submit confidential or third-party personal information unless you may lawfully do so. We will explain relevant data use before enabling a feature; acceptance of these Terms does not authorise unrestricted training on private messages or identity documents.</p>
                <p>An automated suggestion does not accept a quotation, sign a contract, approve work, or release payment unless you separately enable and expressly authorise a specific action. Any automation remains subject to the accepted booking. Where applicable law gives you rights relating to a decision based solely on automated processing, those rights remain available, including the right to request human review where required.</p>
            </section>

            <section id="sec-19" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">19</span>
                    Availability and third-party services
                </h2>
                <p>We will use reasonable care to provide the platform and address faults. We cannot guarantee uninterrupted access, error-free software or the constant availability of every professional. Maintenance, provider outages and security incidents may interrupt features. We will give reasonable notice of planned material downtime where practicable and take reasonable steps to restore service and provide an alternative route for urgent booking administration.</p>
                <p>Third-party payment, mapping, social, communications or app-store services may have their own terms, which are disclosed where relevant. We are not responsible for an independent provider’s conduct merely because it is linked or integrated, but we remain responsible for our own promises, selection and administration to the extent required by law. An outage does not cancel earned payments or allow funds to be appropriated. Keep accessible copies of essential briefs and venue contacts.</p>
                <p>Events beyond reasonable control may delay obligations under the affected platform while we notify users and reasonably mitigate their effects. Lack of funds is not an excuse. We remain responsible for the lawful protection and accounting of funds, non-excludable duties, and refunds for services not supplied. The separate booking agreement determines how an event affects the Client and Crew’s performance.</p>
            </section>

            <section id="sec-20" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">20</span>
                    Restrictions, suspension and closure
                </h2>
                <p>We may proportionately restrict content, a category, new bookings or an account for a material breach, a credible safety or fraud concern, compromised security, an unmet lawful verification requirement, non-payment of an agreed fee, or a binding legal requirement. Measures must be proportionate to the risk. Where practicable, we will state the grounds, the evidence required, the corrective steps and the review route before taking action. Immediate action may be necessary to prevent harm or to comply with the law.</p>
                <p>A suspension is not a finding of guilt and does not automatically cancel signed bookings. We will consider a safe way to complete, transfer by consent, or cancel affected bookings under their terms. Except where unsafe or legally prohibited, users must retain a support route and access to relevant contract and payment records. We may withhold confidential security details or a reporter’s identity where justified.</p>
                <p>You may request account closure via settings or support. Closure ends future account use but does not erase debts, refund rights, disputes or obligations on existing bookings. We will reconcile balances and facilitate lawful payout or refund, subject to documented restrictions. Content and personal records are removed or retained in accordance with the Privacy Notice and the law. Closing an account does not require forfeiting lawfully owed funds.</p>
            </section>

            <section id="sec-21" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">21</span>
                    Holds, deductions and refunds
                </h2>
                <p>A hold must have a specific basis: a booking dispute, suspected fraud, a required verification check, a provider rule, or a legal order. We will explain the basis and the affected amount where lawful, review the hold at least every ten business days while it is within our control, and release restrictions when they are no longer justified. Unaffected funds should remain available where technically and legally possible. A hold is not a platform fee or a transfer of ownership.</p>
                <p>No blanket forfeiture, arbitrary penalty or unrelated set-off applies. We may correct a documented ledger error after notice and an opportunity to query it, but correcting an entry does not permit an unauthorised debit from a payment method. Alleged damages or indemnity claims cannot be deducted from disputed user funds. A refund follows the booking, the accepted purchase terms, lawful provider requirements and mandatory rights; the expected provider processing time must be disclosed.</p>
            </section>

            <section id="sec-22" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">22</span>
                    Complaints and appeals
                </h2>
                <p>Send the account or booking reference, issue, evidence and requested remedy to <a href="mailto:support@africrew.com" class="text-amber-600 underline">support@africrew.com</a>. We aim to acknowledge a complaint within two business days and to respond substantively within ten business days, or to explain the delay and the next update. You may request a human review of a refusal, restriction or suspension, preferably within 30 days; later requests remain eligible where reasonable, and this period does not shorten legal rights.</p>
                <p>For a gig dispute, the signed booking procedure applies. AfriCrew facilitates fair communication but cannot impose a binding decision on service liability merely because it administers the platform. Contested funds remain under the lawful holding arrangement pending joint instructions, a signed settlement, or a binding order. If lawful holding cannot continue, we will notify the parties and seek lawful directions, including court directions where needed. Undisputed payments and refunds must not be delayed by unrelated disputes.</p>
            </section>

            <section id="sec-23" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">23</span>
                    Your responsibility and indemnity
                </h2>
                <p>You are responsible for loss to the extent caused by your breach, negligence or unlawful conduct. You indemnify AfriCrew against third-party claims and reasonable legal costs to that extent, including claims arising from rights violations in Content you supply. This does not cover AfriCrew’s own fault, another party’s share of fault, or penalties that cannot lawfully be indemnified. We must notify you promptly, mitigate loss and permit reasonable participation in the defence. No settlement imposing obligations or admitting fault on your behalf may be made without your reasonable consent. Recovery requires an agreed settlement or established liability and is subject to clause 24.</p>
            </section>

            <section id="sec-24" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">24</span>
                    Liability and preserved rights
                </h2>
                <p>AfriCrew is responsible for its own platform services with reasonable skill and care. We do not guarantee a user’s honesty, performance, future availability or payment merely by hosting a profile or facilitating a booking. These limits do not excuse our own breach, misleading statements, negligence or statutory duties. The Client and Crew remain responsible under their booking and applicable law.</p>
                <p>For liability arising from a particular booking, the liability terms in that signed agreement apply. For other claims under these Terms, each party’s aggregate damages liability arising from the same event or connected events is limited to the greater of KES 10,000 and the platform or subscription fees you paid or owed us in the 12 months before the event. This is a reciprocal limit, subject to the exceptions below. Neither party is liable for remote or indirect loss or for lost anticipated profit, except where an express entitlement is preserved by a signed booking.</p>
                <p>Neither the cap nor the exclusion limits apply to earned payment debts, required refunds, proper accounting or return of customer funds, fraud, wilful misconduct, gross negligence, death or personal injury caused by negligence, confidentiality or data protection breaches, third-party intellectual-property claims, or any liability that cannot lawfully be limited. The same limits and exceptions apply to clause 23. Nothing waives mandatory consumer, employment, compensation, privacy, regulatory complaint or court rights. Each party must take reasonable steps to mitigate loss, and no one may recover twice for the same loss.</p>
            </section>

            <section id="sec-25" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">25</span>
                    Governing law and resolving legal disputes
                </h2>
                <p>Kenyan law governs these Terms. First, contact support where practicable; we will attempt a fair resolution. Parties may agree to mediation and its costs after a dispute arises. Mediation is not mandatory, and there is no compulsory arbitration or class-action waiver in these Terms. Unresolved matters may be brought before a competent Kenyan court or tribunal, including the Small Claims Court where it has jurisdiction. Nothing prevents urgent relief, statutory complaints, employment proceedings, or the protection of a limitation deadline.</p>
            </section>

            <section id="sec-26" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">26</span>
                    Changes and discontinuation
                </h2>
                <p>We will give at least 30 days’ notice of any material change to these Terms, stating the change, the effective date and the available choices. Changes required urgently by law, for security or to prevent harm may take effect sooner, with notice as soon as practicable and an explanation. Where fresh consent is required, we will obtain it before applying the change. Continued silence does not authorise a new recurring charge, new data-processing consent or altered booking payment instructions.</p>
                <p>You may stop using affected features or close your account before a change takes effect. A material reduction in a prepaid service allows you to cancel the unused portion and receive an appropriate refund, subject to mandatory rights. Existing bookings retain their accepted terms unless the affected parties agree otherwise or the law requires a change. If the platform closes, we will give reasonable notice, provide records, and arrange lawful reconciliation of funds and unused paid services.</p>
            </section>

            <section id="sec-27" class="space-y-3 pt-4 border-t border-slate-100">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">27</span>
                    Notices and general provisions
                </h2>
                <p>We send formal notices to your registered email and, where available, to your account. You may send notices to <a href="mailto:legal@africrew.com" class="text-amber-600 underline">legal@africrew.com</a>. Delivery must be reasonably documented; a known failure requires reasonable alternative contact steps. Keep contact information current. A business day is Monday to Friday, excluding Kenyan public holidays; stated times use Africa/Nairobi unless specified.</p>
                <p>No delay in enforcing a right waives it. An invalid provision is severed only to the extent necessary; lawful provisions continue. You may not transfer an account without approval. AfriCrew may transfer this agreement as part of a lawful business transfer only if your rights and paid entitlements are preserved, with notice and any consent required by law. Payment, refunds, privacy, content retention, liability and dispute provisions survive closure as needed. These Terms and the documents expressly accepted with them form the platform agreement, without excluding any protected prior statements or fraud claims.</p>
            </section>

        </div>

    </div>
</div>
@endsection
