<?php

namespace App\Services;

use App\Repositories\Interfaces\InventoryTransactionRepositoryInterface;

class InventoryTransactionService
{
    protected $transactionRepo;

    public function __construct(InventoryTransactionRepositoryInterface $transactionRepo)
    {
        $this->transactionRepo = $transactionRepo;
    }

    public function getFilteredTransactions(array $filters, int $perPage = 20)
    {
        return $this->transactionRepo->filterAndPaginate($filters, $perPage);
    }
}