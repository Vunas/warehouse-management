<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InboundTicketController;
use App\Http\Controllers\OutboundTicketController;
use App\Http\Controllers\InternalTransferController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerDashboardController;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| 2. AUTH – GUEST
|--------------------------------------------------------------------------
*/
Route::middleware('redirect.login')->group(function () {

    // Employee login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Customer auth
    Route::get('/customer/login', [AuthController::class, 'showCustomerLogin']);
    Route::post('/customer/login', [AuthController::class, 'customerLogin']);

    Route::get('/customer/register', [AuthController::class, 'showRegisterForm']);
    Route::post('/customer/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| 3. AUTH ACTIONS
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| 4. ADMIN PANEL
|--------------------------------------------------------------------------
| URL: /admin/*
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'active_employee'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])
            ->name('admin.dashboard');

        // IAM
        Route::resource('employees', EmployeeController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('customers', CustomerController::class);

        // Warehouse core
        Route::resource('warehouses', WarehouseController::class);
        Route::resource('products', ProductController::class);

        // Contracts
        Route::resource('contracts', ContractController::class);

        // Inbound
        Route::post(
            'inbound-tickets/{inbound_ticket}/approve',
            [InboundTicketController::class, 'approve']
        )->name('inbound_tickets.approve');
        Route::post(
            'inbound-tickets/{inbound_ticket}/approve',
            [InboundTicketController::class, 'approve']
        )->name('inbound_tickets.reject');

        Route::post(
            'inbound-tickets/{inbound_ticket}/process',
            [InboundTicketController::class, 'process']
        )->name('inbound_tickets.process');

        Route::resource('inbound_tickets', InboundTicketController::class);

        // Outbound
        Route::post(
            'outbound-tickets/{outbound_ticket}/process',
            [OutboundTicketController::class, 'process']
        )->name('outbound_tickets.process');

        Route::resource('outbound_tickets', OutboundTicketController::class);

        // Internal transfer
        Route::post(
            'transfers/{internal_transfer}/complete',
            [InternalTransferController::class, 'complete']
        )->name('transfers.complete');

        Route::resource('transfers', InternalTransferController::class);

        // Inventory (read only)
        Route::get('inventory', [InventoryController::class, 'index'])
            ->name('inventory.index');
    });
/*
|--------------------------------------------------------------------------
| 4. CUSTOMER PANEL
|--------------------------------------------------------------------------
| URL: /Customer/*
|--------------------------------------------------------------------------
*/


Route::prefix('customer')
->middleware('active_customer')
->group(function(){

    Route::get('dashboard',
        [CustomerDashboardController::class, 'index']
    )->name('customer.dashboard');

});



