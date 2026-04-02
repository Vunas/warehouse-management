<?php

namespace App\Http\Controllers;

use App\Services\ProductBatchService;
use App\Models\Product; // Cần để load danh sách sản phẩm cho Dropdown
use Illuminate\Http\Request;

class ProductBatchController extends Controller
{
    protected $batchService;

    public function __construct(ProductBatchService $batchService)
    {
        $this->batchService = $batchService;
    }

    public function index()
    {
        $batches = $this->batchService->getAllPaginated();
        return view('admin.product_batches.index', compact('batches'));
    }

    public function create()
    {
        $products = Product::select('id', 'name')->get();
        return view('admin.product_batches.form', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_code' => 'nullable|string|max:100|unique:product_batches,batch_code',
            'manufacture_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:manufacture_date',
        ]);

        $this->batchService->createBatch($validated);

        return redirect()->route('product-batches.index')->with('success', 'Tạo lô hàng thành công!');
    }

    public function edit($id)
    {
        $batch = $this->batchService->getBatchById($id);
        $products = Product::select('id', 'name')->get();
        
        return view('admin.product_batches.form', compact('batch', 'products'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_code' => 'required|string|max:100|unique:product_batches,batch_code,' . $id,
            'manufacture_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:manufacture_date',
        ]);

        $this->batchService->updateBatch($id, $validated);

        return redirect()->route('product-batches.index')->with('success', 'Cập nhật lô hàng thành công!');
    }

    public function destroy($id)
    {
        $this->batchService->deleteBatch($id);
        return redirect()->route('product-batches.index')->with('success', 'Xóa lô hàng thành công!');
    }
}