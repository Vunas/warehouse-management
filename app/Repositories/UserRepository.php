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

    public function paginate(
        int $perPage = 15,
        array $columns = ['*'],
        array $relations = [],
        array $filters = [],
        string $sort = 'id',
        string $dir = 'desc'
    ) {
        $query = $this->model->with($relations);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_active', $filters['status']);
        }

        $allowedSorts = ['id', 'username', 'full_name', 'email', 'is_active'];
        $sortColumn = in_array($sort, $allowedSorts) ? $sort : 'id';
        $sortDirection = strtolower($dir) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortColumn, $sortDirection);

        return $query->paginate($perPage, $columns)->withQueryString();
    }
}
