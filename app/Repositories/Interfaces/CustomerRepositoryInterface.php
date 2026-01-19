<?php

namespace App\Repositories\Interfaces;

interface CustomerRepositoryInterface
{
    // Cơ bản
    public function paginate($perPage = 10);
    public function findById($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getSelectable();
    public function findByUserId($userId);
}