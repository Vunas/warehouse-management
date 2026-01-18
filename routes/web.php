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

Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. AUTH (GUEST ONLY)

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});


// 3. AUTH ACTIONS (LOGOUT)

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| 4. ADMIN PANEL
|--------------------------------------------------------------------------
| URL: /admin/*
| Middleware: auth + active_employee
*/
Route::prefix('admin')
    ->middleware(['auth', 'active_employee'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])
            ->name('admin.dashboard');


        // IAM MODULE

        Route::resource('employees', EmployeeController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('customers', CustomerController::class);


        // CORE WAREHOUSE

        Route::resource('warehouses', WarehouseController::class);
        Route::resource('products', ProductController::class);


        // CONTRACT

        Route::resource('contracts', ContractController::class);


        // INBOUND

        Route::post(
            'inbound-tickets/{inbound_ticket}/approve',
            [InboundTicketController::class, 'approve']
        )->name('inbound_tickets.approve');

        Route::post(
            'inbound-tickets/{inbound_ticket}/process',
            [InboundTicketController::class, 'process']
        )->name('inbound_tickets.process');

        Route::resource('inbound_tickets', InboundTicketController::class);


        // OUTBOUND

        Route::post(
            'outbound-tickets/{outbound_ticket}/process',
            [OutboundTicketController::class, 'process']
        )->name('outbound_tickets.process');

        Route::resource('outbound_tickets', OutboundTicketController::class);


        // INTERNAL TRANSFER

        Route::post(
            'transfers/{internal_transfer}/complete',
            [InternalTransferController::class, 'complete']
        )->name('transfers.complete');

        Route::resource('transfers', InternalTransferController::class);


        // INVENTORY (READ ONLY)

        Route::get('inventory', [InventoryController::class, 'index'])
            ->name('inventory.index');
    });
