<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerSponsorshipMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $requestData, public readonly array $plan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->requestData['email'], $this->requestData['contact_name'])],
            subject: 'Demande de publication sponsorisée — '.$this->requestData['company_name'],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.partner-sponsorship');
    }
}
