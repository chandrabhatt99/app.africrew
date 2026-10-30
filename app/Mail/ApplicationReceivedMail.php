<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $verificationUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify your email address - AfriCrew',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-received',
            with: [
                'firstName' => $this->firstName,
                'verificationUrl' => $this->verificationUrl,
            ],
        );
    }
}
