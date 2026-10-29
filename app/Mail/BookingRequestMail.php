<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $role,
        public string $eventDate,
        public string $location,
        public string $gigId,
        public string $clientName,
        public string $gigLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New booking request: {$this->role} on {$this->eventDate}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-request',
            with: [
                'firstName' => $this->firstName,
                'role' => $this->role,
                'eventDate' => $this->eventDate,
                'location' => $this->location,
                'gigId' => $this->gigId,
                'clientName' => $this->clientName,
                'gigLink' => $this->gigLink,
            ],
        );
    }
}
