<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $invoiceNo,
        public string $gigId,
        public string $amount
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your AfriCrew receipt - {$this->invoiceNo}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.receipt',
            with: [
                'firstName' => $this->firstName,
                'invoiceNo' => $this->invoiceNo,
                'gigId' => $this->gigId,
                'amount' => $this->amount,
            ],
        );
    }
}
