<?php

namespace App\Repositories;

use App\Models\StockTransfer;
use App\Repositories\Interfaces\StockTransferRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class StockTransferRepository extends BaseRepository implements StockTransferRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return StockTransfer::class;
    }
}
