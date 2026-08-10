<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use App;

class Locale
{
    public function handle($request, Closure $next)
    {
        $locale = Session::get('locale', 'en');
        if (in_array($locale, ['en', 'id'])) {
            App::setLocale($locale);
        }
        return $next($request);
    }
}
