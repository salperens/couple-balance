<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language', config('app.locale'));
        $locale = strtolower($locale);

        if (!in_array($locale, ['tr','en'])) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
