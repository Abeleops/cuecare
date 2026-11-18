<?php

namespace App\Enums;

enum Permission: string
{
    case DashboardView = 'DashboardView';
    case PosAccess = 'PosAccess';
    case DiscountApply = 'DiscountApply';
    case ProductsView = 'ProductsView';
    case ProductsManage = 'ProductsManage';
    case InventoryAdjust = 'InventoryAdjust';
    case CustomersView = 'CustomersView';
    case CustomersManage = 'CustomersManage';
    case StaffView = 'StaffView';
    case StaffManage = 'StaffManage';
    case SalesViewAll = 'SalesViewAll';
    case SalesViewOwn = 'SalesViewOwn';
    case SalesVoid = 'SalesVoid';
    case ReportsView = 'ReportsView';
    case ReportsExport = 'ReportsExport';
}
