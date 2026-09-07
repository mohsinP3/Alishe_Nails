<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $seller = Auth::guard('seller')->user();

        if (! $seller || ! $seller->hasActiveSubscription()) {
            return redirect()->route('seller.dashboard')->with('error', 'An active subscription is required to manage listed products.');
        }

        return $next($request);
    }
}
