<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CrewRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public ?string $reason = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Update Regarding Your AfriCrew Application",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.crew-rejected',
            with: [
                'firstName' => $this->firstName,
                'reason' => $this->reason ?? 'Your profile did not meet our current vetting requirements.',
            ],
        );
    }
}
