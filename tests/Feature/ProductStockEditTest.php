<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

uses(DatabaseTransactions::class);

beforeEach(function (): void {
    $role = Role::query()->firstOrCreate(['key' => 'admin'], ['name' => 'Stock edit admin']);
    $suffix = Str::lower(Str::random(8));
    $this->admin = User::query()->create([
        'staff_code' => 'STOCK-'.$suffix,
        'role_id' => $role->id,
        'full_name' => 'Stock test admin',
        'username' => 'stock-'.$suffix,
        'password' => 'password123',
        'is_active' => true,
    ]);
    $this->category = Category::query()->create(['name' => 'Stock category '.$suffix, 'category_group' => 'food']);
    $this->product = Product::query()->create([
        'name' => 'Stock test product',
        'category_id' => $this->category->id,
        'price' => 10,
        'stock_quantity' => 7,
        'low_stock_threshold' => 2,
        'is_active' => true,
    ]);
    $this->payload = [
        'name' => 'Updated stock product',
        'category_id' => $this->category->id,
        'price' => 10,
        'low_stock_threshold' => 2,
        'stock_quantity' => 12,
        'original_stock' => 7,
    ];
    $this->actingAs($this->admin);
});

it('saves an edited stock quantity and records the adjustment', function (): void {
    $this->putJson(route('products.update', $this->product), $this->payload)->assertRedirect();
    $this->assertDatabaseHas('products', ['id' => $this->product->id, 'stock_quantity' => 12]);
    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $this->product->id,
        'movement_type' => 'adjustment',
        'quantity_change' => 5,
        'resulting_stock' => 12,
    ]);
    $this->assertDatabaseHas('audit_logs', ['action' => 'stock_adjustment', 'subject_id' => $this->product->id]);
});

it('still accepts opening stock when creating a product', function (): void {
    $this->postJson(route('products.store'), [
        'name' => 'New stock product',
        'category_id' => $this->category->id,
        'price' => 10,
        'stock_quantity' => 4,
        'low_stock_threshold' => 2,
    ])->assertRedirect();
    $this->assertDatabaseHas('products', ['name' => 'New stock product', 'stock_quantity' => 4]);
});

it('rejects stale stock changes without saving other edits', function (): void {
    $this->product->update(['stock_quantity' => 6]);
    $this->putJson(route('products.update', $this->product), $this->payload)
        ->assertUnprocessable()->assertJsonValidationErrors('stock_quantity');
    $this->assertDatabaseHas('products', ['id' => $this->product->id, 'name' => 'Stock test product', 'stock_quantity' => 6]);
    expect($this->product->stockMovements()->count())->toBe(0);
});

it('does not overwrite newer stock when the stock field was unchanged', function (): void {
    $this->product->update(['stock_quantity' => 6]);
    $this->payload['stock_quantity'] = 7;
    $this->putJson(route('products.update', $this->product), $this->payload)->assertRedirect();
    $this->assertDatabaseHas('products', ['id' => $this->product->id, 'name' => 'Updated stock product', 'stock_quantity' => 6]);
    expect($this->product->stockMovements()->count())->toBe(0);
});

it('forbids stock changes without inventory adjustment permission', function (): void {
    $role = Role::query()->firstOrCreate(['key' => 'product-editor'], ['name' => 'Product editor']);
    config()->set('permissions.roles.product-editor', ['ProductsManage', 'ProductsView']);
    $this->admin->update(['role_id' => $role->id]);
    $this->admin->unsetRelation('role');
    $this->putJson(route('products.update', $this->product), $this->payload)->assertForbidden();
    $this->assertDatabaseHas('products', ['id' => $this->product->id, 'stock_quantity' => 7]);
});
