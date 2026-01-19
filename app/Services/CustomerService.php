<?php

namespace App\Services;

use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class CustomerService
{
    protected $customerRepo;
    protected $userRepo;

    public function __construct(
        CustomerRepositoryInterface $customerRepo,
        UserRepositoryInterface $userRepo
    ) {
        $this->customerRepo = $customerRepo;
        $this->userRepo = $userRepo;
    }

    public function getCustomersPaginated()
    {
        return $this->customerRepo->paginate();
    }

    public function getCustomerSelection()
    {
        return $this->customerRepo->getSelectable();
    }

    public function getCustomerById($id)
    {
        return $this->customerRepo->findById($id);
    }

    public function createCustomer(array $data)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepo->create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'full_name' => $data['full_name'],
                'is_active' => true,
            ]);

            $customer = $this->customerRepo->create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'],
                'customer_code' => $data['customer_code'] ?? 'CUST' . time(),
                'tax_code' => $data['tax_code'],
                'billing_phone' => $data['billing_phone'],
                'address' => $data['address'] ?? null,
            ]);

            DB::commit();
            return $customer;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateCustomer($id, array $data)
    {
        DB::beginTransaction();
        try {
            $customer = $this->customerRepo->findById($id);


            $this->userRepo->update($customer->user_id, [
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            $updatedCustomer = $this->customerRepo->update($id, [
                'company_name' => $data['company_name'],
                'tax_code' => $data['tax_code'],
                'billing_phone' => $data['billing_phone'],
                'address' => $data['address'],
            ]);

            DB::commit();
            return $updatedCustomer;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteCustomer($id)
    {
        return $this->customerRepo->delete($id);
    }
}
