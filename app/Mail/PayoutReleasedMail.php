<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayoutReleasedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $amount,
        public string $gigId,
        public string $payoutLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payout released: KES {$this->amount} for {$this->gigId}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payout-released',
            with: [
                'firstName' => $this->firstName,
                'amount' => $this->amount,
                'gigId' => $this->gigId,
                'payoutLink' => $this->payoutLink,
            ],
        );
    }
}
