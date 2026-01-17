<?php

namespace App\Repositories\Interfaces;

interface CustomerRepositoryInterface
{
    public function getAllPaginated($perPage = 10);
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}