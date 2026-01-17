<?php

use Illuminate\Support\Facades\Route;
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

// 1. Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// 2. Authenticated Routes
Route::middleware(['auth', 'active_employee'])->group(function () {
    
    // Auth Actions
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // === IAM MODULE ===
    Route::resource('employees', EmployeeController::class);
    // Chỉ Admin mới can thiệp Role (đã check trong Controller nhưng thêm middleware càng tốt)
    Route::resource('roles', RoleController::class); 
    Route::resource('customers', CustomerController::class);

    // === CORE WAREHOUSE MODULE ===
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('products', ProductController::class);

    // === CONTRACT MODULE ===
    Route::resource('contracts', ContractController::class);

    // === INBOUND MODULE ===
    // Custom actions cho quy trình nhập kho
    Route::post('inbound-tickets/{inbound_ticket}/approve', [InboundTicketController::class, 'approve'])->name('inbound_tickets.approve');
    Route::post('inbound-tickets/{inbound_ticket}/process', [InboundTicketController::class, 'process'])->name('inbound_tickets.process');
    Route::resource('inbound_tickets', InboundTicketController::class);

    // === OUTBOUND MODULE ===
    Route::post('outbound-tickets/{outbound_ticket}/process', [OutboundTicketController::class, 'process'])->name('outbound_tickets.process');
    Route::resource('outbound_tickets', OutboundTicketController::class);

    // === INVENTORY & TRANSFER ===
    Route::post('transfers/{internal_transfer}/complete', [InternalTransferController::class, 'complete'])->name('transfers.complete');
    Route::resource('transfers', InternalTransferController::class);
    
    // Báo cáo tồn kho (Read-only)
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
});