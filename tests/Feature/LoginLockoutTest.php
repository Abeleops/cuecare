<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

it('locks an account after ten failures and permits login after fifteen minutes', function (): void {
    $role = Role::query()->create(['name' => 'Test cashier', 'key' => 'cashier']);
    $user = User::query()->create([
        'staff_code' => 'LOCKOUT-TEST',
        'role_id' => $role->id,
        'full_name' => 'Test cashier',
        'username' => 'lockout-test',
        'password' => 'password123',
        'is_active' => true,
    ]);

    for ($attempt = 1; $attempt <= 10; $attempt++) {
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.'.$attempt])
            ->post(route('login.store'), ['username' => 'lockout-test', 'password' => 'incorrect'])
            ->assertSessionHasErrors('username');
    }

    expect($user->fresh()->failed_login_attempts)->toBe(10)
        ->and($user->fresh()->locked_until)->not->toBeNull();
    $this->assertDatabaseHas('audit_logs', ['action' => 'account_lockout', 'subject_id' => $user->id]);
    $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.11'])
        ->post(route('login.store'), ['username' => 'lockout-test', 'password' => 'password123'])
        ->assertSessionHasErrors('username');
    $this->assertGuest();

    $this->travel(16)->minutes();
    $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.12'])
        ->post(route('login.store'), ['username' => 'lockout-test', 'password' => 'password123'])
        ->assertRedirect(route('pos.index'));
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->failed_login_attempts)->toBe(0)
        ->and($user->fresh()->locked_until)->toBeNull();
});
