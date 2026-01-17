<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class CustomerService
{
    protected $customerRepo;

    public function __construct(CustomerRepository $customerRepo)
    {
        $this->customerRepo = $customerRepo;
    }

    public function getAllCustomers()
    {
        return $this->customerRepo->getAllPaginated();
    }

    public function getCustomerById($id)
    {
        return $this->customerRepo->findById($id);
    }

    public function createCustomer(array $data)
    {
        DB::beginTransaction();
        try {
            // 1. Tạo User (Tài khoản đăng nhập)
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'full_name' => $data['full_name'], // Tên người đại diện
                'is_active' => true,
            ]);

            // 2. Tạo Hồ sơ Khách hàng
            $customerData = [
                'user_id' => $user->id,
                'company_name' => $data['company_name'],
                'tax_code' => $data['tax_code'],
                'billing_phone' => $data['billing_phone'],
                'address' => $data['address'] ?? null,
            ];
            
            $customer = $this->customerRepo->create($customerData);

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

            // Update User Info
            $customer->user->update([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'] ?? $customer->user->is_active,
            ]);

            // Update Customer Info
            $this->customerRepo->update($id, [
                'company_name' => $data['company_name'],
                'tax_code' => $data['tax_code'],
                'billing_phone' => $data['billing_phone'],
                'address' => $data['address'],
            ]);

            DB::commit();
            return $customer;
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