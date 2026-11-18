<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

it('audits permission denials and temporarily blocks an abusive IP', function (): void {
    $role = Role::query()->create(['name' => 'Test cashier', 'key' => 'cashier']);
    $cashier = User::query()->create([
        'staff_code' => 'DENIAL-TEST', 'role_id' => $role->id, 'full_name' => 'Test cashier',
        'username' => 'denial-test', 'password' => 'password123', 'is_active' => true,
    ]);
    $this->actingAs($cashier)->withServerVariables(['REMOTE_ADDR' => '192.0.2.75']);
    for ($attempt = 0; $attempt < 50; $attempt++) {
        $this->get(route('staff.index', ['private' => 'do-not-log']))->assertForbidden();
    }
    $denials = DB::table('audit_logs')->where('action', 'permission_denied')->where('user_id', $cashier->id);
    expect($denials->count())->toBe(50);
    $entry = $denials->first();
    expect($entry->user_id)->toBe($cashier->id)
        ->and($entry->metadata)->toContain('StaffView')->not->toContain('do-not-log');
    $this->get(route('health'))->assertTooManyRequests()->assertHeader('Retry-After');

    $this->travel(11)->minutes();
    $this->get(route('health'))->assertOk();
});
