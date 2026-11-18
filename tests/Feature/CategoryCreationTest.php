<?php

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

uses(DatabaseTransactions::class);

beforeEach(function (): void {
    $adminRole = Role::query()->firstOrCreate(
        ['key' => 'admin'],
        ['name' => 'Category test admin'],
    );
    $suffix = Str::lower(Str::random(8));
    $this->admin = User::query()->create([
        'staff_code' => 'CAT-'.$suffix,
        'role_id' => $adminRole->id,
        'full_name' => 'Category test admin',
        'username' => 'category-'.$suffix,
        'password' => 'password123',
        'is_active' => true,
    ]);
    $this->actingAs($this->admin);
});

it('creates a category and makes it available in the product and POS filters', function (): void {
    $name = 'QA category '.Str::random(8);
    $response = $this->postJson(route('categories.store'), [
        'name' => '  '.$name.'  ',
        'category_group' => 'beverage',
    ])->assertCreated()->assertJsonPath('name', $name)->assertJsonPath('category_group', 'beverage');

    $categoryId = $response->json('id');
    $this->assertDatabaseHas('categories', ['id' => $categoryId, 'name' => $name, 'category_group' => 'beverage']);
    $this->assertDatabaseHas('audit_logs', ['action' => 'category_created', 'subject_id' => $categoryId]);
    $this->get(route('products.index'))->assertOk()->assertSee($name);
    $this->get(route('pos.index'))->assertOk()->assertSee($name);
});

it('validates category names and groups and denies users without product management permission', function (): void {
    $category = Category::query()->create(['name' => 'Existing '.Str::random(8), 'category_group' => 'food']);
    $this->postJson(route('categories.store'), ['name' => $category->name, 'category_group' => 'food'])
        ->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->postJson(route('categories.store'), ['name' => '   ', 'category_group' => 'other'])
        ->assertUnprocessable()->assertJsonValidationErrors(['name', 'category_group']);

    $staffRole = Role::query()->firstOrCreate(['key' => 'staff'], ['name' => 'Category test staff']);
    $this->admin->update(['role_id' => $staffRole->id]);
    $this->admin->unsetRelation('role');
    $this->postJson(route('categories.store'), ['name' => 'Not allowed', 'category_group' => 'food'])
        ->assertForbidden();
});
