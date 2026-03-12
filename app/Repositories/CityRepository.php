<?php

namespace App\Repositories;

use App\Models\City;
use App\Repositories\Interfaces\CityRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class CityRepository extends BaseRepository implements CityRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return City::class;
    }
}