<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

it('requires authentication to search POS customers', function (): void {
    $this->getJson(route('pos.customers.search', ['q' => 'Ada']))->assertUnauthorized();
});

it('returns only the permitted fields for matching active customers', function (): void {
    DB::table('customers')->insert([
        ['id' => 1, 'customer_code' => 'TEST-A', 'full_name' => 'Ada Test', 'phone' => '09170001111', 'email' => 'ada@example.test', 'deleted_at' => null],
        ['id' => 2, 'customer_code' => 'TEST-B', 'full_name' => 'Ada Deleted', 'phone' => null, 'email' => 'deleted@example.test', 'deleted_at' => now()],
    ]);
    $cashier = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $cashier->setRelation('role', (new Role)->forceFill(['key' => 'cashier']));
    $this->actingAs($cashier)->getJson(route('pos.customers.search', ['q' => 'Ada']))
        ->assertOk()->assertExactJson([['id' => 1, 'name' => 'Ada Test', 'phone' => '09170001111']]);
});
