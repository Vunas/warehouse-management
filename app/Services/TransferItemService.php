<?php

namespace App\Services;

use App\Repositories\Interfaces\TransferItemRepositoryInterface;

class TransferItemService
{
    protected $itemRepo;

    public function __construct(TransferItemRepositoryInterface $itemRepo)
    {
        $this->itemRepo = $itemRepo;
    }

    public function addTransferItem(array $data)
    {
        return $this->itemRepo->create($data);
    }

    public function removeTransferItem($id)
    {
        return $this->itemRepo->delete($id);
    }
}