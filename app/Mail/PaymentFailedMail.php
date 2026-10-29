<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $gigId,
        public string $failureReason,
        public string $paymentLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Action needed: Payment failed for {$this->gigId}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-failed',
            with: [
                'firstName' => $this->firstName,
                'gigId' => $this->gigId,
                'failureReason' => $this->failureReason,
                'paymentLink' => $this->paymentLink,
            ],
        );
    }
}
