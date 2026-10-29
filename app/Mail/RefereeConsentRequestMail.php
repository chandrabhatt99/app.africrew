<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefereeConsentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $refereeFirstName;
    public string $crewFirstName;

    public function __construct(
        public string $refereeName,
        public string $refereeOrg,
        public string $crewName,
        public string $crewCategory,
        public string $reviewUrl,
        public ?string $period = null,
        public ?string $companyOrProject = null,
        public ?string $consentUrl = null,
        public ?string $declineUrl = null
    ) {
        // Resolve first names for friendly greeting
        $refParts = preg_split('/\s+/', trim($this->refereeName));
        $this->refereeFirstName = $refParts[0] ?? $this->refereeName;

        $crewParts = preg_split('/\s+/', trim($this->crewName));
        $this->crewFirstName = $crewParts[0] ?? $this->crewName;

        $this->companyOrProject = $this->companyOrProject ?: $this->refereeOrg;
        $this->period = $this->period ?: 'recent projects';
        $this->consentUrl = $this->consentUrl ?: $this->reviewUrl;
        $this->declineUrl = $this->declineUrl ?: $this->reviewUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Work Reference Request: {$this->crewName} on AfriCrew",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.referee-consent',
            with: [
                'refereeName' => $this->refereeName,
                'refereeFirstName' => $this->refereeFirstName,
                'refereeOrg' => $this->refereeOrg,
                'crewName' => $this->crewName,
                'crewFirstName' => $this->crewFirstName,
                'crewCategory' => $this->crewCategory,
                'reviewUrl' => $this->reviewUrl,
                'period' => $this->period,
                'companyOrProject' => $this->companyOrProject,
                'consentUrl' => $this->consentUrl,
                'declineUrl' => $this->declineUrl,
            ],
        );
    }
}
