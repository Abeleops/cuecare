<?php

use App\Models\Role;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(DatabaseTransactions::class);

it('re-encodes uploaded product and staff images before storing them', function (): void {
    Storage::fake('public');
    $role = Role::query()->create(['name' => 'Test admin', 'key' => 'admin']);
    $admin = User::query()->create([
        'staff_code' => 'IMAGE-ADMIN', 'role_id' => $role->id, 'full_name' => 'Admin',
        'username' => 'image-admin', 'password' => 'password123', 'is_active' => true,
    ]);
    $staffRole = Role::query()->create(['name' => 'Test staff', 'key' => 'staff']);
    $category = DB::table('categories')->insertGetId(['name' => 'Images', 'category_group' => 'food']);
    $this->actingAs($admin);

    $productImage = UploadedFile::fake()->image('product.png', 20, 20);
    file_put_contents($productImage->getRealPath(), 'UNTRUSTED_TRAILING_CONTENT', FILE_APPEND);
    $this->post(route('products.store'), [
        'name' => 'Test image product', 'category_id' => $category, 'price' => 5,
        'stock_quantity' => 0, 'low_stock_threshold' => 1, 'image' => $productImage,
    ])->assertRedirect();
    $productPath = DB::table('products')->where('name', 'Test image product')->value('image');
    Storage::disk('public')->assertExists($productPath);
    expect(Storage::disk('public')->get($productPath))->not->toContain('UNTRUSTED_TRAILING_CONTENT');
    $productId = DB::table('products')->where('name', 'Test image product')->value('id');
    $productUpdate = ['name' => 'Test image product', 'category_id' => $category, 'price' => 5, 'low_stock_threshold' => 1];
    $this->put(route('products.update', $productId), [
        ...$productUpdate,
        'image' => UploadedFile::fake()->createWithContent('invalid.png', 'not an image'),
    ])->assertSessionHasErrorsIn('productEdit', 'image');
    Storage::disk('public')->assertExists($productPath);
    $this->put(route('products.update', $productId), [
        ...$productUpdate,
        'image' => UploadedFile::fake()->image('replacement.jpeg', 20, 20),
        'remove_image' => 1,
    ])->assertRedirect();
    $replacementPath = DB::table('products')->where('id', $productId)->value('image');
    Storage::disk('public')->assertMissing($productPath);
    Storage::disk('public')->assertExists($replacementPath);
    expect(getimagesizefromstring(Storage::disk('public')->get($replacementPath))['mime'])->toBe('image/jpeg');
    $this->put(route('products.update', $productId), [
        ...$productUpdate,
        'remove_image' => 1,
    ])->assertRedirect();
    Storage::disk('public')->assertMissing($replacementPath);
    expect(DB::table('products')->where('id', $productId)->value('image'))->toBeNull();

    $avatar = UploadedFile::fake()->image('avatar.png', 20, 20);
    file_put_contents($avatar->getRealPath(), 'UNTRUSTED_TRAILING_CONTENT', FILE_APPEND);
    $this->post(route('staff.store'), [
        'full_name' => 'Test worker', 'username' => 'image-worker', 'role' => $staffRole->key,
        'password' => 'password123', 'avatar' => $avatar,
    ])->assertRedirect();
    $avatarPath = User::query()->where('username', 'image-worker')->value('avatar');
    Storage::disk('public')->assertExists($avatarPath);
    expect(Storage::disk('public')->get($avatarPath))->not->toContain('UNTRUSTED_TRAILING_CONTENT');
});

it('re-encodes WebP uploads without trailing content', function (): void {
    Storage::fake('public');
    $image = UploadedFile::fake()->image('product.webp', 20, 20);
    file_put_contents($image->getRealPath(), 'UNTRUSTED_TRAILING_CONTENT', FILE_APPEND);
    $path = app(ImageUploadService::class)->store($image, 'products', 'image');
    $stored = Storage::disk('public')->get($path);
    expect($stored)->not->toContain('UNTRUSTED_TRAILING_CONTENT')
        ->and(getimagesizefromstring($stored)['mime'])->toBe('image/webp');
});
