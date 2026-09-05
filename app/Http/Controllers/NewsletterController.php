<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterRequest;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function store(NewsletterRequest $request)
    {
        $email = $request->validated()['email'];

        $existing = NewsletterSubscriber::where('email', $email)->first();

        if ($existing && $existing->unsubscribed_at === null) {
            return back()->with('success', "You're already subscribed!");
        }

        if ($existing) {
            $existing->update(['unsubscribed_at' => null, 'subscribed_at' => now()]);
        } else {
            NewsletterSubscriber::create(['email' => $email]);
        }

        return back()->with('success', 'Thanks for subscribing to Alishe Nails!');
    }

    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();
        $subscriber->update(['unsubscribed_at' => now()]);

        return view('newsletter.unsubscribed');
    }
}
