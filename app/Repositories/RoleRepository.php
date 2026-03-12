<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return Role::class;
    }
}