<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lightweight admin gate — no full user table/auth system was requested,
 * so access is a single shared password stored in .env (ADMIN_PASSWORD)
 * and a session flag. Good enough for one shop owner managing products;
 * swap for real Laravel auth + a users table if multiple admins are needed.
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('is_admin')) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
