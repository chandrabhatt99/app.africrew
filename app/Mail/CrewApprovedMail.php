<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CrewApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $profileLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're approved! Welcome to AfriCrew",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.crew-approved',
            with: [
                'firstName' => $this->firstName,
                'profileLink' => $this->profileLink,
            ],
        );
    }
}
