<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CrewGigAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $gigId,
        public string $eventName,
        public string $clientName,
        public string $eventDate,
        public string $offeredRate,
        public string $shiftDuration,
        public string $jobLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Gig Invitation: {$this->eventName} on {$this->eventDate}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.crew-gig-assigned',
            with: [
                'firstName' => $this->firstName,
                'gigId' => $this->gigId,
                'eventName' => $this->eventName,
                'clientName' => $this->clientName,
                'eventDate' => $this->eventDate,
                'offeredRate' => $this->offeredRate,
                'shiftDuration' => $this->shiftDuration,
                'jobLink' => $this->jobLink,
            ],
        );
    }
}
