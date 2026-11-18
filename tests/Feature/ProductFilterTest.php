<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

it('filters products by name, category and stock status', function (): void {
    DB::table('roles')->insert(['id' => 1, 'name' => 'Staff', 'key' => 'staff']);
    DB::table('users')->insert([
        'id' => 1,
        'staff_code' => 'STAFF-TEST',
        'role_id' => 1,
        'full_name' => 'Staff',
        'username' => 'staff-test',
        'password' => 'unused',
        'is_active' => true,
    ]);
    DB::table('categories')->insert([
        ['id' => 1, 'name' => 'Food', 'category_group' => 'food', 'parent_id' => null, 'is_active' => true],
        ['id' => 2, 'name' => 'Meals', 'category_group' => 'food', 'parent_id' => 1, 'is_active' => true],
    ]);
    DB::table('products')->insert([
        ['id' => 1, 'category_id' => 2, 'name' => 'Test Meal', 'price' => 50, 'stock_quantity' => 2, 'low_stock_threshold' => 5, 'is_active' => true],
        ['id' => 2, 'category_id' => 2, 'name' => 'Other Meal', 'price' => 30, 'stock_quantity' => 20, 'low_stock_threshold' => 5, 'is_active' => true],
    ]);
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'staff', 'name' => 'Staff']));
    $this->actingAs($user)->get(route('products.index', ['search' => 'Test', 'category' => 2, 'group' => 'food', 'stock' => 'lowStock']))
        ->assertOk()->assertSee('Test Meal')->assertDontSee('Other Meal')->assertSee('Showing 1 to 1 of 1 products')->assertSee('Low Stock');
    $this->patch(route('products.stock', 1), ['quantity_change' => 3, 'note' => 'Count correction'])->assertRedirect();
    $this->assertDatabaseHas('products', ['id' => 1, 'stock_quantity' => 5]);
    $this->assertDatabaseHas('stock_movements', ['product_id' => 1, 'movement_type' => 'adjustment', 'quantity_change' => 3, 'resulting_stock' => 5]);
    $this->put(route('products.update', 1), [])->assertForbidden();
});
