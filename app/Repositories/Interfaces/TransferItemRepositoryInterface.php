<?php

namespace App\Repositories\Interfaces;

interface TransferItemRepositoryInterface
{
    public function create(array $payload);
    public function update($id, array $payload);
    public function delete($id);
    public function findById($id, array $columns = ['*'], array $relations = []);
}