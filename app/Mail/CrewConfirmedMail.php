<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CrewConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $gigId,
        public string $crewName,
        public string $eventDate,
        public string $contractLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your crew is confirmed for {$this->eventDate}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.crew-confirmed',
            with: [
                'firstName' => $this->firstName,
                'gigId' => $this->gigId,
                'crewName' => $this->crewName,
                'eventDate' => $this->eventDate,
                'contractLink' => $this->contractLink,
            ],
        );
    }
}
