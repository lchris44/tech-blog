<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Get the locale from the route parameter, session, or default to 'en'
        $locale = $request->route('locale') ?? session('locale', 'en');
        App::setLocale($locale);

        return $next($request);
    }
}
