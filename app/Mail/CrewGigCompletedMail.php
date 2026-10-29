<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CrewGigCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $gigId,
        public string $eventDate,
        public string $reportLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Well done! Gig {$this->gigId} completed",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.crew-gig-completed',
            with: [
                'firstName' => $this->firstName,
                'gigId' => $this->gigId,
                'eventDate' => $this->eventDate,
                'reportLink' => $this->reportLink,
            ],
        );
    }
}
