<?php

namespace App\Repositories\Traits;

trait CanRead
{
    public function all(array $columns = ['*'], array $relations = [])
    {
        return $this->model->with($relations)->get($columns);
    }

    public function findById($id, array $columns = ['*'], array $relations = [])
    {
        return $this->model->with($relations)->findOrFail($id, $columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = [])
    {
        return $this->model
            ->with($relations)
            ->orderBy('id', 'desc')
            ->paginate($perPage, $columns);
    }
}
