<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRescheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $gigId,
        public string $oldDate,
        public string $newDate,
        public ?string $bookingLink = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your booking has been rescheduled to {$this->newDate}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-rescheduled',
            with: [
                'firstName' => $this->firstName,
                'gigId' => $this->gigId,
                'oldDate' => $this->oldDate,
                'newDate' => $this->newDate,
                'bookingLink' => $this->bookingLink,
            ],
        );
    }
}
