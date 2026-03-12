<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanSoftDelete;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    use CanRead, CanWrite, CanSoftDelete;

    public function getModel()
    {
        return User::class;
    }
}