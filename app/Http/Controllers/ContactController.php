<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
    }

    public function store(ContactRequest $request)
    {
        // 'website' is a honeypot field (hidden via CSS in the form) — real
        // visitors never fill it in, so ContactRequest already rejects bots
        // that do. Rate limiting on the route (see routes/web.php) covers
        // repeated automated submissions.
        $validated = $request->safe()->except('website');

        // Always logged so nothing is silently lost even if mail isn't
        // configured or fails.
        Log::info('Contact form submission', $validated);

        try {
            Mail::to(config('services.admin.notification_email'))->send(new ContactFormMail($validated));
        } catch (\Throwable $e) {
            Log::warning('Contact form email failed: '.$e->getMessage());
        }

        return back()->with('success', 'Thanks for reaching out! We will get back to you soon.');
    }
}
