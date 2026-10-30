<?php

namespace App\Services;

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
use App\Mail\InterviewScheduledMail;
use App\Mail\ReceiptMail;
use App\Mail\ResetPasswordMail;
use App\Models\Professional;
use App\Models\StaffingRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    /**
     * Send Password Reset Email
     */
    public static function sendPasswordReset(string $email, string $firstName, string $resetLink): bool
    {
        try {
            Mail::to($email)->send(new ResetPasswordMail($firstName, $resetLink));
            Log::info("Password reset email sent to {$email}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send password reset email to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Crew Confirmed Email to Client
     */
    public static function sendCrewConfirmedToClient(StaffingRequest $staffingRequest, ?string $crewNames = null): bool
    {
        $clientEmail = $staffingRequest->email ?: $staffingRequest->user?->email;
        if (!$clientEmail) {
            return false;
        }

        $firstName = explode(' ', trim($staffingRequest->full_name ?: ($staffingRequest->user?->name ?: 'Client')))[0];
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $eventDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : 'Upcoming Date';
        $contractLink = route('contracts.forRequest', $staffingRequest->id);

        if (!$crewNames) {
            $assignedCrew = $staffingRequest->assignments()->with('professional')->get();
            $crewNames = $assignedCrew->map(fn($a) => $a->professional?->full_name)->filter()->join(', ');
            if (empty($crewNames)) {
                $crewNames = $staffingRequest->category . ' Talent Member';
            }
        }

        try {
            Mail::to($clientEmail)->send(new CrewConfirmedMail(
                $firstName,
                $gigId,
                $crewNames,
                $eventDate,
                $contractLink
            ));
            Log::info("Crew confirmed email sent to client {$clientEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send crew confirmed email to {$clientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Event Complete Email to Client
     */
    public static function sendEventCompleteToClient(StaffingRequest $staffingRequest): bool
    {
        $clientEmail = $staffingRequest->email ?: $staffingRequest->user?->email;
        if (!$clientEmail) {
            return false;
        }

        $firstName = explode(' ', trim($staffingRequest->full_name ?: ($staffingRequest->user?->name ?: 'Client')))[0];
        $eventDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : date('M j, Y');
        $reviewLink = route('client.dashboard', ['tab' => 'all-bookings']);

        try {
            Mail::to($clientEmail)->send(new EventCompleteMail(
                $firstName,
                $eventDate,
                $reviewLink
            ));
            Log::info("Event complete email sent to client {$clientEmail} for Request #{$staffingRequest->id}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send event complete email to {$clientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send New Gig Assignment Invitation to Crew Member
     */
    public static function sendGigAssignedToCrew(Professional $professional, StaffingRequest $staffingRequest, $offeredRate = null): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $eventName = $staffingRequest->event_name ?: 'Event Staffing Shift';
        $clientName = $staffingRequest->full_name ?: ($staffingRequest->company_name ?: 'AfriCrew Client');
        $eventDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : 'Scheduled Event';
        $shiftDuration = $staffingRequest->shift_duration ?: '8 Hours Standard Shift';
        
        $curr = $staffingRequest->currency ?: 'KES';
        $rateFormatted = $curr . ' ' . number_format($offeredRate ?: ($staffingRequest->budget ?: 3500));
        if ($staffingRequest->rate_type === 'negotiable') {
            $rateFormatted .= ' (Negotiable)';
        }

        $jobLink = route('professional.dashboard');

        try {
            Mail::to($crewEmail)->send(new CrewGigAssignedMail(
                $firstName,
                $gigId,
                $eventName,
                $clientName,
                $eventDate,
                $rateFormatted,
                $shiftDuration,
                $jobLink
            ));
            Log::info("Gig assignment invitation sent to crew {$crewEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send gig assignment email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Payment Failed Email to Client
     */
    public static function sendPaymentFailedToClient(StaffingRequest $staffingRequest, ?string $failureReason = null, ?string $paymentLink = null): bool
    {
        $clientEmail = $staffingRequest->email ?: $staffingRequest->user?->email;
        if (!$clientEmail) {
            return false;
        }

        $firstName = explode(' ', trim($staffingRequest->full_name ?: ($staffingRequest->user?->name ?: 'Client')))[0];
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $reason = $failureReason ?: 'Transaction was declined or cancelled';
        $link = $paymentLink ?: route('contracts.forRequest', $staffingRequest->id);

        try {
            Mail::to($clientEmail)->send(new PaymentFailedMail(
                firstName: $firstName,
                gigId: $gigId,
                failureReason: $reason,
                paymentLink: $link
            ));
            Log::info("Payment failed email sent to client {$clientEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send payment failed email to {$clientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Profile Updated Confirmation Email
     */
    public static function sendProfileUpdated($userOrProfessional, ?string $profileLink = null): bool
    {
        $email = $userOrProfessional->email ?? null;
        if (!$email) {
            return false;
        }

        $firstName = $userOrProfessional->first_name 
            ?? (explode(' ', trim($userOrProfessional->full_name ?? ($userOrProfessional->name ?? 'User')))[0]);

        if (!$profileLink) {
            if ($userOrProfessional instanceof Professional) {
                $profileLink = route('professional.dashboard', ['tab' => 'personal']);
            } else {
                $profileLink = route('client.dashboard');
            }
        }

        try {
            Mail::to($email)->send(new ProfileUpdatedMail(
                firstName: $firstName,
                profileLink: $profileLink
            ));
            Log::info("Profile updated email sent to {$email}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send profile updated email to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Official Payment Receipt to Client
     */
    public static function sendReceiptToClient(StaffingRequest $staffingRequest, $amount = null, ?string $invoiceNo = null): bool
    {
        $clientEmail = $staffingRequest->email ?: $staffingRequest->user?->email;
        if (!$clientEmail) {
            return false;
        }

        $firstName = explode(' ', trim($staffingRequest->full_name ?: ($staffingRequest->user?->name ?: 'Client')))[0];
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $inv = $invoiceNo ?: ('INV-' . date('Ymd') . '-' . str_pad($staffingRequest->id, 4, '0', STR_PAD_LEFT));
        $formattedAmount = number_format((float) ($amount ?: ($staffingRequest->budget ?: 0)), 2);

        try {
            Mail::to($clientEmail)->send(new ReceiptMail(
                firstName: $firstName,
                invoiceNo: $inv,
                gigId: $gigId,
                amount: $formattedAmount
            ));
            Log::info("Official receipt email sent to client {$clientEmail} for Invoice {$inv}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send receipt email to {$clientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send New Booking Request Notification to Crew Member
     */
    public static function sendBookingRequestToCrew(Professional $professional, StaffingRequest $staffingRequest, ?string $gigLink = null): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];
        $role = $staffingRequest->category ?: 'Event Professional';
        $eventDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : 'Upcoming Event';
        $location = $staffingRequest->location ?: 'Nairobi, Kenya';
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $clientName = $staffingRequest->full_name ?: ($staffingRequest->company_name ?: 'AfriCrew Client');
        $link = $gigLink ?: route('professional.dashboard');

        try {
            Mail::to($crewEmail)->send(new BookingRequestMail(
                firstName: $firstName,
                role: $role,
                eventDate: $eventDate,
                location: $location,
                gigId: $gigId,
                clientName: $clientName,
                gigLink: $link
            ));
            Log::info("Booking request email sent to crew {$crewEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send booking request email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Profile Live Approval Email to Crew Member
     */
    public static function sendProfileLiveToCrew(Professional $professional, ?string $profileLink = null): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];
        $link = $profileLink ?: route('crew.show', $professional);

        try {
            Mail::to($crewEmail)->send(new ProfileLiveMail(
                firstName: $firstName,
                profileLink: $link
            ));
            Log::info("Profile live email sent to crew {$crewEmail}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send profile live email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Application Received Email to Crew Member upon Sign Up
     */
    public static function sendApplicationReceivedToCrew(Professional $professional): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];

        try {
            Mail::to($crewEmail)->send(new ApplicationReceivedMail(
                firstName: $firstName,
                verificationUrl: route('staff.verify.email', ['token' => $professional->verification_token])
            ));
            Log::info("Application received email sent to crew {$crewEmail}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send application received email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Booking Cancelled Email to Crew Member
     */
    public static function sendBookingCancelledToCrew(Professional $professional, StaffingRequest $staffingRequest): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $eventDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : 'Scheduled Event';

        try {
            Mail::to($crewEmail)->send(new BookingCancelledMail(
                firstName: $firstName,
                gigId: $gigId,
                eventDate: $eventDate
            ));
            Log::info("Booking cancelled email sent to crew {$crewEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send booking cancelled email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Booking Cancelled Email to all assigned crew of a staffing request
     */
    public static function sendBookingCancelledToAllCrew(StaffingRequest $staffingRequest): int
    {
        $assignments = $staffingRequest->assignments()->with('professional')->get();
        $sentCount = 0;

        foreach ($assignments as $assignment) {
            if ($assignment->professional) {
                if (self::sendBookingCancelledToCrew($assignment->professional, $staffingRequest)) {
                    $sentCount++;
                }
            }
        }

        return $sentCount;
    }

    /**
     * Send Deposit Secured Email to Crew Member
     */
    public static function sendDepositSecuredToCrew(Professional $professional, StaffingRequest $staffingRequest): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $eventDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : 'Scheduled Event';

        try {
            Mail::to($crewEmail)->send(new DepositSecuredMail(
                firstName: $firstName,
                gigId: $gigId,
                eventDate: $eventDate
            ));
            Log::info("Deposit secured email sent to crew {$crewEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send deposit secured email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Deposit Secured Email to all assigned crew
     */
    public static function sendDepositSecuredToAllCrew(StaffingRequest $staffingRequest): int
    {
        $assignments = $staffingRequest->assignments()->with('professional')->get();
        $sentCount = 0;

        foreach ($assignments as $assignment) {
            if ($assignment->professional) {
                if (self::sendDepositSecuredToCrew($assignment->professional, $staffingRequest)) {
                    $sentCount++;
                }
            }
        }

        return $sentCount;
    }

    /**
     * Send Welcome to AfriCrew - Account received Email to newly registered Client
     */
    public static function sendClientWelcome($userOrEmail, ?string $fullName = null): bool
    {
        if ($userOrEmail instanceof User) {
            $email = $userOrEmail->email;
            $firstName = explode(' ', trim($userOrEmail->name ?: 'Client'))[0];
        } else {
            $email = (string) $userOrEmail;
            $firstName = $fullName ? explode(' ', trim($fullName))[0] : 'Client';
        }

        if (!$email) {
            return false;
        }

        try {
            Mail::to($email)->send(new ClientWelcomeMail(
                firstName: $firstName
            ));
            Log::info("Client welcome email sent to {$email}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send client welcome email to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Booking Rescheduled Email to Client or Crew
     */
    public static function sendBookingRescheduled($recipient, StaffingRequest $staffingRequest, ?string $oldDate = null, ?string $newDate = null, ?string $bookingLink = null): bool
    {
        if ($recipient instanceof User || $recipient instanceof Professional) {
            $email = $recipient->email;
            $firstName = $recipient->first_name ?? (explode(' ', trim($recipient->name ?? ($recipient->full_name ?? 'Client')))[0]);
        } else {
            $email = (string) $recipient;
            $firstName = explode(' ', trim($staffingRequest->full_name ?: ($staffingRequest->user?->name ?: 'Client')))[0];
        }

        if (!$email) {
            return false;
        }

        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $newDateFormatted = $newDate ?: ($staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : date('M j, Y'));
        $oldDateFormatted = $oldDate ?: 'Previous Date';
        $link = $bookingLink ?: route('client.dashboard', ['tab' => 'all-bookings']);

        try {
            Mail::to($email)->send(new BookingRescheduledMail(
                firstName: $firstName,
                gigId: $gigId,
                oldDate: $oldDateFormatted,
                newDate: $newDateFormatted,
                bookingLink: $link
            ));
            Log::info("Booking rescheduled email sent to {$email} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send booking rescheduled email to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Interview Scheduled Email to Crew Member
     */
    public static function sendInterviewScheduledToCrew(Professional $professional, ?string $loginLink = null): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];
        $interviewDate = $professional->interview_date ? $professional->interview_date->format('l, F j, Y') : date('l, F j, Y');
        $interviewTime = $professional->interview_time ?: '10:00 AM';
        $interviewerName = $professional->interviewer_name ?: 'AfriCrew Vetting Team';
        $interviewLink = $professional->interview_link;
        $interviewNotes = $professional->interview_notes;
        $link = $loginLink ?: route('staff.login');

        try {
            Mail::to($crewEmail)->send(new InterviewScheduledMail(
                firstName: $firstName,
                interviewDate: $interviewDate,
                interviewTime: $interviewTime,
                interviewerName: $interviewerName,
                interviewLink: $interviewLink,
                interviewNotes: $interviewNotes,
                loginLink: $link
            ));
            Log::info("Interview scheduled email sent to crew {$crewEmail} for {$interviewDate} at {$interviewTime}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send interview scheduled email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Crew Approved Email
     */
    public static function sendCrewApproved(Professional $professional, ?string $profileLink = null): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];
        $link = $profileLink ?: route('professional.profile');

        try {
            Mail::to($crewEmail)->send(new CrewApprovedMail(
                firstName: $firstName,
                profileLink: $link
            ));
            Log::info("Crew approved email sent to {$crewEmail}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send crew approved email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Crew Rejected Email
     */
    public static function sendCrewRejected(Professional $professional, ?string $reason = null): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];

        try {
            Mail::to($crewEmail)->send(new CrewRejectedMail(
                firstName: $firstName,
                reason: $reason
            ));
            Log::info("Crew rejected email sent to {$crewEmail}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send crew rejected email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Booking Request Sent Email to Client
     */
    public static function sendBookingRequestSentToClient(StaffingRequest $staffingRequest, ?string $bookingLink = null): bool
    {
        $clientEmail = $staffingRequest->email ?: $staffingRequest->user?->email;
        if (!$clientEmail) {
            return false;
        }

        $firstName = explode(' ', trim($staffingRequest->full_name ?: ($staffingRequest->user?->name ?: 'Client')))[0];
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $role = $staffingRequest->category ?: 'Event Crew';
        $eventDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : date('M j, Y');
        $location = $staffingRequest->location ?: 'Nairobi, Kenya';
        $link = $bookingLink ?: route('client.dashboard', ['tab' => 'all-bookings']);

        try {
            Mail::to($clientEmail)->send(new BookingRequestSentMail(
                firstName: $firstName,
                gigId: $gigId,
                role: $role,
                eventDate: $eventDate,
                location: $location,
                bookingLink: $link
            ));
            Log::info("Booking request sent confirmation email sent to client {$clientEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send booking request sent email to {$clientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Payout Released Email to Crew Member
     */
    public static function sendPayoutReleasedToCrew(Professional $professional, $amount, string $gigId, ?string $payoutLink = null): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];
        $formattedAmount = is_numeric($amount) ? number_format((float) $amount, 2) : (string) $amount;
        $link = $payoutLink ?: route('professional.wallet');

        try {
            Mail::to($crewEmail)->send(new PayoutReleasedMail(
                firstName: $firstName,
                amount: $formattedAmount,
                gigId: $gigId,
                payoutLink: $link
            ));
            Log::info("Payout released email sent to crew {$crewEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send payout released email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Action Required / Profile Update Request Email to Crew Member
     */
    public static function sendProfileActionRequired(Professional $professional, string $rejectionReason, ?string $profileLink = null): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];
        $link = $profileLink ?: route('professional.profile');

        try {
            Mail::to($crewEmail)->send(new ProfileActionRequiredMail(
                firstName: $firstName,
                rejectionReason: $rejectionReason,
                profileLink: $link
            ));
            Log::info("Profile action required email sent to crew {$crewEmail}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send profile action required email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Payment Confirmed Email to Client
     */
    public static function sendPaymentConfirmedToClient(StaffingRequest $staffingRequest, $amount = null, ?string $bookingLink = null): bool
    {
        $clientEmail = $staffingRequest->email ?: $staffingRequest->user?->email;
        if (!$clientEmail) {
            return false;
        }

        $firstName = explode(' ', trim($staffingRequest->full_name ?: ($staffingRequest->user?->name ?: 'Client')))[0];
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $eventDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : date('M j, Y');
        $formattedAmount = is_numeric($amount) ? number_format((float) $amount, 2) : number_format((float) ($staffingRequest->budget ?: 0), 2);
        $link = $bookingLink ?: route('client.dashboard', ['tab' => 'all-bookings']);

        try {
            Mail::to($clientEmail)->send(new PaymentConfirmedMail(
                firstName: $firstName,
                amount: $formattedAmount,
                gigId: $gigId,
                eventDate: $eventDate,
                bookingLink: $link
            ));
            Log::info("Payment confirmed email sent to client {$clientEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send payment confirmed email to {$clientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Gig Completed (Submit Report / Timesheet) Email to Crew Member
     */
    public static function sendCrewGigCompleted(Professional $professional, StaffingRequest $staffingRequest, ?string $reportLink = null): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $eventDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : date('M j, Y');
        $link = $reportLink ?: route('professional.dashboard');

        try {
            Mail::to($crewEmail)->send(new CrewGigCompletedMail(
                firstName: $firstName,
                gigId: $gigId,
                eventDate: $eventDate,
                reportLink: $link
            ));
            Log::info("Crew gig completed email sent to {$crewEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send crew gig completed email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Gig Completed Email to all assigned crew of a staffing request
     */
    public static function sendCrewGigCompletedToAllCrew(StaffingRequest $staffingRequest): int
    {
        $assignments = $staffingRequest->assignments()->with('professional')->get();
        $sentCount = 0;

        foreach ($assignments as $assignment) {
            if ($assignment->professional) {
                if (self::sendCrewGigCompleted($assignment->professional, $staffingRequest)) {
                    $sentCount++;
                }
            }
        }

        return $sentCount;
    }

    /**
     * Send Client Account Approved Email
     */
    public static function sendClientAccountApproved(User $user, ?string $searchLink = null): bool
    {
        $clientEmail = $user->email;
        if (!$clientEmail) {
            return false;
        }

        $firstName = explode(' ', trim($user->name ?: 'Client'))[0];
        $link = $searchLink ?: route('crew.index');

        try {
            Mail::to($clientEmail)->send(new ClientAccountApprovedMail(
                firstName: $firstName,
                searchLink: $link
            ));
            Log::info("Client account approved email sent to {$clientEmail}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send client account approved email to {$clientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send You're Booked (Sign Contract) Email to Crew Member
     */
    public static function sendCrewBooked(Professional $professional, StaffingRequest $staffingRequest, ?string $contractLink = null): bool
    {
        $crewEmail = $professional->email;
        if (!$crewEmail) {
            return false;
        }

        $firstName = $professional->first_name ?: explode(' ', trim($professional->full_name))[0];
        $role = $staffingRequest->category ?: 'Event Professional';
        $eventDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : date('M j, Y');
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $link = $contractLink ?: route('contracts.forRequest', $staffingRequest->id);

        try {
            Mail::to($crewEmail)->send(new CrewBookedMail(
                firstName: $firstName,
                role: $role,
                eventDate: $eventDate,
                gigId: $gigId,
                contractLink: $link
            ));
            Log::info("Crew booked email sent to {$crewEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send crew booked email to {$crewEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send You're Booked Email to all assigned crew
     */
    public static function sendCrewBookedToAllCrew(StaffingRequest $staffingRequest): int
    {
        $assignments = $staffingRequest->assignments()->with('professional')->get();
        $sentCount = 0;

        foreach ($assignments as $assignment) {
            if ($assignment->professional) {
                if (self::sendCrewBooked($assignment->professional, $staffingRequest)) {
                    $sentCount++;
                }
            }
        }

        return $sentCount;
    }

    /**
     * Send Booking Cancelled Email to Client
     */
    public static function sendClientBookingCancelled(StaffingRequest $staffingRequest, ?string $refundDetails = null, ?string $searchLink = null): bool
    {
        $clientEmail = $staffingRequest->email ?: $staffingRequest->user?->email;
        if (!$clientEmail) {
            return false;
        }

        $firstName = explode(' ', trim($staffingRequest->full_name ?: ($staffingRequest->user?->name ?: 'Client')))[0];
        $gigId = 'AFR-' . str_pad($staffingRequest->id, 5, '0', STR_PAD_LEFT);
        $eventDate = $staffingRequest->event_date ? $staffingRequest->event_date->format('M j, Y') : date('M j, Y');
        $refund = $refundDetails ?: 'any eligible refund has been processed back to your payment account';
        $link = $searchLink ?: route('crew.index');

        try {
            Mail::to($clientEmail)->send(new ClientBookingCancelledMail(
                firstName: $firstName,
                gigId: $gigId,
                eventDate: $eventDate,
                refundDetails: $refund,
                searchLink: $link
            ));
            Log::info("Booking cancelled email sent to client {$clientEmail} for Gig {$gigId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send booking cancelled email to {$clientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Login Notification Email to User (Crew or Client or Admin)
     */
    public static function sendLoginNotification(
        string $email,
        string $name,
        string $role = 'Crew Member',
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): bool {
        if (!$email) {
            return false;
        }

        try {
            Mail::to($email)->send(new LoginNotificationMail(
                recipientName: $name ?: 'Member',
                email: $email,
                role: $role,
                loginTime: now()->format('M j, Y - g:i A T'),
                ipAddress: $ipAddress ?: (request()?->ip() ?: '127.0.0.1'),
                deviceInfo: $userAgent ?: (request()?->userAgent() ?: 'Web Browser'),
                securityLink: route('password.request')
            ));
            Log::info("Login notification email sent to {$email} ({$role})");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send login notification to {$email}: " . $e->getMessage());
            return false;
        }
    }
}
