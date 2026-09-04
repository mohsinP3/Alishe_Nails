<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Wire this up to Mail::send() or a support-ticket table once mail
        // credentials / a ContactMessage model are decided on. For now the
        // submission is logged so nothing is silently dropped.
        Log::info('Contact form submission', $validated);

        return back()->with('success', 'Thanks for reaching out! We will get back to you soon.');
    }
}
