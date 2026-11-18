<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

it('uses the selected period for dashboard sales and product widgets', function (): void {
    DB::table('roles')->insert(['id' => 1, 'name' => 'Admin', 'key' => 'admin']);
    DB::table('users')->insert(['id' => 1, 'staff_code' => 'TEST', 'role_id' => 1, 'full_name' => 'Admin', 'username' => 'admin', 'password' => 'unused']);
    DB::table('categories')->insert(['id' => 1, 'name' => 'Food', 'category_group' => 'food']);
    DB::table('products')->insert([
        'id' => 1,
        'category_id' => 1,
        'name' => 'Low Stock Item',
        'price' => 50,
        'stock_quantity' => 2,
        'low_stock_threshold' => 5,
        'is_active' => true,
    ]);
    DB::table('sales')->insert([
        'id' => 1,
        'transaction_code' => 'TRX-000001',
        'sold_by' => 1,
        'order_type' => 'dine_in',
        'subtotal' => 100,
        'total_amount' => 100,
        'payment_method' => 'cash',
        'amount_received' => 100,
        'status' => 'completed',
        'completed_at' => '2025-03-10 12:00:00',
    ]);
    DB::table('sale_items')->insert([
        'sale_id' => 1,
        'product_id' => 1,
        'product_name' => 'Low Stock Item',
        'quantity' => 2,
        'unit_price' => 50,
        'total_price' => 100,
    ]);
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'admin', 'name' => 'Administrator']));
    $this->actingAs($user)->get(route('dashboard', ['start_date' => '2025-03-01', 'end_date' => '2025-03-31']))
        ->assertOk()->assertSee('₱100.00')->assertSee('Low Stock Item')->assertSee('TRX-000001')->assertSee('Food');
});
