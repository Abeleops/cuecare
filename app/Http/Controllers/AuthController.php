<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $validated = $request->validate(['username' => ['required', 'string', 'max:50'], 'password' => ['required', 'string'], 'remember' => ['boolean']]);
        $key = Str::lower($validated['username']).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['username' => 'Invalid username or password.'])->onlyInput('username');
        }
        $user = User::withTrashed()->where('username', $validated['username'])->first();
        if (! $user || $user->trashed() || ! $user->is_active || ! Auth::validate(['username' => $validated['username'], 'password' => $validated['password']])) {
            RateLimiter::hit($key, 60);
            $audit->record('login_failure', null, ['username' => $validated['username']]);

            return back()->withErrors(['username' => 'Invalid username or password.'])->onlyInput('username');
        }
        RateLimiter::clear($key);
        Auth::login($user, (bool) ($validated['remember'] ?? false));
        $request->session()->regenerate();
        $audit->record('login_success', $user);
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
