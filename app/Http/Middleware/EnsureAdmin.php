<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards every /admin/* route (except login) behind the dedicated 'admin'
 * Auth guard — a real, hashed-password, database-backed session, not a
 * shared flag. See config/auth.php for the guard/provider definition.
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
