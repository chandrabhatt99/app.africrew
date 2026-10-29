<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $amount,
        public string $gigId,
        public string $eventDate,
        public string $bookingLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payment confirmed - {$this->gigId}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-confirmed',
            with: [
                'firstName' => $this->firstName,
                'amount' => $this->amount,
                'gigId' => $this->gigId,
                'eventDate' => $this->eventDate,
                'bookingLink' => $this->bookingLink,
            ],
        );
    }
}
