<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && (! $user->is_active || $user->trashed())) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }
        if ($user) {
            $lastActivity = (int) $request->session()->get('last_activity', 0);
            if ($lastActivity > 0 && now()->timestamp - $lastActivity > 1800) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login');
            }
            $request->session()->put('last_activity', now()->timestamp);
        }

        return $next($request);
    }
}
