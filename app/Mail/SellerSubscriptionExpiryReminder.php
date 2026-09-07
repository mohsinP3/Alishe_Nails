<?php

namespace App\Mail;

use App\Models\SellerSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SellerSubscriptionExpiryReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SellerSubscription $subscription) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Alishe Nails seller subscription expires soon');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.seller-subscription-expiry');
    }
}
