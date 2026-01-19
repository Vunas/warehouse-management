<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Import đầy đủ Interfaces
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\ContractRepositoryInterface;
use App\Repositories\Interfaces\InboundTicketRepositoryInterface;
use App\Repositories\Interfaces\OutboundTicketRepositoryInterface;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\SizeConversionRuleRepositoryInterface;

// Import đầy đủ Implementations (Repository Class)
use App\Repositories\UserRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\RoleRepository;
use App\Repositories\ContractRepository;
use App\Repositories\InboundTicketRepository;
use App\Repositories\OutboundTicketRepository;
use App\Repositories\InventoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\WarehouseRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SizeConversionRuleRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 1. Nhóm Auth & Nhân sự
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);

        // 2. Nhóm Sản phẩm & Cấu hình
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SizeConversionRuleRepositoryInterface::class, SizeConversionRuleRepository::class);

        // 3. Nhóm Kho & Hợp đồng
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
        $this->app->bind(ContractRepositoryInterface::class, ContractRepository::class);

        // 4. Nhóm Tồn kho & Vận hành (Fix lỗi InboundTicketRepositoryInterface)
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(InboundTicketRepositoryInterface::class, InboundTicketRepository::class);
        $this->app->bind(OutboundTicketRepositoryInterface::class, OutboundTicketRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}