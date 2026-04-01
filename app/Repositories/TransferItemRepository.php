<?php

namespace App\Repositories;

use App\Models\TransferItem;
use App\Repositories\Interfaces\TransferItemRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class TransferItemRepository implements TransferItemRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    protected $model;

    public function __construct(TransferItem $model)
    {
        $this->model = $model;
    }
}