<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

it('limits sales search to the cashier and selected dates', function (): void {
    DB::table('roles')->insert(['id' => 1, 'name' => 'Cashier', 'key' => 'cashier']);
    DB::table('users')->insert([
        [
            'id' => 1,
            'staff_code' => 'TEST1',
            'role_id' => 1,
            'username' => 'one',
            'password' => 'unused',
            'full_name' => 'Cashier A',
        ],
        [
            'id' => 2,
            'staff_code' => 'TEST2',
            'role_id' => 1,
            'username' => 'two',
            'password' => 'unused',
            'full_name' => 'Cashier B',
        ],
    ]);
    DB::table('customers')->insert(['id' => 1, 'customer_code' => 'TEST', 'full_name' => 'Search Customer']);
    DB::table('sales')->insert([
        [
            'id' => 1,
            'transaction_code' => 'TRX-000001',
            'sold_by' => 1,
            'customer_id' => 1,
            'subtotal' => 10,
            'total_amount' => 10,
            'amount_received' => 10,
            'gcash_reference' => null,
            'payment_method' => 'cash',
            'completed_at' => '2025-03-10 12:00:00',
        ],
        [
            'id' => 2,
            'transaction_code' => 'TRX-000002',
            'sold_by' => 2,
            'customer_id' => 1,
            'subtotal' => 20,
            'total_amount' => 20,
            'amount_received' => 20,
            'gcash_reference' => 'ABC123',
            'payment_method' => 'gcash',
            'completed_at' => '2025-03-10 12:00:00',
        ],
    ]);
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'cashier']));
    $this->actingAs($user)->get(route('sales.index', ['search' => 'Search Customer', 'start_date' => '2025-03-01', 'end_date' => '2025-03-31']))
        ->assertOk()->assertSee('TRX-000001')->assertDontSee('TRX-000002')->assertSee('Showing 1 to 1 of 1 transactions');
    $this->get(route('sales.show', 2))->assertNotFound();
});
