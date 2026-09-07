<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSeller
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('seller')->check()) {
            return redirect()->route('seller.login');
        }

        if (Auth::guard('seller')->user()->status !== 'approved') {
            Auth::guard('seller')->logout();
            return redirect()->route('seller.login')->with('error', 'Your seller application is still awaiting approval.');
        }

        return $next($request);
    }
}
