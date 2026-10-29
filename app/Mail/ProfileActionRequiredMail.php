<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProfileActionRequiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $rejectionReason,
        public string $profileLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required: Update your AfriCrew profile',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.profile-action-required',
            with: [
                'firstName' => $this->firstName,
                'rejectionReason' => $this->rejectionReason,
                'profileLink' => $this->profileLink,
            ],
        );
    }
}
