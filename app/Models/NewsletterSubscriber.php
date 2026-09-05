<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_token',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (NewsletterSubscriber $subscriber) {
            $subscriber->unsubscribe_token ??= Str::random(40);
            $subscriber->subscribed_at ??= now();
        });
    }
}
