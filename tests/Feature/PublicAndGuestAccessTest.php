<?php

use Illuminate\Testing\TestResponse;

it('returns a minimal public health response with security headers', function (): void {
    $response = $this->get(route('health'));

    $response->assertOk()
        ->assertExactJson(['status' => 'ok'])
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});

it('redirects guests to login for protected modules', function (string $routeName): void {
    /** @var TestResponse $response */
    $response = $this->get(route($routeName));

    $response->assertRedirect(route('login'));
})->with([
    'dashboard' => 'dashboard',
    'pos' => 'pos.index',
    'products' => 'products.index',
    'customers' => 'customers.index',
    'staff' => 'staff.index',
    'sales' => 'sales.index',
    'reports' => 'reports.index',
]);
