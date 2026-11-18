<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

it('calculates totals for the selected date and payment method', function (): void {
    DB::table('roles')->insert(['id' => 1, 'name' => 'Admin', 'key' => 'admin']);
    DB::table('users')->insert(['id' => 1, 'staff_code' => 'TEST', 'role_id' => 1, 'full_name' => 'Admin', 'username' => 'admin', 'password' => 'unused']);
    DB::table('sales')->insert([
        [
            'id' => 1, 'transaction_code' => 'TRX-1', 'sold_by' => 1,
            'subtotal' => 125, 'amount_received' => 125, 'gcash_reference' => null,
            'status' => 'completed', 'payment_method' => 'cash', 'total_amount' => 125,
            'completed_at' => '2025-03-10 12:00:00',
        ],
        [
            'id' => 2, 'transaction_code' => 'TRX-2', 'sold_by' => 1,
            'subtotal' => 75, 'amount_received' => 75, 'gcash_reference' => 'ABC123',
            'status' => 'completed', 'payment_method' => 'gcash', 'total_amount' => 75,
            'completed_at' => '2025-03-10 12:00:00',
        ],
        [
            'id' => 3, 'transaction_code' => 'TRX-3', 'sold_by' => 1,
            'subtotal' => 999, 'amount_received' => 999, 'gcash_reference' => null,
            'status' => 'voided', 'payment_method' => 'cash', 'total_amount' => 999,
            'completed_at' => '2025-03-10 12:00:00',
        ],
    ]);
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'admin']));
    $this->actingAs($user)->get(route('reports.index', ['start_date' => '2025-03-01', 'end_date' => '2025-03-30', 'payment' => 'cash']))
        ->assertOk()->assertSee('₱125.00')->assertDontSee('₱999.00');
});
