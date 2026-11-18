<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

beforeEach(function (): void {
    DB::table('categories')->insert(['id' => 1, 'name' => 'Test category', 'category_group' => 'food']);
    DB::table('products')->insert(['id' => 1, 'category_id' => 1, 'name' => 'Test item', 'price' => 125.50, 'stock_quantity' => 3, 'is_active' => true]);
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'cashier']));
    $this->actingAs($user);
});

it('persists cart quantities and calculates totals from product prices', function (): void {
    $this->putJson(route('pos.cart.update', 1), ['quantity' => 2, 'price' => 0])->assertOk()->assertJsonPath('subtotal', '₱251.00');
    $this->getJson(route('pos.cart'))->assertOk()->assertJsonPath('items.0.quantity', 2)->assertJsonPath('total', '₱251.00');
    $this->putJson(route('pos.cart.update', 1), ['quantity' => 3])->assertOk()->assertJsonPath('total', '₱376.50');
    $this->putJson(route('pos.cart.update', 1), ['quantity' => 4])->assertUnprocessable();
    $this->getJson(route('pos.cart'))->assertJsonPath('items.0.quantity', 3);
    $this->deleteJson(route('pos.cart.remove', 1))->assertOk()->assertJsonCount(0, 'items');
    $this->putJson(route('pos.cart.update', 1), ['quantity' => 1])->assertOk();
    $this->deleteJson(route('pos.cart.clear'))->assertOk()->assertJsonPath('total', '₱0.00')->assertJsonCount(0, 'items');
});

it('calculates fixed and percentage discounts and rejects excessive discounts', function (): void {
    $user = (new User)->forceFill(['id' => 2, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'admin']));
    $this->actingAs($user);
    $this->putJson(route('pos.cart.update', 1), ['quantity' => 2])->assertOk();
    $this->putJson(route('pos.cart.discount'), ['type' => 'fixed', 'value' => 50])
        ->assertOk()
        ->assertJsonPath('discount', '₱50.00')
        ->assertJsonPath('total', '₱201.00');
    $this->putJson(route('pos.cart.discount'), ['type' => 'percentage', 'value' => 10])
        ->assertOk()
        ->assertJsonPath('discount', '₱25.10')
        ->assertJsonPath('total', '₱225.90');
    $this->putJson(route('pos.cart.discount'), ['type' => 'fixed', 'value' => 252])->assertUnprocessable();
    $this->getJson(route('pos.cart'))->assertJsonPath('total', '₱225.90');
});
