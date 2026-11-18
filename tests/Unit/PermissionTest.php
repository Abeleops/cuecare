<?php

use App\Enums\Permission;
use App\Models\Role;
use App\Models\User;

function userWithRoleKey(string $roleKey): User
{
    $user = new User;
    $user->setRelation('role', (new Role)->forceFill(['key' => $roleKey]));

    return $user;
}

it('grants every permission to administrators', function (): void {
    $administrator = userWithRoleKey('admin');

    foreach (Permission::cases() as $permission) {
        expect($administrator->hasPermission($permission->value))->toBeTrue();
    }
});

it('limits cashiers to POS and own sales', function (): void {
    $cashier = userWithRoleKey('cashier');

    expect($cashier->hasPermission('PosAccess'))->toBeTrue()
        ->and($cashier->hasPermission('SalesViewOwn'))->toBeTrue()
        ->and($cashier->hasPermission('DashboardView'))->toBeFalse()
        ->and($cashier->hasPermission('ReportsView'))->toBeFalse();
});

it('limits staff to products and inventory adjustments', function (): void {
    $staff = userWithRoleKey('staff');

    expect($staff->hasPermission('ProductsView'))->toBeTrue()
        ->and($staff->hasPermission('InventoryAdjust'))->toBeTrue()
        ->and($staff->hasPermission('ProductsManage'))->toBeFalse()
        ->and($staff->hasPermission('PosAccess'))->toBeFalse();
});
