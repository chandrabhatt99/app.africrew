<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InterviewScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $interviewDate,
        public string $interviewTime,
        public string $interviewerName,
        public ?string $interviewLink = null,
        public ?string $interviewNotes = null,
        public ?string $loginLink = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your AfriCrew Interview is Scheduled: {$this->interviewDate} at {$this->interviewTime}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.interview-scheduled',
            with: [
                'firstName' => $this->firstName,
                'interviewDate' => $this->interviewDate,
                'interviewTime' => $this->interviewTime,
                'interviewerName' => $this->interviewerName,
                'interviewLink' => $this->interviewLink,
                'interviewNotes' => $this->interviewNotes,
                'loginLink' => $this->loginLink ?? route('staff.login'),
            ],
        );
    }
}
