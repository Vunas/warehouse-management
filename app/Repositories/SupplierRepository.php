<?php

namespace App\Repositories;

use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanSoftDelete;

class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    use CanRead, CanWrite, CanSoftDelete;

    public function getModel()
    {
        return Supplier::class;
    }
}