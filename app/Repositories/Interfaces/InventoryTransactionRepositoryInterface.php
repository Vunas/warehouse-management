<?php

namespace App\Repositories\Interfaces;

interface InventoryTransactionRepositoryInterface
{
    public function filterAndPaginate(array $filters, int $perPage = 20);
    public function create(array $data);
}