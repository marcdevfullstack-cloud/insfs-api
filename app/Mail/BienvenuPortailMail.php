<?php

namespace App\Mail;

use App\Models\PortalUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenuPortailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PortalUser $portalUser) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue sur le Portail Étudiant INSFS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bienvenu-portail',
        );
    }
}
