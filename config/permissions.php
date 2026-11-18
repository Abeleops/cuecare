<?php

use App\Enums\Permission;

return [
    'roles' => [
        'admin' => array_column(Permission::cases(), 'value'),
        'cashier' => [
            Permission::PosAccess->value,
            Permission::SalesViewOwn->value,
        ],
        'staff' => [
            Permission::ProductsView->value,
            Permission::InventoryAdjust->value,
        ],
    ],
];
