<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest; // Bạn tự tạo file này tương tự Store
use App\Services\CustomerService;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
        // $this->authorizeResource(Customer::class, 'customer'); // Đăng ký Policy nếu cần
    }

    public function index()
    {
        $customers = $this->customerService->getAllCustomers();
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $this->customerService->createCustomer($request->validated());
        return redirect()->route('customers.index')->with('success', 'Thêm khách hàng thành công');
    }

    public function edit($id)
    {
        $customer = $this->customerService->getCustomerById($id);
        return view('admin.customers.edit', compact('customer'));
    }

    // public function update(UpdateCustomerRequest $request, $id)
    // {
    //     $this->customerService->updateCustomer($id, $request->validated());
    //     return redirect()->route('customers.index')->with('success', 'Cập nhật khách hàng thành công');
    // }

    public function destroy($id)
    {
        $this->customerService->deleteCustomer($id);
        return redirect()->route('customers.index')->with('success', 'Đã xóa khách hàng');
    }
}