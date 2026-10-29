<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventCompleteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $eventDate,
        public string $reviewLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your event is complete - How was your crew?',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-complete',
            with: [
                'firstName' => $this->firstName,
                'eventDate' => $this->eventDate,
                'reviewLink' => $this->reviewLink,
            ],
        );
    }
}
