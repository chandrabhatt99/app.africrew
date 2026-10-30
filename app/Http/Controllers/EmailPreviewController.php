<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationReceivedMail;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingRequestMail;
use App\Mail\BookingRequestSentMail;
use App\Mail\BookingRescheduledMail;
use App\Mail\ClientAccountApprovedMail;
use App\Mail\ClientBookingCancelledMail;
use App\Mail\ClientWelcomeMail;
use App\Mail\CrewApprovedMail;
use App\Mail\CrewRejectedMail;
use App\Mail\InterviewScheduledMail;
use App\Mail\CrewBookedMail;
use App\Mail\CrewConfirmedMail;
use App\Mail\CrewGigAssignedMail;
use App\Mail\CrewGigCompletedMail;
use App\Mail\DepositSecuredMail;
use App\Mail\EventCompleteMail;
use App\Mail\PaymentConfirmedMail;
use App\Mail\PaymentFailedMail;
use App\Mail\PayoutReleasedMail;
use App\Mail\ProfileActionRequiredMail;
use App\Mail\ProfileLiveMail;
use App\Mail\ProfileUpdatedMail;
use App\Mail\LoginNotificationMail;
use App\Mail\ReceiptMail;
use App\Mail\RefereeConsentRequestMail;
use App\Mail\ResetPasswordMail;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailPreviewController extends Controller
{
    /**
     * Directory listing of all transactional email templates
     */
    public function index()
    {
        $templates = [
            [
                'key' => 'crew-gig-completed',
                'title' => 'Crew Gig Completed (Timesheet / Report)',
                'subject' => 'Well done! Gig AFR-00042 completed',
                'recipient' => 'Crew Member',
                'trigger' => 'Event / staffing gig is marked as completed',
                'badge' => 'New',
            ],
            [
                'key' => 'client-account-approved',
                'title' => 'Client Account Approved',
                'subject' => 'Your AfriCrew account is approved - Start hiring',
                'recipient' => 'Client',
                'trigger' => 'Admin approves client registration account',
                'badge' => 'New',
            ],
            [
                'key' => 'crew-booked',
                'title' => "Crew Booked & Contract Required",
                'subject' => "You're booked! Lead Sound Engineer on Oct 24, 2026",
                'recipient' => 'Crew Member',
                'trigger' => 'Crew member is booked/assigned for client gig',
                'badge' => 'New',
            ],
            [
                'key' => 'client-booking-cancelled',
                'title' => 'Client Booking Cancelled',
                'subject' => 'Your booking AFR-00042 has been cancelled',
                'recipient' => 'Client',
                'trigger' => 'Client booking is cancelled with refund notice',
                'badge' => 'New',
            ],
            [
                'key' => 'crew-approved',
                'title' => 'Crew Approved',
                'subject' => "You're approved! Welcome to AfriCrew",
                'recipient' => 'Crew Member',
                'trigger' => 'Admin approves crew application to join directory',
                'badge' => 'New',
            ],
            [
                'key' => 'interview-scheduled',
                'title' => 'Crew Interview Scheduled',
                'subject' => 'Your AfriCrew Interview is Scheduled: Friday, Oct 24 at 10:00 AM',
                'recipient' => 'Crew Member',
                'trigger' => 'Admin schedules official vetting interview for registered crew applicant',
                'badge' => 'New',
            ],
            [
                'key' => 'crew-rejected',
                'title' => 'Crew Application Rejected',
                'subject' => 'Update Regarding Your AfriCrew Application',
                'recipient' => 'Crew Member',
                'trigger' => 'Admin rejects crew applicant profile following evaluation',
                'badge' => 'New',
            ],
            [
                'key' => 'booking-request-sent',
                'title' => 'Booking Request Sent',
                'subject' => 'Booking request sent - Gig AFR-00042',
                'recipient' => 'Client',
                'trigger' => 'Client submits a new event staffing booking request',
                'badge' => 'New',
            ],
            [
                'key' => 'payout-released',
                'title' => 'Payout Released',
                'subject' => 'Payout released: KES 15,000.00 for AFR-00042',
                'recipient' => 'Crew Member',
                'trigger' => 'Admin approves / processes crew payout or wallet withdrawal',
                'badge' => 'New',
            ],
            [
                'key' => 'profile-action-required',
                'title' => 'Action Required: Update Profile',
                'subject' => 'Action Required: Update your AfriCrew profile',
                'recipient' => 'Crew Member',
                'trigger' => 'Admin requires document or portfolio updates before approving profile',
                'badge' => 'New',
            ],
            [
                'key' => 'payment-confirmed',
                'title' => 'Payment Confirmed',
                'subject' => 'Payment confirmed - AFR-00042',
                'recipient' => 'Client',
                'trigger' => 'Client payment is successfully verified and crew locked in',
                'badge' => 'New',
            ],
            [
                'key' => 'booking-cancelled',
                'title' => 'Booking Cancelled',
                'subject' => 'Booking cancelled: AFR-00042 on Oct 18, 2026',
                'recipient' => 'Crew Member',
                'trigger' => 'Client or Admin cancels an event booking',
                'badge' => 'New',
            ],
            [
                'key' => 'deposit-secured',
                'title' => 'Deposit Secured',
                'subject' => 'Deposit secured for Gig AFR-00042',
                'recipient' => 'Crew Member',
                'trigger' => 'Client completes initial M-Pesa deposit / escrow payment',
                'badge' => 'New',
            ],
            [
                'key' => 'client-welcome',
                'title' => 'Client Welcome - Account Received',
                'subject' => 'Welcome to AfriCrew - Account received',
                'recipient' => 'Client',
                'trigger' => 'New client registers directly or submits first staffing request',
                'badge' => 'New',
            ],
            [
                'key' => 'booking-rescheduled',
                'title' => 'Booking Rescheduled',
                'subject' => 'Your booking has been rescheduled to Oct 25, 2026',
                'recipient' => 'Client & Crew',
                'trigger' => 'Event date is rescheduled in the system',
                'badge' => 'New',
            ],
            [
                'key' => 'reset-password',
                'title' => 'Password Reset',
                'subject' => 'Reset your AfriCrew password',
                'recipient' => 'Client / Crew',
                'trigger' => 'User requests password reset via portal login',
                'badge' => 'Updated',
            ],
            [
                'key' => 'crew-confirmed',
                'title' => 'Crew Confirmed',
                'subject' => 'Crew Confirmed: AFR-00042 on Oct 18, 2026',
                'recipient' => 'Client',
                'trigger' => 'Crew member is assigned & confirmed for client booking',
                'badge' => '',
            ],
            [
                'key' => 'event-complete',
                'title' => 'Event Completed Review',
                'subject' => 'How did your event on Oct 18, 2026 go?',
                'recipient' => 'Client',
                'trigger' => 'Staffing request status set to completed',
                'badge' => '',
            ],
            [
                'key' => 'crew-gig-assigned',
                'title' => 'Crew Gig Assignment',
                'subject' => 'New gig assignment: AFR-00042 on Oct 18, 2026',
                'recipient' => 'Crew Member',
                'trigger' => 'Staff assigned or matched to an event staffing gig',
                'badge' => '',
            ],
            [
                'key' => 'payment-failed',
                'title' => 'Payment Failed',
                'subject' => 'Payment failed for Gig AFR-00042',
                'recipient' => 'Client',
                'trigger' => 'M-Pesa payment is cancelled, declined, or fails',
                'badge' => '',
            ],
            [
                'key' => 'profile-updated',
                'title' => 'Profile Updated',
                'subject' => 'Your AfriCrew profile has been updated',
                'recipient' => 'Crew / Client',
                'trigger' => 'User updates profile information or preferences',
                'badge' => '',
            ],
            [
                'key' => 'receipt',
                'title' => 'Official Receipt',
                'subject' => 'Official Receipt: INV-20261016-0042',
                'recipient' => 'Client',
                'trigger' => 'M-Pesa payment successfully verified and recorded',
                'badge' => '',
            ],
            [
                'key' => 'booking-request',
                'title' => 'Booking Request',
                'subject' => 'New booking request for Lead Anchor / MC on Oct 18, 2026',
                'recipient' => 'Crew Member',
                'trigger' => 'Client inquires or selects crew member profile',
                'badge' => '',
            ],
            [
                'key' => 'profile-live',
                'title' => 'Profile Live',
                'subject' => 'Your AfriCrew profile is now live!',
                'recipient' => 'Crew Member',
                'trigger' => 'Admin approves crew applicant profile for directory',
                'badge' => '',
            ],
            [
                'key' => 'application-received',
                'title' => 'Application Received',
                'subject' => "We've received your application - AfriCrew",
                'recipient' => 'Crew Member',
                'trigger' => 'Talent submits crew join application',
                'badge' => '',
            ],
            [
                'key' => 'login-notification',
                'title' => 'Sign-In Security Notification',
                'subject' => 'AfriCrew - Security Alert: New Sign-in (Crew Member / Client)',
                'recipient' => 'Crew & Client',
                'trigger' => 'User signs in to Crew Portal, Client Portal, or Admin Dashboard',
                'badge' => 'New',
            ],
            [
                'key' => 'referee-consent',
                'title' => 'Referee Work Verification Request',
                'subject' => 'Work Reference Request: Leilani Kiprop on AfriCrew',
                'recipient' => 'Professional Referee',
                'trigger' => 'Crew member submits referee details or requests reference verification',
                'badge' => 'New',
            ],
        ];

        return view('emails.index', compact('templates'));
    }

    /**
     * Preview responsive HTML email templates directly in the browser
     */
    public function preview(string $type)
    {
        switch ($type) {
            case 'crew-gig-completed':
                return (new CrewGigCompletedMail(
                    firstName: 'Leilani',
                    gigId: 'AFR-00042',
                    eventDate: 'Oct 24, 2026',
                    reportLink: route('professional.dashboard')
                ))->render();

            case 'client-account-approved':
                return (new ClientAccountApprovedMail(
                    firstName: 'Michael',
                    searchLink: route('crew.index')
                ))->render();

            case 'crew-booked':
                return (new CrewBookedMail(
                    firstName: 'Leilani',
                    role: 'Lead Sound Engineer',
                    eventDate: 'Oct 24, 2026',
                    gigId: 'AFR-00042',
                    contractLink: route('professional.dashboard')
                ))->render();

            case 'client-booking-cancelled':
                return (new ClientBookingCancelledMail(
                    firstName: 'Michael',
                    gigId: 'AFR-00042',
                    eventDate: 'Oct 24, 2026',
                    refundDetails: 'your escrow deposit of KES 12,500 has been fully refunded to your M-Pesa account',
                    searchLink: route('crew.index')
                ))->render();

            case 'crew-approved':
                return (new CrewApprovedMail(
                    firstName: 'Leilani',
                    profileLink: route('professional.profile')
                ))->render();

            case 'interview-scheduled':
                return (new InterviewScheduledMail(
                    firstName: 'Leilani',
                    interviewDate: 'Friday, Oct 24, 2026',
                    interviewTime: '10:00 AM EAT',
                    interviewerName: 'Sarah Jenkins (Head of Talent Ops)',
                    interviewLink: 'https://meet.google.com/afr-crw-demo',
                    interviewNotes: 'Please join 5 minutes early, ensure your microphone and camera work properly, and have your National ID ready for verification.',
                    loginLink: route('staff.login')
                ))->render();

            case 'crew-rejected':
                return (new CrewRejectedMail(
                    firstName: 'Leilani',
                    reason: 'Identity verification could not be validated against official registry records and portfolio experience is below the required 1-year threshold.'
                ))->render();

            case 'booking-request-sent':
                return (new BookingRequestSentMail(
                    firstName: 'Michael',
                    gigId: 'AFR-00042',
                    role: 'Lead Sound Engineer',
                    eventDate: 'Oct 24, 2026',
                    location: 'Westlands, Nairobi',
                    bookingLink: route('client.dashboard', ['tab' => 'all-bookings'])
                ))->render();

            case 'payout-released':
                return (new PayoutReleasedMail(
                    firstName: 'Leilani',
                    amount: '15,000.00',
                    gigId: 'AFR-00042',
                    payoutLink: route('professional.wallet')
                ))->render();

            case 'profile-action-required':
                return (new ProfileActionRequiredMail(
                    firstName: 'Leilani',
                    rejectionReason: 'National ID copy was blurry. Please upload a clear photo of your government ID and complete your certifications.',
                    profileLink: route('professional.profile')
                ))->render();

            case 'payment-confirmed':
                return (new PaymentConfirmedMail(
                    firstName: 'Michael',
                    amount: '12,500.00',
                    gigId: 'AFR-00042',
                    eventDate: 'Oct 24, 2026',
                    bookingLink: route('client.dashboard', ['tab' => 'all-bookings'])
                ))->render();

            case 'reset-password':
                return (new ResetPasswordMail(
                    firstName: 'Jane',
                    resetLink: route('password.reset', ['token' => 'demo-token-123', 'email' => 'jane@example.com'])
                ))->render();

            case 'crew-confirmed':
                return (new CrewConfirmedMail(
                    firstName: 'Michael',
                    gigId: 'AFR-00042',
                    crewName: 'Leilani Morales (Lead Anchor / MC)',
                    eventDate: 'Oct 18, 2026',
                    contractLink: route('home')
                ))->render();

            case 'event-complete':
                return (new EventCompleteMail(
                    firstName: 'Michael',
                    eventDate: 'Oct 18, 2026',
                    reviewLink: route('client.dashboard', ['tab' => 'all-bookings'])
                ))->render();

            case 'crew-gig-assigned':
                return (new CrewGigAssignedMail(
                    firstName: 'Leilani',
                    gigId: 'AFR-00042',
                    eventName: 'Annual Tech Summit 2026',
                    clientName: 'Safaricom Events Ltd',
                    eventDate: 'Oct 18, 2026',
                    offeredRate: 'KES 5,000 / Day (Negotiable)',
                    shiftDuration: '3 Days (8h/day · 24 Hours Total)',
                    jobLink: route('professional.dashboard')
                ))->render();

            case 'payment-failed':
                return (new PaymentFailedMail(
                    firstName: 'Michael',
                    gigId: 'AFR-00042',
                    failureReason: 'Transaction cancelled by user or insufficient M-Pesa balance',
                    paymentLink: route('contracts.forRequest', 1)
                ))->render();

            case 'profile-updated':
                return (new ProfileUpdatedMail(
                    firstName: 'Leilani',
                    profileLink: route('professional.dashboard')
                ))->render();

            case 'receipt':
                return (new ReceiptMail(
                    firstName: 'Michael',
                    invoiceNo: 'INV-20261016-0042',
                    gigId: 'AFR-00042',
                    amount: '10,500.00'
                ))->render();

            case 'booking-request':
                return (new BookingRequestMail(
                    firstName: 'Leilani',
                    role: 'Lead Anchor / MC',
                    eventDate: 'Oct 18, 2026',
                    location: 'Nairobi, Kenya',
                    gigId: 'AFR-00042',
                    clientName: 'Safaricom Events Ltd',
                    gigLink: route('professional.dashboard')
                ))->render();

            case 'profile-live':
                return (new ProfileLiveMail(
                    firstName: 'Leilani',
                    profileLink: route('professional.dashboard')
                ))->render();

            case 'application-received':
                return (new ApplicationReceivedMail(
                    firstName: 'Leilani',
                    verificationUrl: route('staff.verify.email', ['token' => 'preview-token'])
                ))->render();

            case 'booking-cancelled':
                return (new BookingCancelledMail(
                    firstName: 'Leilani',
                    gigId: 'AFR-00042',
                    eventDate: 'Oct 18, 2026'
                ))->render();

            case 'deposit-secured':
                return (new DepositSecuredMail(
                    firstName: 'Leilani',
                    gigId: 'AFR-00042',
                    eventDate: 'Oct 18, 2026'
                ))->render();

            case 'client-welcome':
                return (new ClientWelcomeMail(
                    firstName: 'Michael'
                ))->render();

            case 'booking-rescheduled':
                return (new BookingRescheduledMail(
                    firstName: 'Michael',
                    gigId: 'AFR-00042',
                    oldDate: 'Oct 18, 2026',
                    newDate: 'Oct 25, 2026',
                    bookingLink: route('client.dashboard', ['tab' => 'all-bookings'])
                ))->render();

            case 'login-notification':
                return (new LoginNotificationMail(
                    recipientName: 'Valued Member',
                    email: 'crew.member@africrew.ke',
                    role: 'Crew Member',
                    loginTime: now()->format('M j, Y - g:i A T'),
                    ipAddress: '197.237.142.85 (Nairobi, Kenya)',
                    deviceInfo: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/129.0.0.0 Safari/537.36',
                    securityLink: route('password.request')
                ))->render();

            case 'referee-consent':
                return (new RefereeConsentRequestMail(
                    refereeName: 'David Omondi',
                    refereeOrg: 'Homeboyz Entertainment Ltd',
                    crewName: 'Leilani Kiprop',
                    crewCategory: 'Lead Sound Engineer',
                    reviewUrl: route('referee.review', ['token' => 'preview-token-demo']),
                    period: '2023–2024',
                    companyOrProject: 'Homeboyz Entertainment Ltd'
                ))->render();

            default:
                abort(404, 'Email template not found');
        }
    }

    /**
     * Send test email to a specified recipient
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'type' => ['required', 'in:crew-gig-completed,client-account-approved,crew-booked,client-booking-cancelled,crew-approved,interview-scheduled,crew-rejected,booking-request-sent,payout-released,profile-action-required,payment-confirmed,reset-password,crew-confirmed,event-complete,crew-gig-assigned,payment-failed,profile-updated,receipt,booking-request,profile-live,application-received,booking-cancelled,deposit-secured,client-welcome,booking-rescheduled,login-notification,referee-consent'],
        ]);

        $recipient = $request->input('email');
        $type = $request->input('type');

        switch ($type) {
            case 'crew-gig-completed':
                Mail::to($recipient)->send(new CrewGigCompletedMail(
                    firstName: 'Valued Crew',
                    gigId: 'AFR-00042',
                    eventDate: date('M j, Y'),
                    reportLink: route('professional.dashboard')
                ));
                break;

            case 'client-account-approved':
                Mail::to($recipient)->send(new ClientAccountApprovedMail(
                    firstName: 'Valued Client',
                    searchLink: route('crew.index')
                ));
                break;

            case 'crew-booked':
                Mail::to($recipient)->send(new CrewBookedMail(
                    firstName: 'Valued Crew',
                    role: 'Lead Sound Engineer',
                    eventDate: date('M j, Y'),
                    gigId: 'AFR-00042',
                    contractLink: route('professional.dashboard')
                ));
                break;

            case 'client-booking-cancelled':
                Mail::to($recipient)->send(new ClientBookingCancelledMail(
                    firstName: 'Valued Client',
                    gigId: 'AFR-00042',
                    eventDate: date('M j, Y'),
                    refundDetails: 'your refund has been initiated to your M-Pesa account',
                    searchLink: route('crew.index')
                ));
                break;

            case 'crew-approved':
                Mail::to($recipient)->send(new CrewApprovedMail(
                    firstName: 'Valued Crew',
                    profileLink: route('professional.profile')
                ));
                break;

            case 'interview-scheduled':
                Mail::to($recipient)->send(new InterviewScheduledMail(
                    firstName: 'Valued Crew',
                    interviewDate: date('l, M j, Y', strtotime('+3 days')),
                    interviewTime: '10:00 AM EAT',
                    interviewerName: 'Sarah Jenkins (AfriCrew Ops)',
                    interviewLink: 'https://meet.google.com/afr-crw-demo',
                    interviewNotes: 'Please join promptly and have your National ID ready.',
                    loginLink: route('staff.login')
                ));
                break;

            case 'crew-rejected':
                Mail::to($recipient)->send(new CrewRejectedMail(
                    firstName: 'Valued Crew',
                    reason: 'Identity verification could not be validated against official registry records.'
                ));
                break;

            case 'booking-request-sent':
                Mail::to($recipient)->send(new BookingRequestSentMail(
                    firstName: 'Valued Client',
                    gigId: 'AFR-00042',
                    role: 'Lead Sound Engineer',
                    eventDate: date('M j, Y'),
                    location: 'Nairobi, Kenya',
                    bookingLink: route('client.dashboard', ['tab' => 'all-bookings'])
                ));
                break;

            case 'payout-released':
                Mail::to($recipient)->send(new PayoutReleasedMail(
                    firstName: 'Valued Crew',
                    amount: '15,000.00',
                    gigId: 'AFR-00042',
                    payoutLink: route('professional.wallet')
                ));
                break;

            case 'profile-action-required':
                Mail::to($recipient)->send(new ProfileActionRequiredMail(
                    firstName: 'Valued Crew',
                    rejectionReason: 'National ID copy was blurry. Please upload a clear photo of your government ID.',
                    profileLink: route('professional.profile')
                ));
                break;

            case 'payment-confirmed':
                Mail::to($recipient)->send(new PaymentConfirmedMail(
                    firstName: 'Valued Client',
                    amount: '12,500.00',
                    gigId: 'AFR-00042',
                    eventDate: date('M j, Y'),
                    bookingLink: route('client.dashboard', ['tab' => 'all-bookings'])
                ));
                break;

            case 'reset-password':
                Mail::to($recipient)->send(new ResetPasswordMail(
                    firstName: 'Valued User',
                    resetLink: route('password.reset', ['token' => 'test-token', 'email' => $recipient])
                ));
                break;

            case 'crew-confirmed':
                Mail::to($recipient)->send(new CrewConfirmedMail(
                    firstName: 'Valued Client',
                    gigId: 'AFR-TEST01',
                    crewName: 'Leilani Morales',
                    eventDate: date('M j, Y'),
                    contractLink: route('home')
                ));
                break;

            case 'event-complete':
                Mail::to($recipient)->send(new EventCompleteMail(
                    firstName: 'Valued Client',
                    eventDate: date('M j, Y'),
                    reviewLink: route('home')
                ));
                break;

            case 'crew-gig-assigned':
                Mail::to($recipient)->send(new CrewGigAssignedMail(
                    firstName: 'Valued Crew',
                    gigId: 'AFR-TEST01',
                    eventName: 'Nairobi Gala Night',
                    clientName: 'AfriCrew Client',
                    eventDate: date('M j, Y'),
                    offeredRate: 'KES 4,500',
                    shiftDuration: '8 Hours Shift',
                    jobLink: route('professional.dashboard')
                ));
                break;

            case 'payment-failed':
                Mail::to($recipient)->send(new PaymentFailedMail(
                    firstName: 'Valued Client',
                    gigId: 'AFR-TEST01',
                    failureReason: 'Transaction cancelled by user or insufficient M-Pesa balance',
                    paymentLink: route('contracts.forRequest', 1)
                ));
                break;

            case 'profile-updated':
                Mail::to($recipient)->send(new ProfileUpdatedMail(
                    firstName: 'Valued User',
                    profileLink: route('professional.dashboard')
                ));
                break;

            case 'receipt':
                Mail::to($recipient)->send(new ReceiptMail(
                    firstName: 'Valued Client',
                    invoiceNo: 'INV-' . date('Ymd') . '-0042',
                    gigId: 'AFR-00042',
                    amount: '10,500.00'
                ));
                break;

            case 'booking-request':
                Mail::to($recipient)->send(new BookingRequestMail(
                    firstName: 'Valued Crew',
                    role: 'Lead Anchor / MC',
                    eventDate: date('M j, Y'),
                    location: 'Nairobi, Kenya',
                    gigId: 'AFR-00042',
                    clientName: 'AfriCrew Client',
                    gigLink: route('professional.dashboard')
                ));
                break;

            case 'profile-live':
                Mail::to($recipient)->send(new ProfileLiveMail(
                    firstName: 'Valued Crew',
                    profileLink: route('professional.dashboard')
                ));
                break;

            case 'application-received':
                Mail::to($recipient)->send(new ApplicationReceivedMail(
                    firstName: 'Valued Crew',
                    verificationUrl: route('staff.verify.email', ['token' => 'preview-token'])
                ));
                break;

            case 'booking-cancelled':
                Mail::to($recipient)->send(new BookingCancelledMail(
                    firstName: 'Valued Crew',
                    gigId: 'AFR-TEST01',
                    eventDate: date('M j, Y')
                ));
                break;

            case 'deposit-secured':
                Mail::to($recipient)->send(new DepositSecuredMail(
                    firstName: 'Valued Crew',
                    gigId: 'AFR-TEST01',
                    eventDate: date('M j, Y')
                ));
                break;

            case 'client-welcome':
                Mail::to($recipient)->send(new ClientWelcomeMail(
                    firstName: 'Valued Client'
                ));
                break;

            case 'booking-rescheduled':
                Mail::to($recipient)->send(new BookingRescheduledMail(
                    firstName: 'Valued Client',
                    gigId: 'AFR-TEST01',
                    oldDate: date('M j, Y'),
                    newDate: date('M j, Y', strtotime('+7 days')),
                    bookingLink: route('client.dashboard', ['tab' => 'all-bookings'])
                ));
                break;

            case 'login-notification':
                Mail::to($recipient)->send(new LoginNotificationMail(
                    recipientName: 'Valued Member',
                    email: $recipient,
                    role: 'Crew & Client Member',
                    loginTime: now()->format('M j, Y - g:i A T'),
                    ipAddress: $request->ip() ?: '127.0.0.1',
                    deviceInfo: $request->userAgent() ?: 'Chrome Browser on Windows',
                    securityLink: route('password.request')
                ));
                break;

            case 'referee-consent':
                Mail::to($recipient)->send(new RefereeConsentRequestMail(
                    refereeName: 'David Omondi',
                    refereeOrg: 'Homeboyz Entertainment Ltd',
                    crewName: 'Leilani Kiprop',
                    crewCategory: 'Lead Sound Engineer',
                    reviewUrl: route('referee.review', ['token' => 'preview-token-demo']),
                    period: '2023–2024',
                    companyOrProject: 'Homeboyz Entertainment Ltd'
                ));
                break;
        }

        return back()->with('success', "Test email [{$type}] successfully dispatched to {$recipient}!");
    }
}
