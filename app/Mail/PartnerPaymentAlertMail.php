<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerPaymentAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $subjectText, public string $messageText) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectText);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.partner-payment-alert', with: ['messageText' => $this->messageText]);
    }
}