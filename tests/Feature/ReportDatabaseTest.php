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
        'full_name' => 'Admin',
        'username' => 'test-admin',
        'password' => 'password123',
        'is_active' => true,
    ]);
    $this->actingAs($this->admin);
    $sales = [
        ['CASH1', 'cash', 100, 'completed'],
        ['GCASH1', 'gcash', 50, 'completed'],
        ['VOID1', 'cash', 500, 'voided'],
    ];
    foreach ($sales as [$code, $method, $amount, $status]) {
        DB::table('sales')->insert([
            'transaction_code' => $code,
            'sold_by' => $this->admin->id,
            'subtotal' => $amount,
            'total_amount' => $amount,
            'payment_method' => $method,
            'gcash_reference' => $method === 'gcash' ? 'ABC123' : null,
            'amount_received' => $amount,
            'status' => $status,
            'completed_at' => now(),
        ]);
    }
});

it('reports completed sales and payment filters with a 30 day limit', function (): void {
    $this->get(route('reports.index'))
        ->assertOk()
        ->assertViewHas('salesTotal', 150.0)
        ->assertViewHas('transactionCount', 2)
        ->assertViewHas('averageSale', 75.0);
    $this->get(route('reports.index', ['payment' => 'cash']))->assertOk()->assertViewHas('salesTotal', 100.0);
    $this->get(route('reports.index', ['payment' => 'gcash']))->assertOk()->assertViewHas('salesTotal', 50.0);
    $this->getJson(route('reports.index', ['payment' => 'card']))->assertUnprocessable()->assertJsonValidationErrors('payment');
    $this->getJson(route('reports.index', ['start_date' => '2026-01-31', 'end_date' => '2026-01-01']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('end_date');
    $this->getJson(route('reports.index', ['start_date' => '2026-01-01', 'end_date' => '2026-01-31']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('end_date');
    $this->get(route('reports.index', ['start_date' => '2020-01-01', 'end_date' => '2020-01-30']))->assertOk()->assertViewHas('averageSale', 0);
});

it('shows split sales in all reports but excludes them from individual payment filters', function (): void {
    DB::table('sales')->insert([
        'transaction_code' => 'SPLIT1',
        'sold_by' => $this->admin->id,
        'subtotal' => 100,
        'total_amount' => 100,
        'payment_method' => 'split',
        'cash_amount' => 40,
        'gcash_amount' => 60,
        'gcash_reference' => 'ABC123',
        'amount_received' => 100,
        'status' => 'completed',
        'completed_at' => now(),
    ]);
    $this->get(route('reports.index'))->assertOk()->assertViewHas('salesTotal', 250.0)->assertSee('Cash + GCash: ₱100.00');
    $this->get(route('reports.index', ['payment' => 'cash']))->assertOk()->assertViewHas('salesTotal', 100.0);
    $this->get(route('reports.index', ['payment' => 'gcash']))->assertOk()->assertViewHas('salesTotal', 50.0);
    $this->get(route('reports.export', ['type' => 'sales']))->assertOk()->assertSee('SPLIT1');
    $this->get(route('reports.export', ['type' => 'sales', 'payment' => 'cash']))->assertOk()->assertDontSee('SPLIT1');
    $this->get(route('reports.export', ['type' => 'sales', 'payment' => 'gcash']))->assertOk()->assertDontSee('SPLIT1');
});

it('exports only active inventory and escapes spreadsheet formulas', function (): void {
    $category = DB::table('categories')->insertGetId(['name' => 'Test category', 'category_group' => 'food']);
    DB::table('products')->insert([
        'name' => '=1+1',
        'category_id' => $category,
        'price' => 1,
        'stock_quantity' => 1,
        'low_stock_threshold' => 2,
        'is_active' => true,
    ]);
    DB::table('products')->insert([
        'name' => 'Inactive product',
        'category_id' => $category,
        'price' => 1,
        'stock_quantity' => 1,
        'low_stock_threshold' => 2,
        'is_active' => false,
    ]);
    $this->get(route('reports.inventory'))->assertOk()->assertSee('=1+1')->assertDontSee('Inactive product');
    $this->get(route('reports.export', ['type' => 'inventory']))->assertOk()->assertSee("'=1+1", false)->assertDontSee('Inactive product');
});

it('restricts report exports to administrators and rate limits repeated exports', function (): void {
    foreach (['cashier', 'staff'] as $key) {
        $role = Role::query()->create(['name' => 'Test '.$key, 'key' => $key]);
        $user = User::query()->create([
            'staff_code' => 'TEST-'.$key,
            'role_id' => $role->id,
            'full_name' => 'Test '.$key,
            'username' => 'test-'.$key,
            'password' => 'password123',
            'is_active' => true,
        ]);
        $this->actingAs($user)->get(route('reports.export', ['type' => 'sales']))->assertForbidden();
    }
    $this->actingAs($this->admin);
    for ($attempt = 0; $attempt < 10; $attempt++) {
        $this->get(route('reports.export', ['type' => 'sales']))->assertOk();
    }
    $this->get(route('reports.export', ['type' => 'sales']))->assertTooManyRequests();
});

it('exports sales through the dedicated route with the same filters and permissions', function (): void {
    $filters = ['payment' => 'cash'];
    $report = $this->get(route('reports.export', ['type' => 'sales', ...$filters]));
    $export = $this->get(route('sales.export', $filters));

    $report->assertOk();
    $export->assertOk()->assertSee('CASH1')->assertDontSee('GCASH1')->assertDontSee('VOID1');
    expect($export->getContent())->toBe($report->getContent());

    foreach (['cashier', 'staff'] as $key) {
        $role = Role::query()->create(['name' => 'Export '.$key, 'key' => $key]);
        $user = User::query()->create([
            'staff_code' => 'EXPORT-'.$key,
            'role_id' => $role->id,
            'full_name' => 'Export '.$key,
            'username' => 'export-'.$key,
            'password' => 'password123',
            'is_active' => true,
        ]);
        $this->actingAs($user)->get(route('sales.export'))->assertForbidden();
    }
});
