<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return Permission::class;
    }
}