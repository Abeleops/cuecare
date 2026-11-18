<?php

use App\Enums\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

uses(DatabaseTransactions::class);

it('requires authentication and a known permission on every protected application route', function (): void {
    $publicRoutes = ['landing.index', 'login', 'login.store', 'health'];
    $frameworkRoutes = ['_boost/browser-logs', 'storage/{path}', 'up'];
    $knownPermissions = array_column(Permission::cases(), 'value');
    foreach (Route::getRoutes() as $route) {
        if (in_array($route->uri(), $frameworkRoutes, true) || in_array($route->getName(), $publicRoutes, true)) {
            continue;
        }
        $middleware = $route->gatherMiddleware();
        expect($middleware)->toContain('auth')->toContain('active');
        if ($route->getName() === 'logout') {
            continue;
        }
        $permission = collect($middleware)->first(fn (string $item): bool => str_starts_with($item, 'permission:'));
        expect($permission)->not->toBeNull("Route {$route->uri()} has no permission middleware");
        expect(substr($permission, strlen('permission:')))->toBeIn($knownPermissions);
    }
});

it('redirects guests and forbids roles without permission on every protected route', function (): void {
    $users = [];
    foreach (['admin', 'cashier', 'staff'] as $key) {
        $role = Role::query()->create(['name' => 'Matrix '.$key, 'key' => $key]);
        $users[$key] = User::query()->create([
            'staff_code' => 'MATRIX-'.$key,
            'role_id' => $role->id,
            'full_name' => 'Matrix '.$key,
            'username' => 'matrix-'.$key,
            'password' => 'password123',
            'is_active' => true,
        ]);
    }
    $categoryId = DB::table('categories')->insertGetId(['name' => 'Matrix category', 'category_group' => 'food']);
    $productId = DB::table('products')->insertGetId(['name' => 'Matrix product', 'category_id' => $categoryId, 'price' => 1]);
    $customerId = DB::table('customers')->insertGetId(['customer_code' => 'MATRIX', 'full_name' => 'Matrix customer']);
    $saleId = DB::table('sales')->insertGetId([
        'transaction_code' => 'MATRIX-SALE', 'customer_id' => $customerId, 'sold_by' => $users['cashier']->id,
        'subtotal' => 1, 'total_amount' => 1, 'payment_method' => 'cash', 'amount_received' => 1,
    ]);
    $parameters = ['product' => $productId, 'customer' => $customerId, 'user' => $users['staff']->id, 'sale' => $saleId];
    $publicRoutes = ['landing.index', 'login', 'login.store', 'health'];
    $frameworkRoutes = ['_boost/browser-logs', 'storage/{path}', 'up'];

    $routeNumber = 0;
    foreach (Route::getRoutes() as $route) {
        if (in_array($route->uri(), $frameworkRoutes, true) || in_array($route->getName(), $publicRoutes, true)) {
            continue;
        }
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.'.++$routeNumber]);
        $routeParameters = array_intersect_key($parameters, array_flip($route->parameterNames()));
        $url = route($route->getName(), $routeParameters);
        $method = $route->methods()[0];
        auth()->logout();
        $this->call($method, $url)->assertRedirect(route('login'));

        $permission = collect($route->gatherMiddleware())->first(fn (string $item): bool => str_starts_with($item, 'permission:'));
        if (! $permission) {
            continue;
        }
        $permission = substr($permission, strlen('permission:'));
        foreach ($users as $user) {
            if (! $user->hasPermission($permission)) {
                $this->actingAs($user)->call($method, $url)->assertForbidden();
            }
        }
    }
});

it('allows each role to use its permitted routes with valid requests', function (): void {
    $users = [];
    foreach (['admin', 'cashier', 'staff'] as $key) {
        $role = Role::query()->create(['name' => 'Allowed '.$key, 'key' => $key]);
        $users[$key] = User::query()->create([
            'staff_code' => 'ALLOW-'.$key, 'role_id' => $role->id, 'full_name' => 'Allowed '.$key,
            'username' => 'allow-'.$key, 'password' => 'password123', 'is_active' => true,
        ]);
    }
    $categoryId = DB::table('categories')->insertGetId(['name' => 'Allowed category', 'category_group' => 'food']);
    $productId = DB::table('products')->insertGetId(['name' => 'Allowed product', 'category_id' => $categoryId, 'price' => 1, 'stock_quantity' => 5]);
    $customerId = DB::table('customers')->insertGetId(['customer_code' => 'ALLOW', 'full_name' => 'Allowed customer']);
    $saleId = DB::table('sales')->insertGetId([
        'transaction_code' => 'ALLOW-SALE', 'customer_id' => $customerId, 'sold_by' => $users['cashier']->id,
        'subtotal' => 1, 'total_amount' => 1, 'payment_method' => 'cash', 'amount_received' => 1,
    ]);
    $parameters = ['product' => $productId, 'customer' => $customerId, 'user' => $users['staff']->id, 'sale' => $saleId];
    $payloads = [
        'pos.customers.search' => ['q' => 'Allowed'],
        'pos.cart.update' => ['quantity' => 1],
        'pos.cart.discount' => ['type' => 'fixed', 'value' => 0],
        'pos.checkout' => [
            'items' => [['product_id' => $productId, 'quantity' => 1]],
            'payment_method' => 'cash',
            'amount_received' => 1,
            'order_type' => 'dine_in',
        ],
        'products.store' => [
            'name' => 'New product',
            'category_id' => $categoryId,
            'price' => 1,
            'stock_quantity' => 0,
            'low_stock_threshold' => 1,
        ],
        'products.update' => [
            'name' => 'Updated product',
            'category_id' => $categoryId,
            'price' => 1,
            'low_stock_threshold' => 1,
        ],
        'products.stock' => ['quantity_change' => 1, 'note' => 'Count correction'],
        'customers.store' => ['full_name' => 'New customer'],
        'customers.update' => ['full_name' => 'Updated customer'],
        'staff.store' => ['full_name' => 'New staff', 'username' => 'new-matrix-staff', 'role' => 'staff', 'password' => 'password123'],
        'staff.update' => ['full_name' => 'Updated staff', 'username' => 'allow-staff', 'role' => 'staff'],
        'sales.void' => ['reason' => 'Order cancelled'],
        'reports.export' => ['type' => 'sales'],
    ];
    $publicRoutes = ['landing.index', 'login', 'login.store', 'health'];
    $frameworkRoutes = ['_boost/browser-logs', 'storage/{path}', 'up'];

    foreach (Route::getRoutes() as $route) {
        if (in_array($route->uri(), $frameworkRoutes, true) || in_array($route->getName(), $publicRoutes, true)) {
            continue;
        }
        $name = $route->getName();
        $permission = collect($route->gatherMiddleware())->first(fn (string $item): bool => str_starts_with($item, 'permission:'));
        $permission = $permission ? substr($permission, strlen('permission:')) : null;
        $routeParameters = array_intersect_key($parameters, array_flip($route->parameterNames()));
        foreach ($users as $user) {
            if ($permission && ! $user->hasPermission($permission)) {
                continue;
            }
            // Roll back each request so the next role starts with the same data
            DB::beginTransaction();
            try {
                $response = $this->actingAs($user)
                    ->withSession(['pos.cart' => [$productId => 1]])
                    ->call($route->methods()[0], route($name, $routeParameters), $payloads[$name] ?? []);
                expect($response->getStatusCode())->toBeIn([200, 302], "Unexpected status for {$name} and {$user->username}");
            } finally {
                DB::rollBack();
            }
        }
    }
});
