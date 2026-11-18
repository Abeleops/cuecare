<?php

use App\Models\Role;
use App\Models\User;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

it('creates a sale from session cart prices and updates stock atomically', function (): void {
    DB::table('roles')->insert([
        ['id' => 1, 'name' => 'Cashier', 'key' => 'cashier'],
        ['id' => 2, 'name' => 'Admin', 'key' => 'admin'],
    ]);
    DB::table('users')->insert([
        [
            'id' => 1,
            'staff_code' => 'TEST1',
            'role_id' => 1,
            'full_name' => 'Cashier',
            'username' => 'cashier',
            'password' => 'unused',
        ],
        [
            'id' => 2,
            'staff_code' => 'TEST2',
            'role_id' => 2,
            'full_name' => 'Admin',
            'username' => 'admin',
            'password' => 'unused',
        ],
    ]);
    DB::table('categories')->insert(['id' => 1, 'name' => 'Test', 'category_group' => 'food']);
    DB::table('products')->insert(['id' => 1, 'category_id' => 1, 'name' => 'Test product', 'price' => 125, 'stock_quantity' => 3, 'is_active' => true]);
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'cashier']));
    $this->actingAs($user)->withSession(['pos.cart' => [1 => 2]])->post(route('pos.checkout'), [
        'items' => [['product_id' => 1, 'quantity' => 2]],
        'payment_method' => 'cash',
        'amount_received' => 10,
        'order_type' => 'dine_in',
    ])->assertSessionHasErrors('amount_received');
    $this->assertDatabaseCount('sales', 0);
    $this->assertDatabaseHas('products', ['id' => 1, 'stock_quantity' => 3]);

    $this->withSession(['pos.cart' => [1 => 2]])->post(route('pos.checkout'), [
        'items' => [['product_id' => 1, 'quantity' => 2]],
        'payment_method' => 'gcash',
        'amount_received' => 250,
        'order_type' => 'dine_in',
    ])->assertSessionHasErrors('gcash_reference');
    $this->assertDatabaseCount('sales', 0);

    $this->withSession(['pos.cart' => [1 => 4]])->post(route('pos.checkout'), [
        'items' => [['product_id' => 1, 'quantity' => 4]],
        'payment_method' => 'cash',
        'amount_received' => 500,
        'order_type' => 'dine_in',
    ])->assertSessionHasErrors('items');
    $this->assertDatabaseCount('sales', 0);
    $this->assertDatabaseHas('products', ['id' => 1, 'stock_quantity' => 3]);

    $this->actingAs($user)->withSession(['pos.cart' => [1 => 2]])->post(route('pos.checkout'), [
        'items' => [['product_id' => 1, 'quantity' => 2, 'price' => 1]],
        'payment_method' => 'cash',
        'amount_received' => 300,
        'order_type' => 'dine_in',
        'total' => 2,
    ])->assertRedirect(route('pos.index'))
        ->assertSessionHas('completed_transaction_code')
        ->assertSessionMissing('pos.cart');
    $firstSale = DB::table('sales')->first();
    expect($firstSale->transaction_code)->toBe('TRX-'.str_pad((string) $firstSale->id, 6, '0', STR_PAD_LEFT));
    $this->assertDatabaseHas('sales', ['id' => $firstSale->id, 'subtotal' => 250, 'total_amount' => 250, 'change_amount' => 50]);
    $this->assertDatabaseHas('sale_items', ['product_id' => 1, 'quantity' => 2, 'unit_price' => 125]);
    $this->assertDatabaseHas('products', ['id' => 1, 'stock_quantity' => 1]);
    $this->assertDatabaseHas('stock_movements', ['product_id' => 1, 'movement_type' => 'sale', 'quantity_change' => -2, 'resulting_stock' => 1]);
    $this->assertDatabaseHas('audit_logs', ['action' => 'sale_created', 'subject_id' => $firstSale->id]);

    DB::table('products')->where('id', 1)->update(['price' => 200]);
    $administrator = (new User)->forceFill(['id' => 2, 'is_active' => true]);
    $administrator->setRelation('role', (new Role)->forceFill(['key' => 'admin']));
    $this->actingAs($administrator);
    app(CheckoutService::class)->checkout([
        'items' => [['product_id' => 1, 'quantity' => 1]],
        'cart_discount' => ['type' => 'percentage', 'value' => 10],
        'payment_method' => 'gcash',
        'gcash_reference' => 'ABC123456',
        'amount_received' => 180,
        'order_type' => 'dine_in',
    ]);
    $this->assertDatabaseHas('sales', ['subtotal' => 200, 'discount_amount' => 20, 'total_amount' => 180, 'gcash_reference' => 'ABC123456']);
    expect(DB::table('audit_logs')->where('metadata', 'like', '%ABC123456%')->count())->toBe(0);
    expect(DB::table('sales')->where('transaction_code', 'like', 'PENDING-%')->count())->toBe(0);
});
