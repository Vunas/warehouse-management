<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function paginate($perPage = 10);
    public function findById($id);
    public function findByEmail($email);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}