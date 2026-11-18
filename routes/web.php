<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

// Public routes.

Route::view('/', 'landing.index')->name('landing.index');

// Authentication.
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('login.store');

// Health check.
Route::get('/health', fn () => response()->json(['status' => 'ok']))
    ->name('health');

// Protected routes require authentication and an active account.

Route::middleware(['auth', 'active'])->group(function (): void {
    // Session.
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    // Dashboard.
    Route::view('/dashboard', 'dashboard.index')
        ->middleware('permission:DashboardView')
        ->name('dashboard');

    // POS.
    Route::view('/pos', 'pos.index')
        ->middleware('permission:PosAccess')
        ->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])
        ->middleware(['permission:PosAccess', 'throttle:30,1'])
        ->name('pos.checkout');

    // Products and inventory.
    Route::get('/products', [ProductController::class, 'index'])
        ->middleware('permission:ProductsView')
        ->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])
        ->middleware('permission:ProductsManage')
        ->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->middleware('permission:ProductsManage')
        ->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->middleware('permission:ProductsManage')
        ->name('products.destroy');
    Route::patch('/products/{product}/stock', [ProductController::class, 'adjustStock'])
        ->middleware('permission:InventoryAdjust')
        ->name('products.stock');

    // Customers.
    Route::get('/customers', [CustomerController::class, 'index'])
        ->middleware('permission:CustomersView')
        ->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])
        ->middleware('permission:CustomersManage')
        ->name('customers.store');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])
        ->middleware('permission:CustomersManage')
        ->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
        ->middleware('permission:CustomersManage')
        ->name('customers.destroy');

    // Staff.
    Route::get('/staff', [StaffController::class, 'index'])
        ->middleware('permission:StaffView')
        ->name('staff.index');
    Route::post('/staff', [StaffController::class, 'store'])
        ->middleware('permission:StaffManage')
        ->name('staff.store');
    Route::put('/staff/{user}', [StaffController::class, 'update'])
        ->middleware('permission:StaffManage')
        ->name('staff.update');
    Route::delete('/staff/{user}', [StaffController::class, 'destroy'])
        ->middleware('permission:StaffManage')
        ->name('staff.destroy');

    // Sales.
    Route::get('/sales', [SaleController::class, 'index'])
        ->middleware('permission:SalesViewOwn')
        ->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])
        ->middleware('permission:SalesViewOwn')
        ->name('sales.show');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])
        ->middleware('permission:SalesViewOwn')
        ->name('sales.receipt');
    Route::post('/sales/{sale}/void', [SaleController::class, 'void'])
        ->middleware('permission:SalesVoid')
        ->name('sales.void');

    // Reports and exports.
    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('permission:ReportsView')
        ->name('reports.index');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])
        ->middleware('permission:ReportsView')
        ->name('reports.inventory');
    Route::get('/reports/export', [ReportController::class, 'export'])
        ->middleware(['permission:ReportsExport', 'throttle:10,1'])
        ->name('reports.export');
});
