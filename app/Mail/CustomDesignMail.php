<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomDesignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data, public ?string $imagePath = null)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New custom nail design request from '.$this->data['name'],
            replyTo: [$this->data['email']],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.custom-design');
    }

    public function attachments(): array
    {
        return $this->imagePath ? [Attachment::fromStorageDisk('public', $this->imagePath)] : [];
    }
}
