<?php

namespace App\Repositories\Traits;

trait CanWrite
{
    public function create(array $payload)
    {
        return $this->model->create($payload);
    }

    public function update($id, array $payload)
    {
        $model = $this->findById($id);
        $model->update($payload);
        return $model;
    }
}