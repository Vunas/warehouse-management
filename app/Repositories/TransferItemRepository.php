<?php

namespace App\Repositories;

use App\Models\TransferItem;
use App\Repositories\Interfaces\TransferItemRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class TransferItemRepository extends BaseRepository implements TransferItemRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return TransferItem::class;
    }

    public function getByTransferId(int $transferId)
    {
        return $this->model->with('inventory.product')
                           ->where('transfer_id', $transferId)
                           ->get();
    }
}