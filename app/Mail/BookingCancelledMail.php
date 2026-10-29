<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledMail extends Mailable
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
            subject: "Booking cancelled: {$this->gigId} on {$this->eventDate}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-cancelled',
            with: [
                'firstName' => $this->firstName,
                'gigId' => $this->gigId,
                'eventDate' => $this->eventDate,
            ],
        );
    }
}
