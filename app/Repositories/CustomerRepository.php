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

    public function paginate($perPage = 10)
    {
        return $this->model->with('user')->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->model->with(['user', 'contracts'])->findOrFail($id);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        $customer = $this->findById($id);
        $customer->update($data);
        return $customer;
    }

    public function delete($id)
    {
        $customer = $this->findById($id);
        // Xóa user liên quan nếu là tài khoản portal
        if ($customer->user) {
            $customer->user()->delete(); 
        }
        return $customer->delete();
    }

    public function getSelectable()
    {
        return $this->model->select('id', 'company_name','tax_code','user_id')
        ->with('user:id,full_name,email')
        ->get();
    }

    public function findByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->first();
    }
}