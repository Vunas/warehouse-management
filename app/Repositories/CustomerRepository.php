<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;

class CustomerRepository implements CustomerRepositoryInterface
{
    protected $model;

    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    public function getAllPaginated($perPage = 10)
    {
        return $this->model->with('user')->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->model->with(['user', 'contracts'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $customer = $this->findById($id);
        $customer->update($data);
        return $customer;
    }

    public function delete($id)
    {
        $customer = $this->findById($id);
        $customer->user()->delete(); 
        return $customer->delete();  
    }
}