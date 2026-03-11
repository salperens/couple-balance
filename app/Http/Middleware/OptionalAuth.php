<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class OptionalAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $next($request);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if ($accessToken) {
            $user = $accessToken->tokenable;

            $request->setUserResolver(static fn() => $user);
        }

        return $next($request);
    }
}
