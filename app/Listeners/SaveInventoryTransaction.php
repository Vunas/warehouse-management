<?php

namespace App\Listeners;

use App\Events\InventoryTransactionRecorded;
use App\Repositories\Interfaces\InventoryTransactionRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;

class SaveInventoryTransaction implements ShouldQueue
{
    protected $transactionRepo;

    public function __construct(InventoryTransactionRepositoryInterface $transactionRepo)
    {
        $this->transactionRepo = $transactionRepo;
    }

    public function handle(InventoryTransactionRecorded $event)
    {
        $this->transactionRepo->create($event->transactionData);
    }
}
