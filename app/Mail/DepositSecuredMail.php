<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepositSecuredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $gigId,
        public string $eventDate
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Deposit secured for Gig {$this->gigId}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.deposit-secured',
            with: [
                'firstName' => $this->firstName,
                'gigId' => $this->gigId,
                'eventDate' => $this->eventDate,
            ],
        );
    }
}
