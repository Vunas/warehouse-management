<?php

namespace App\Providers;

use App\Http\Controllers\ContractController;
use App\Repositories\CustomerRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\InboundTicketRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\InventoryRepository;
use App\Repositories\OutboundTicketRepository;
use App\Repositories\ProductRepository;
use App\Repositories\RoleRepository;
use App\Repositories\WarehouseRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            EmployeeRepositoryInterface::class,
            EmployeeRepository::class,
            CustomerRepositoryInterface::class,
            CustomerRepository::class,
            RoleRepositoryInterface::class,
            RoleRepository::class,
            ContractController::class,
            InboundTicketRepository::class,
            OutboundTicketRepository::class,
            InventoryRepository::class,
            ProductRepository::class,
            WarehouseRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
