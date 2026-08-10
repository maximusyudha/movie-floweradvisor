<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class AuthMiddleware
{
    /**
     * Ensure the user is authenticated via session before accessing protected routes.
     */
    public function handle($request, Closure $next)
    {
        if (!Session::has('user')) {
            return redirect()->route('login')
                             ->with('error', 'Please login to continue.');
        }

        return $next($request);
    }
}
