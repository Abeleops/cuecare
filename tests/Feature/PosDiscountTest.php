<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

it('rejects discounts from cashiers', function (): void {
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'cashier']));
    $this->actingAs($user)->putJson(route('pos.cart.discount'), ['type' => 'fixed', 'value' => 10])->assertForbidden();
});

it('rejects percentages above 100 for administrators', function (): void {
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'admin']));
    $this->actingAs($user)
        ->putJson(route('pos.cart.discount'), ['type' => 'percentage', 'value' => 101])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('value');
});
