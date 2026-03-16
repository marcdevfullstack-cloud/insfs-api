<?php

namespace App\Mail;

use App\Models\EnrollmentApplication;
use App\Models\PortalUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DossierRejeterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PortalUser $portalUser,
        public EnrollmentApplication $application
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre dossier a été rejeté — INSFS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dossier-rejeter',
        );
    }
}
