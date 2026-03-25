<?php

namespace App\Providers;

use App\Policies\RolePolicy;
use App\Repositories\BrandRepository;
use App\Repositories\CartItemRepository;
use Illuminate\Support\ServiceProvider;

// Import đầy đủ Interfaces
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

// Import đầy đủ Implementations (Repository Class)
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\InventoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\WarehouseRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\InboundItemRepository;
use App\Repositories\InboundOrderRepository;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use App\Repositories\Interfaces\CartItemRepositoryInterface;
use App\Repositories\Interfaces\InboundItemRepositoryInterface;
use App\Repositories\Interfaces\InboundOrderRepositoryInterface;
use App\Repositories\Interfaces\LocationRepositoryInterface;
use App\Repositories\Interfaces\OrderItemRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\StockTransferRepositoryInterface;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Repositories\Interfaces\TransferItemRepositoryInterface;
use App\Repositories\LocationRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\StockTransferRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\TransferItemRepository;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 1. Nhóm Auth & Nhân sự
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        // 2. Nhóm Sản phẩm & Cấu hình
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);

        // 3. Nhóm Kho & Hợp đồng
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);

        // 4. Nhóm Tồn kho & Vận hành (Fix lỗi InboundTicketRepositoryInterface)
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(InboundOrderRepositoryInterface::class, InboundOrderRepository::class);
        $this->app->bind(InboundItemRepositoryInterface::class, InboundItemRepository::class);
        $this->app->bind(StockTransferRepositoryInterface::class, StockTransferRepository::class);
        $this->app->bind(TransferItemRepositoryInterface::class, TransferItemRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);

        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(CartItemRepositoryInterface::class, CartItemRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }


    // AuthServiceProvider.php
    protected $policies = [];


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Role::class, RolePolicy::class);
        Gate::define('inbound.approve', function ($user) {
            return $user->employee && $user->employee->hasPermission('inbound.approve');
        });
    }
}
