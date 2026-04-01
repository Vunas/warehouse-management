<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InboundOrderController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OutboundOrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\CustomerCartController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerAddressController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC & AUTH
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('customer_login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    // Admin login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Customer login
    Route::get('/customer/login', [AuthController::class, 'showCustomerLoginForm'])->name('customer_login');
    Route::post('/customer/login', [AuthController::class, 'login'])->name('customer_login.post');
});

// Logout routes
Route::post('/logout', function(Request $request) {
    return app(AuthController::class)->logout($request, 'web');
})->name('logout')->middleware('auth:web');

Route::post('/customer/logout', function(Request $request) {
    return app(AuthController::class)->logout($request, 'customer');
})->name('customer.logout')->middleware('auth:customer');

/*
|--------------------------------------------------------------------------
| 2. ADMIN MODULES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->group(function () {
    
    // Tổng quan (Dashboard)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Hệ thống & Người dùng
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);

    // Master Data (Danh mục lõi)
    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('products', ProductController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('locations', LocationController::class);

    // Tồn kho (Inventory)
    Route::resource('inventory', InventoryController::class);

    // Nhập kho (Inbound)
    Route::resource('inbounds', InboundOrderController::class)->except(['edit']);
    Route::post('inbounds/{inbound}/items', [InboundOrderController::class, 'addItem'])->name('inbounds.addItem');
    Route::put('inbounds/{inbound}/items/{item}', [InboundOrderController::class, 'updateItem'])->name('inbounds.items.update');
    Route::delete('inbounds/{inbound}/items/{item}', [InboundOrderController::class, 'removeItem'])->name('inbounds.removeItem');
    Route::post('inbounds/{inbound}/complete', [InboundOrderController::class, 'complete'])->name('inbounds.complete');
    Route::post('inbounds/{inbound}/cancel', [InboundOrderController::class, 'cancel'])->name('inbounds.cancel');

    // Luân chuyển kho (Stock Transfer)
    Route::resource('transfers', StockTransferController::class);
    Route::post('transfers/{transfer}/items', [StockTransferController::class, 'addItem'])->name('transfers.items.add');
    Route::put('transfers/{transfer}/items/{item}', [StockTransferController::class, 'updateItem'])->name('transfers.items.update');
    Route::delete('transfers/{transfer}/items/{item}', [StockTransferController::class, 'removeItem'])->name('transfers.items.remove');
    Route::post('transfers/{transfer}/complete', [StockTransferController::class, 'complete'])->name('transfers.complete');
    Route::post('transfers/{transfer}/cancel', [StockTransferController::class, 'cancel'])->name('transfers.cancel');

    // Xuất kho (Outbound)
    Route::resource('outbounds', OutboundOrderController::class)->except(['edit', 'update']);
    Route::post('outbounds/{outbound}/complete', [OutboundOrderController::class, 'complete'])->name('outbounds.complete');
    Route::post('outbounds/{outbound}/cancel', [OutboundOrderController::class, 'cancel'])->name('outbounds.cancel');

    // Tài chính & Khách hàng
    Route::resource('payments', PaymentController::class)->except(['create', 'store', 'destroy']);
    Route::resource('carts', CartItemController::class)->only(['index']);

    Route::resource('orders', OrderController::class)->only(['index']);
});


Route::middleware(['auth:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/overview', [DashboardController::class, 'overview'])->name('overview');
    Route::get('/dashboard', [DashboardController::class, 'customerIndex'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [CustomerProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [CustomerProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::delete('/profile', [CustomerProfileController::class, 'deleteAccount'])->name('profile.delete');
    
    // Address Management
    Route::resource('address', CustomerAddressController::class)->except(['show']);
    
    // Address API routes
    Route::get('address-api/districts/{cityId}', [CustomerAddressController::class, 'getDistricts'])->name('address.api.districts');
    Route::get('address-api/wards/{districtId}', [CustomerAddressController::class, 'getWards'])->name('address.api.wards');
    
    // Cart Management
    Route::get('/cart', [CustomerCartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CustomerCartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{cartItem}', [CustomerCartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CustomerCartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CustomerCartController::class, 'checkout'])->name('cart.checkout');
    
    // Order Management
    Route::get('/order/{order}', [CustomerOrderController::class, 'show'])->name('order.show');
    Route::post('/order/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('order.cancel');
});