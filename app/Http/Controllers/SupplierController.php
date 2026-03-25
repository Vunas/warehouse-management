<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Exception;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
        // $this->authorizeResource(Supplier::class, 'supplier');
    }

    public function index(Request $request)
    {
        $suppliers = $this->supplierService->getPaginatedSuppliers($request->get('per_page', 15), $request->get('search'));
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create() { return view('admin.suppliers.form'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
        ]);
        $this->supplierService->createSupplier($validated);
        return redirect()->route('suppliers.index')->with('success', 'Thêm nhà cung cấp thành công!');
    }

    public function edit(Supplier $supplier) { return view('admin.suppliers.form', compact('supplier')); }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
        ]);
        $this->supplierService->updateSupplier($supplier, $validated);
        return redirect()->route('suppliers.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Supplier $supplier)
    {
        $this->supplierService->deleteSupplier($supplier);
        return back()->with('success', 'Đã xóa nhà cung cấp!');
    }
}