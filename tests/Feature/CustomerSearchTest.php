<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

it('filters customers by phone and displays the matching count', function (): void {
    DB::table('customers')->insert([
        ['customer_code' => 'CUST-001', 'full_name' => 'Ada Test', 'phone' => '09171234567', 'email' => null],
        ['customer_code' => 'CUST-002', 'full_name' => 'Bob Test', 'phone' => '09990000000', 'email' => null],
    ]);
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'admin']));
    $this->actingAs($user)->get(route('customers.index', ['search' => '0917']))
        ->assertOk()->assertSee('Ada Test')->assertDontSee('Bob Test')->assertSee('Showing 1 to 1 of 1 customers');
});
