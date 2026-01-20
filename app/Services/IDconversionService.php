<?php

namespace App\Services;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;

class IDconversionService
{
    protected $customerRepo;
    protected $userRepo;
    protected $employeeRepo;

    public function __construct(
        CustomerRepositoryInterface $customerRepo,
        UserRepositoryInterface $userRepo,
        EmployeeRepositoryInterface $employeeRepo
    ) {
        $this->customerRepo = $customerRepo;
        $this->userRepo = $userRepo;
        $this->employeeRepo = $employeeRepo;
    }

    public function userExists($userid){
        $user = $this->userRepo->findById($userid);
        return $user != null;
    }


    public function convertUserIdtoCustomerId($userId)
    {
        if (!$this->userExists($userId)) {
            return null;
        }
        $customer = $this->customerRepo->findByUserId($userId);
        return $customer ? $customer->id : null;
    }

    public function convertUserIdtoEmployeeId($userId)
    {
        if (!$this->userExists($userId)) {
            return null;
        }
        $employee = $this->employeeRepo->findByUserId($userId);
        return $employee ? $employee->id : null;
    }

    public function covertEmployeeIdtoCustomerId($employeeid)
    {
        $employee = $this->employeeRepo->findById($employeeid);
        if (!$employee) {
            return null;
        }
        $userId = $employee->user_id;
        return $this->convertUserIdtoCustomerId($userId);
    }

    public function covertCustomertoEmployeeId($customerid)
    {
        $customer = $this->customerRepo->findById($customerid);
        if (!$customer) {
            return null;
        }
        $userId = $customer->user_id;
        return $this->convertUserIdtoEmployeeId($userId);
    }

    
}