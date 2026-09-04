<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (session('is_admin')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        // Single shared password from .env — never hard-coded, never trust
        // client-side checks. See ADMIN_PASSWORD in .env.example.
        if ($request->password !== config('services.admin.password')) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $request->session()->put('is_admin', true);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        $request->session()->regenerate();

        return redirect()->route('admin.login')->with('success', 'Logged out.');
    }
}
