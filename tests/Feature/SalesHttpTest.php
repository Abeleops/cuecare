<?php

use App\Models\Role;
use App\Models\Sale;
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
    $category = DB::table('categories')->insertGetId(['name' => 'Test category', 'category_group' => 'food']);
    $this->product = DB::table('products')->insertGetId([
        'name' => 'Test product',
        'category_id' => $category,
        'price' => 100,
        'stock_quantity' => 3,
        'low_stock_threshold' => 1,
    ]);
    $this->payload = [
        'items' => [['product_id' => $this->product, 'quantity' => 1]],
        'payment_method' => 'gcash',
        'amount_received' => 100,
        'gcash_reference' => 'REF12345',
        'order_type' => 'dine_in',
    ];
    $this->actingAs($this->admin)->withSession(['pos.cart' => [$this->product => 1]]);
});

it('processes a GCash checkout through the POS endpoint and masks the audit reference', function (): void {
    $this->postJson(route('pos.checkout'), $this->payload)->assertRedirect(route('pos.index'));
    $sale = Sale::query()->firstOrFail();
    expect($sale->payment_method)->toBe('gcash')
        ->and($sale->gcash_reference)->toBe('REF12345')
        ->and($sale->transaction_code)->toMatch('/^TRX-\d{6,}$/');
    $this->assertDatabaseHas('sales', ['id' => $sale->id, 'total_amount' => 100]);
    $metadata = DB::table('audit_logs')->where('action', 'sale_created')->value('metadata');
    expect($metadata)->toContain('2345')->not->toContain('REF12345');
});

it('rejects invalid payments and stock without partial writes', function (): void {
    $invalidPayments = [
        ['gcash_reference' => null],
        ['gcash_reference' => 'bad!'],
        ['gcash_reference' => str_repeat('A', 51)],
        ['amount_received' => 101],
        ['payment_method' => 'card'],
        ['payment_method' => 'cash', 'amount_received' => 99],
    ];

    foreach ($invalidPayments as $invalid) {
        $this->postJson(route('pos.checkout'), array_replace($this->payload, $invalid))->assertUnprocessable();
    }
    $this->withSession(['pos.cart' => [$this->product => 4]])->postJson(route('pos.checkout'), $this->payload)->assertUnprocessable();
    $this->assertDatabaseCount('sales', 0);
    $this->assertDatabaseCount('sale_items', 0);
    $this->assertDatabaseHas('products', ['id' => $this->product, 'stock_quantity' => 3]);
});

it('stores both split payment amounts and requires their sum to equal the total', function (): void {
    $split = array_replace($this->payload, [
        'payment_method' => 'split',
        'amount_received' => null,
        'cash_amount' => 40,
        'gcash_amount' => 59,
    ]);
    $this->postJson(route('pos.checkout'), $split)->assertUnprocessable()->assertJsonValidationErrors('cash_amount');
    $this->assertDatabaseCount('sales', 0);
    $this->postJson(route('pos.checkout'), array_replace($split, ['gcash_amount' => 60]))->assertRedirect();
    $this->assertDatabaseHas('sales', [
        'payment_method' => 'split',
        'cash_amount' => 40,
        'gcash_amount' => 60,
        'amount_received' => 100,
        'gcash_reference' => 'REF12345',
    ]);
});

it('restores stock once when a completed sale is voided', function (): void {
    $this->postJson(route('pos.checkout'), array_replace($this->payload, ['payment_method' => 'cash', 'amount_received' => 150]))->assertRedirect();
    $sale = Sale::query()->firstOrFail();
    expect((float) $sale->change_amount)->toBe(50.0);
    $this->postJson(route('sales.void', $sale), [])->assertUnprocessable();
    $this->postJson(route('sales.void', $sale), ['reason' => 'Order cancelled'])->assertRedirect();
    $this->postJson(route('sales.void', $sale), ['reason' => 'Again'])->assertUnprocessable();
    $this->assertDatabaseHas('sales', ['id' => $sale->id, 'status' => 'voided', 'void_reason' => 'Order cancelled']);
    $this->assertDatabaseHas('products', ['id' => $this->product, 'stock_quantity' => 3]);
    $this->assertDatabaseHas('audit_logs', ['action' => 'sale_voided', 'subject_id' => $sale->id]);
    $this->get(route('sales.index'))->assertOk()->assertSee('Voided');
    $this->get(route('sales.receipt', $sale))->assertOk()->assertSee('Voided');
    $this->get(route('dashboard'))->assertOk()->assertViewHas('salesTotal', fn ($total) => (float) $total === 0.0);
});

it('forbids a cashier from voiding their own sale', function (): void {
    $cashierRole = Role::query()->create(['name' => 'Test cashier', 'key' => 'cashier']);
    $this->admin->update(['role_id' => $cashierRole->id]);
    $this->admin->unsetRelation('role');
    $this->postJson(route('pos.checkout'), $this->payload)->assertRedirect();
    $sale = Sale::query()->firstOrFail();
    $this->postJson(route('sales.void', $sale), ['reason' => 'Mistake'])->assertForbidden();
    $this->assertDatabaseHas('sales', ['id' => $sale->id, 'status' => 'completed']);
});
