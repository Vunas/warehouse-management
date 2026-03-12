<?php

namespace App\Providers;

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
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 1. Nhóm Auth & Nhân sự
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);

        // 2. Nhóm Sản phẩm & Cấu hình
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);

        // 3. Nhóm Kho & Hợp đồng
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);

        // 4. Nhóm Tồn kho & Vận hành (Fix lỗi InboundTicketRepositoryInterface)
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
    }

    // AuthServiceProvider.php
    protected $policies = [
    ];


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('inbound.approve', function ($user) {
            return $user->employee && $user->employee->hasPermission('inbound.approve');
        });
    }
}
