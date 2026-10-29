<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRequestSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $gigId,
        public string $role,
        public string $eventDate,
        public string $location,
        public string $bookingLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Booking request sent - Gig {$this->gigId}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-request-sent',
            with: [
                'firstName' => $this->firstName,
                'gigId' => $this->gigId,
                'role' => $this->role,
                'eventDate' => $this->eventDate,
                'location' => $this->location,
                'bookingLink' => $this->bookingLink,
            ],
        );
    }
}
