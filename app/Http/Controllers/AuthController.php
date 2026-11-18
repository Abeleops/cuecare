<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ]);
        $key = Str::lower($validated['username']).'|'.$request->ip();

        // Limit repeated login attempts for this username and IP address
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['username' => 'Invalid username or password.'])->onlyInput('username');
        }
        $user = User::withTrashed()->where('username', $validated['username'])->first();
        $lockoutStarted = false;
        $authenticated = false;

        if ($user && ! $user->trashed() && $user->is_active) {
            // Serialize failures so concurrent attempts cannot bypass the lockout
            $authenticated = DB::transaction(function () use ($user, $validated, &$lockoutStarted): bool {
                $account = User::query()->whereKey($user->id)->lockForUpdate()->first();
                if (! $account || ! $account->is_active) {
                    return false;
                }
                if ($account->locked_until && $account->locked_until->isFuture()) {
                    return false;
                }
                if ($account->locked_until) {
                    $account->forceFill(['failed_login_attempts' => 0, 'locked_until' => null]);
                }
                if (Auth::validate(['username' => $validated['username'], 'password' => $validated['password']])) {
                    $account->forceFill(['failed_login_attempts' => 0, 'locked_until' => null])->save();

                    return true;
                }
                $attempts = $account->failed_login_attempts + 1;
                $lockoutStarted = $attempts >= 10;
                $account->forceFill([
                    'failed_login_attempts' => $attempts,
                    'locked_until' => $lockoutStarted ? now()->addMinutes(15) : null,
                ])->save();

                return false;
            });
        }

        if (! $authenticated) {
            RateLimiter::hit($key, 60);
            $audit->record('login_failure', null, ['username' => $validated['username']]);
            if ($lockoutStarted) {
                $audit->record('account_lockout', $user);
            }

            return back()->withErrors(['username' => 'Invalid username or password.'])->onlyInput('username');
        }
        RateLimiter::clear($key);
        Auth::login($user, (bool) ($validated['remember'] ?? false));
        $request->session()->regenerate();
        $audit->record('login_success', $user);
        // Open the first module available to the user
        $destination = match (true) {
            $user->hasPermission('DashboardView') => route('dashboard'),
            $user->hasPermission('PosAccess') => route('pos.index'),
            $user->hasPermission('ProductsView') => route('products.index'),
            default => route('login'),
        };

        return redirect($destination);
    }

    public function destroy(Request $request, AuditLogger $audit): RedirectResponse
    {
        $audit->record('logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
