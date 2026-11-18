<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing.index')->name('landing.index');
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
Route::get('/health', fn () => response()->json(['status' => 'ok']))->name('health');

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::view('/dashboard', 'dashboard.index')->middleware('permission:DashboardView')->name('dashboard');
    Route::view('/pos', 'pos.index')->middleware('permission:PosAccess')->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->middleware('permission:PosAccess')->name('pos.checkout');
    Route::get('/products', [ProductController::class, 'index'])->middleware('permission:ProductsView')->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->middleware('permission:ProductsManage')->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('permission:ProductsManage')->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('permission:ProductsManage')->name('products.destroy');
    Route::patch('/products/{product}/stock', [ProductController::class, 'adjustStock'])->middleware('permission:InventoryAdjust')->name('products.stock');
    Route::view('/customers', 'customers.index')->middleware('permission:CustomersView')->name('customers.index');
    Route::view('/staff', 'staff.index')->middleware('permission:StaffView')->name('staff.index');
    Route::view('/sales', 'sales.index')->middleware('permission:SalesViewOwn')->name('sales.index');
    Route::view('/reports', 'reports.index')->middleware('permission:ReportsView')->name('reports.index');
    Route::view('/reports/inventory', 'reports.inventory')->middleware('permission:ReportsView')->name('reports.inventory');
});
