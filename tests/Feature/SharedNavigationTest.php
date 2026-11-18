<?php

use App\Models\Role;
use App\Models\User;

it('shows the signed-in cashier and only permitted navigation', function (): void {
    $user = (new User)->forceFill(['id' => 1, 'full_name' => 'Alex Cashier', 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'cashier', 'name' => 'Cashier']));
    $this->actingAs($user);
    $this->view('partials.profile')->assertSee('Alex Cashier')->assertSee('Cashier');
    $this->view('partials.navigation')
        ->assertSee('Record Sale')
        ->assertSee('Sales History')
        ->assertDontSee('Products')
        ->assertDontSee('Reports')
        ->assertDontSee('Staff');
});
