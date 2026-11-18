<?php

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

beforeEach(function (): void {
    $role = Role::query()->create(['name' => 'Test admin', 'key' => 'admin']);
    $this->user = User::query()->create([
        'staff_code' => 'TEST-ADMIN',
        'role_id' => $role->id,
        'full_name' => 'Test Admin',
        'username' => 'test-admin',
        'password' => 'password',
        'is_active' => true,
    ]);
    $this->actingAs($this->user);
});

it('creates and updates customers with nullable contact details and audits changes', function (): void {
    $this->postJson(route('customers.store'), ['full_name' => 'Test customer'])->assertRedirect();
    $customer = Customer::query()->where('full_name', 'Test customer')->firstOrFail();
    expect($customer->phone)->toBeNull()->and($customer->email)->toBeNull();
    $this->putJson(route('customers.update', $customer), ['full_name' => 'Test customer', 'phone' => '  ', 'email' => '  '])->assertRedirect();
    expect($customer->fresh()->phone)->toBeNull()->and($customer->fresh()->email)->toBeNull();
    $this->deleteJson(route('customers.destroy', $customer))->assertRedirect();
    $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    foreach (['customer_created', 'customer_updated', 'customer_deleted'] as $action) {
        $this->assertDatabaseHas('audit_logs', ['action' => $action, 'subject_id' => $customer->id]);
    }
});

it('enforces active email uniqueness but permits reuse after soft deletion', function (): void {
    $this->postJson(route('customers.store'), ['full_name' => 'First customer', 'email' => 'reuse@example.com'])->assertRedirect();
    $customer = Customer::query()->where('email', 'reuse@example.com')->firstOrFail();
    $this->postJson(route('customers.store'), ['full_name' => 'Second customer', 'email' => 'reuse@example.com'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
    $this->putJson(route('customers.update', $customer), ['full_name' => 'First customer', 'email' => ' reuse@example.com '])->assertRedirect();
    $this->deleteJson(route('customers.destroy', $customer))->assertRedirect();
    $this->postJson(route('customers.store'), ['full_name' => 'Second customer', 'email' => 'reuse@example.com'])->assertRedirect();
    $this->assertDatabaseHas('customers', ['full_name' => 'Second customer', 'email' => 'reuse@example.com', 'deleted_at' => null]);
});

it('forbids cashier and staff customer writes', function (): void {
    $customer = Customer::query()->create(['customer_code' => 'TEST-CUSTOMER', 'full_name' => 'Test customer']);
    foreach (['cashier', 'staff'] as $key) {
        $role = Role::query()->create(['name' => 'Test '.$key, 'key' => $key]);
        $this->user->update(['role_id' => $role->id]);
        $this->user->unsetRelation('role');
        $this->postJson(route('customers.store'), ['full_name' => 'New customer'])->assertForbidden();
        $this->putJson(route('customers.update', $customer), ['full_name' => 'New name'])->assertForbidden();
        $this->deleteJson(route('customers.destroy', $customer))->assertForbidden();
    }
});

it('hides deleted customers from POS suggestions while retaining their name in sales history', function (): void {
    $customer = Customer::query()->create([
        'customer_code' => 'TEST-CUSTOMER',
        'full_name' => 'Historical customer',
        'phone' => '09171234567',
        'email' => 'historic@example.test',
    ]);
    $saleId = DB::table('sales')->insertGetId([
        'transaction_code' => 'TRX-HISTORY',
        'customer_id' => $customer->id,
        'sold_by' => $this->user->id,
        'subtotal' => 100,
        'total_amount' => 100,
        'payment_method' => 'cash',
        'amount_received' => 100,
        'status' => 'completed',
        'completed_at' => now(),
    ]);
    $this->putJson(route('customers.update', $customer), [
        'full_name' => 'Historical customer',
        'phone' => '09171234567',
        'email' => 'historic@example.test',
    ])->assertRedirect();
    $this->deleteJson(route('customers.destroy', $customer))->assertRedirect();
    $this->getJson(route('pos.customers.search', ['q' => 'Historical']))->assertOk()->assertExactJson([]);
    $this->get(route('sales.show', $saleId))->assertOk()->assertSee('Historical customer');
    $metadata = DB::table('audit_logs')
        ->where('subject_id', $customer->id)
        ->whereIn('action', ['customer_created', 'customer_updated', 'customer_deleted'])
        ->pluck('metadata')
        ->implode(' ');
    expect($metadata)->not->toContain('09171234567')->not->toContain('historic@example.test');
});
