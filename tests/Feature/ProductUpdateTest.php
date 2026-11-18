<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

beforeEach(function (): void {
    $role = Role::query()->create(['name' => 'Test admin', 'key' => 'admin']);
    $this->admin = User::query()->create([
        'staff_code' => 'TEST-ADMIN',
        'role_id' => $role->id,
        'full_name' => 'Test Admin',
        'username' => 'test-admin',
        'password' => 'password',
        'is_active' => true,
    ]);
    $this->category = DB::table('categories')->insertGetId(['name' => 'Test category', 'category_group' => 'food']);
    $this->otherCategory = DB::table('categories')->insertGetId(['name' => 'Other category', 'category_group' => 'food']);
    $this->product = DB::table('products')->insertGetId([
        'name' => 'Test product',
        'category_id' => $this->category,
        'price' => 10,
        'stock_quantity' => 7,
        'low_stock_threshold' => 2,
    ]);
    $this->payload = [
        'name' => 'Updated product',
        'category_id' => $this->otherCategory,
        'price' => 12,
        'low_stock_threshold' => 3,
    ];
    $this->actingAs($this->admin);
});

it('updates the category and threshold and audits price changes', function (): void {
    $this->putJson(route('products.update', $this->product), $this->payload)->assertRedirect();
    $this->assertDatabaseHas('products', [
        'id' => $this->product,
        'category_id' => $this->otherCategory,
        'low_stock_threshold' => 3,
        'stock_quantity' => 7,
        'price' => 12,
    ]);
    $this->assertDatabaseHas('audit_logs', ['action' => 'product_price_changed', 'subject_id' => $this->product]);
});

it('rejects invalid category and threshold values', function (): void {
    $invalidValues = [
        ['category_id' => null],
        ['category_id' => 999999],
        ['low_stock_threshold' => null],
        ['low_stock_threshold' => -1],
        ['stock_quantity' => 20],
    ];

    foreach ($invalidValues as $invalid) {
        $this->putJson(route('products.update', $this->product), array_replace($this->payload, $invalid))
            ->assertUnprocessable()->assertJsonValidationErrors(array_keys($invalid));
    }
    DB::table('categories')->where('id', $this->otherCategory)->update(['deleted_at' => now()]);
    $this->putJson(route('products.update', $this->product), $this->payload)->assertUnprocessable()->assertJsonValidationErrors('category_id');
    $oldCategoryField = array_replace($this->payload, [
        'category_id' => null,
        'category' => $this->otherCategory,
    ]);
    $this->putJson(route('products.update', $this->product), $oldCategoryField)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('category_id');
    $this->assertDatabaseHas('products', ['id' => $this->product, 'category_id' => $this->category]);
});

it('forbids staff from sending crafted product updates', function (): void {
    $role = Role::query()->create(['name' => 'Test staff', 'key' => 'staff']);
    $this->admin->update(['role_id' => $role->id]);
    $this->admin->unsetRelation('role');
    $this->putJson(route('products.update', $this->product), $this->payload)->assertForbidden();
});
