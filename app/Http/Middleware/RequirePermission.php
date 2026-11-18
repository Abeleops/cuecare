<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RequirePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (! $user?->hasPermission($permission)) {
            // Log the path without query parameters to avoid recording sensitive input
            AuditLog::query()->create([
                'user_id' => $user?->exists ? $user->id : null,
                'action' => 'permission_denied',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => ['permission' => $permission, 'path' => $request->path()],
            ]);
            $ipHash = hash('sha256', (string) $request->ip());
            if (RateLimiter::hit('denied-ip:'.$ipHash, 600) >= 50) {
                RateLimiter::hit('denied-ip-block:'.$ipHash, 600);
            }
            abort(403);
        }

        return $next($request);
    }
}
