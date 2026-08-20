<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public string $prenom = 'Utilisateur'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Votre code de vérification Elite 2.0');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'code'   => $this->otp,
                'prenom' => $this->prenom,
            ]
        );
    }
}