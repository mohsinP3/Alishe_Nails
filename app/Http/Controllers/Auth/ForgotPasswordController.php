<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // Uses Laravel's built-in password broker (password_reset_tokens
        // table + Mail). If MAIL_MAILER=log (no SMTP configured yet), the
        // reset link is written to storage/logs/laravel.log instead of
        // failing checkout/registration — nothing breaks without mail setup.
        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'A password reset link has been sent to your email.')
            : back()->withErrors(['email' => __($status)]);
    }
}
