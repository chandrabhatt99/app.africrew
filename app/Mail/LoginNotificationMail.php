<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $email,
        public string $role = 'Crew Member',
        public ?string $loginTime = null,
        public ?string $ipAddress = null,
        public ?string $deviceInfo = null,
        public ?string $securityLink = null,
    ) {
        $this->loginTime = $this->loginTime ?? now()->format('M j, Y - g:i A T');
        $this->ipAddress = $this->ipAddress ?? (request()?->ip() ?: '127.0.0.1');
        $this->deviceInfo = $this->deviceInfo ?? (request()?->userAgent() ?: 'Web Browser');
        $this->securityLink = $this->securityLink ?? (function_exists('route') ? route('password.request') : '#');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "AfriCrew - Security Alert: New Sign-in ({$this->role})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.login-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
