<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanSoftDelete;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    use CanRead, CanWrite, CanSoftDelete;

    public function getModel()
    {
        return Category::class;
    }
}