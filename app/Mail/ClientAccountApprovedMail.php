<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientAccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $searchLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your AfriCrew account is approved - Start hiring',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-account-approved',
            with: [
                'firstName' => $this->firstName,
                'searchLink' => $this->searchLink,
            ],
        );
    }
}
