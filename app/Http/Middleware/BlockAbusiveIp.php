<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class BlockAbusiveIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'denied-ip-block:'.hash('sha256', (string) $request->ip());
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $headers = ['Retry-After' => RateLimiter::availableIn($key)];
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Too many requests'], 429, $headers);
            }

            return response()->view('errors.429', [], 429, $headers);
        }

        return $next($request);
    }
}
