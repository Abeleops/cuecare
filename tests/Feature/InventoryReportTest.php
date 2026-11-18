<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

it('shows filtered stock and category totals', function (): void {
    DB::table('categories')->insert(['id' => 1, 'name' => 'Meals', 'category_group' => 'food', 'is_active' => true]);
    DB::table('products')->insert([
        ['id' => 1, 'category_id' => 1, 'name' => 'Low Item', 'price' => 1, 'stock_quantity' => 2, 'low_stock_threshold' => 5],
        ['id' => 2, 'category_id' => 1, 'name' => 'Available Item', 'price' => 1, 'stock_quantity' => 20, 'low_stock_threshold' => 5],
    ]);
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'admin', 'name' => 'Administrator']));
    $this->actingAs($user)->get(route('reports.inventory', ['category' => 1, 'stock' => 'low_stock']))
        ->assertOk()->assertSee('Low Item')->assertDontSee('Available Item')->assertSee('Showing 1 to 1 of 1 products');
});
