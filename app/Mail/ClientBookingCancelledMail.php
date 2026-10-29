<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientBookingCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $gigId,
        public string $eventDate,
        public string $refundDetails,
        public string $searchLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your booking {$this->gigId} has been cancelled",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-booking-cancelled',
            with: [
                'firstName' => $this->firstName,
                'gigId' => $this->gigId,
                'eventDate' => $this->eventDate,
                'refundDetails' => $this->refundDetails,
                'searchLink' => $this->searchLink,
            ],
        );
    }
}
