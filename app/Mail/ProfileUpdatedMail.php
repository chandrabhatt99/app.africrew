<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProfileUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $profileLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your profile has been updated',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.profile-updated',
            with: [
                'firstName' => $this->firstName,
                'profileLink' => $this->profileLink,
            ],
        );
    }
}
