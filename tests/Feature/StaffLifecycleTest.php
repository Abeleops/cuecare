<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(DatabaseTransactions::class);

beforeEach(function (): void {
    $this->adminRole = Role::query()->create(['name' => 'Admin test', 'key' => 'admin']);
    $this->staffRole = Role::query()->create(['name' => 'Staff test', 'key' => 'staff']);
    $this->cashierRole = Role::query()->create(['name' => 'Cashier test', 'key' => 'cashier']);
    $this->admin = User::query()->create([
        'staff_code' => 'ADMIN-TEST',
        'role_id' => $this->adminRole->id,
        'full_name' => 'Admin',
        'username' => 'test-admin',
        'password' => 'password123',
        'is_active' => true,
    ]);
    $this->actingAs($this->admin);
});

it('generates staff codes from inserted IDs', function (): void {
    foreach (['sequence-one', 'sequence-two'] as $username) {
        $this->post(route('staff.store'), [
            'full_name' => 'Sequential worker',
            'username' => $username,
            'role' => 'staff',
            'password' => 'password123',
        ])->assertRedirect();
        $user = User::query()->where('username', $username)->firstOrFail();
        expect($user->staff_code)->toBe('STF-'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT));
    }
});

it('creates an account with a hashed password and generated staff code', function (): void {
    $this->postJson(route('staff.store'), [
        'full_name' => 'New worker',
        'username' => 'worker',
        'role' => 'staff',
        'password' => 'password123',
    ])->assertRedirect();
    $staff = User::query()->where('username', 'worker')->firstOrFail();
    expect($staff->staff_code)->toMatch('/^STF-\d{4,}$/')->and(Hash::check('password123', $staff->password))->toBeTrue();
    auth()->logout();
    $this->post(route('login.store'), ['username' => 'worker', 'password' => 'password123'])->assertRedirect(route('products.index'));
    $this->assertAuthenticatedAs($staff);
});

it('prevents a deactivated cashier from signing in or continuing an existing session', function (): void {
    $cashier = User::query()->create([
        'staff_code' => 'CASHIER-TEST',
        'role_id' => $this->cashierRole->id,
        'full_name' => 'Cashier',
        'username' => 'cashier-test',
        'password' => 'password123',
        'is_active' => true,
    ]);
    $this->putJson(route('staff.update', $cashier), [
        'full_name' => 'Cashier',
        'username' => 'cashier-test',
        'role' => 'cashier',
        'is_active' => false,
    ])->assertRedirect();
    $this->actingAs($cashier->fresh())->get(route('pos.index'))->assertRedirect(route('login'));
    $this->assertGuest();
    $this->post(route('login.store'), ['username' => 'cashier-test', 'password' => 'password123'])->assertSessionHasErrors('username');
    $this->assertGuest();
});

it('clears database sessions after role changes and password resets', function (): void {
    $staff = User::query()->create([
        'staff_code' => 'WORKER',
        'role_id' => $this->cashierRole->id,
        'full_name' => 'Worker',
        'username' => 'worker',
        'password' => 'password123',
        'is_active' => true,
    ]);
    config(['session.driver' => 'database']);
    DB::table('sessions')->insert(['id' => 'test-session', 'user_id' => $staff->id, 'payload' => '', 'last_activity' => time()]);
    $this->putJson(route('staff.update', $staff), [
        'full_name' => 'Renamed worker',
        'username' => 'worker',
        'role' => 'staff',
        'password' => 'newpassword123',
    ])->assertRedirect();
    $staff->refresh();
    expect($staff->full_name)->toBe('Renamed worker')
        ->and($staff->role_id)->toBe($this->staffRole->id)
        ->and(Hash::check('password123', $staff->password))->toBeFalse()
        ->and(Hash::check('newpassword123', $staff->password))->toBeTrue();
    $this->assertDatabaseMissing('sessions', ['id' => 'test-session']);
    $this->assertDatabaseHas('audit_logs', ['action' => 'staff_updated', 'subject_id' => $staff->id]);
});

it('prevents self demotion and protects the last active administrator', function (): void {
    $second = User::query()->create([
        'staff_code' => 'OTHER',
        'role_id' => $this->adminRole->id,
        'full_name' => 'Other Admin',
        'username' => 'other-admin',
        'password' => 'password123',
        'is_active' => true,
    ]);
    $this->putJson(route('staff.update', $second), [
        'full_name' => 'Other Admin',
        'username' => 'other-admin',
        'role' => 'admin',
        'is_active' => false,
    ])->assertRedirect();
    $this->putJson(route('staff.update', $this->admin), ['full_name' => 'Admin', 'username' => 'test-admin', 'role' => 'staff'])->assertUnprocessable();
    expect(User::query()->where('role_id', $this->adminRole->id)->where('is_active', true)->count())->toBe(1);
    $this->actingAs($second->fresh())->putJson(route('staff.update', $this->admin), [
        'full_name' => 'Admin',
        'username' => 'test-admin',
        'role' => 'admin',
        'is_active' => false,
    ])->assertRedirect(route('login'));
    expect(User::query()->where('role_id', $this->adminRole->id)->where('is_active', true)->count())->toBe(1);
});

it('forbids cashiers and staff from every staff route', function (): void {
    $target = User::query()->create([
        'staff_code' => 'TARGET-TEST',
        'role_id' => $this->staffRole->id,
        'full_name' => 'Target',
        'username' => 'target-test',
        'password' => 'password123',
        'is_active' => true,
    ]);
    foreach ([$this->cashierRole, $this->staffRole] as $role) {
        $this->admin->update(['role_id' => $role->id]);
        $this->admin->unsetRelation('role');
        $this->get(route('staff.index'))->assertForbidden();
        $this->postJson(route('staff.store'), [])->assertForbidden();
        $this->putJson(route('staff.update', $target), [])->assertForbidden();
        $this->deleteJson(route('staff.destroy', $target))->assertForbidden();
    }
});

it('reserves deleted usernames and rejects non-image avatars', function (): void {
    $staff = User::query()->create([
        'staff_code' => 'FORMER-TEST',
        'role_id' => $this->staffRole->id,
        'full_name' => 'Former staff',
        'username' => 'former',
        'password' => 'password123',
        'is_active' => true,
    ]);
    $staff->delete();
    $this->postJson(route('staff.store'), [
        'full_name' => 'New worker',
        'username' => 'former',
        'role' => 'staff',
        'password' => 'password123',
    ])->assertUnprocessable()->assertJsonValidationErrors('username');
    $this->postJson(route('staff.store'), [
        'full_name' => 'New worker',
        'username' => 'new-worker',
        'role' => 'staff',
        'password' => 'password123',
        'avatar' => UploadedFile::fake()->createWithContent('fake.jpg', 'not an image'),
    ])->assertUnprocessable()->assertJsonValidationErrors('avatar');
});

it('rejects a password that matches the username', function (): void {
    $this->postJson(route('staff.store'), [
        'full_name' => 'New worker',
        'username' => 'matching-user',
        'role' => 'staff',
        'password' => 'matching-user',
    ])->assertUnprocessable()->assertJsonValidationErrors('password');
});

it('keeps a deleted staff member visible on their past sales', function (): void {
    $staff = User::query()->create([
        'staff_code' => 'FORMER-TEST',
        'role_id' => $this->staffRole->id,
        'full_name' => 'Former staff',
        'username' => 'former',
        'password' => 'password123',
        'is_active' => true,
    ]);
    $saleId = DB::table('sales')->insertGetId([
        'transaction_code' => 'STAFF-HISTORY',
        'sold_by' => $staff->id,
        'subtotal' => 100,
        'total_amount' => 100,
        'payment_method' => 'cash',
        'amount_received' => 100,
        'status' => 'completed',
        'completed_at' => now(),
    ]);
    $this->deleteJson(route('staff.destroy', $staff))->assertRedirect();
    $this->assertSoftDeleted('users', ['id' => $staff->id]);
    $this->get(route('staff.index'))->assertOk()->assertDontSee('Former staff');
    $this->get(route('sales.show', $saleId))->assertOk()->assertSee('Former staff');
});
