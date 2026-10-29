<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CrewBookedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $role,
        public string $eventDate,
        public string $gigId,
        public string $contractLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're booked! {$this->role} on {$this->eventDate}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.crew-booked',
            with: [
                'firstName' => $this->firstName,
                'role' => $this->role,
                'eventDate' => $this->eventDate,
                'gigId' => $this->gigId,
                'contractLink' => $this->contractLink,
            ],
        );
    }
}
