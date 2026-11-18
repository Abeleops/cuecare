<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

it('prevents an administrator from demoting or deactivating their own account', function (): void {
    DB::table('roles')->insert([['id' => 1, 'name' => 'Administrator', 'key' => 'admin'], ['id' => 2, 'name' => 'Staff', 'key' => 'staff']]);
    DB::table('users')->insert([
        'id' => 1,
        'staff_code' => 'ADMIN-TEST',
        'role_id' => 1,
        'full_name' => 'Admin',
        'username' => 'admin',
        'password' => 'unused',
        'is_active' => true,
    ]);
    $user = User::query()->with('role')->findOrFail(1);
    $this->actingAs($user)->put(route('staff.update', 1), ['full_name' => 'Admin', 'username' => 'admin', 'role' => 'staff'])->assertUnprocessable();
    $this->put(route('staff.update', 1), ['full_name' => 'Admin', 'username' => 'admin', 'role' => 'admin', 'is_active' => '0'])->assertUnprocessable();
    $this->delete(route('staff.destroy', 1))->assertUnprocessable();
    $this->assertDatabaseHas('users', ['id' => 1, 'role_id' => 1, 'is_active' => true, 'deleted_at' => null]);
});
