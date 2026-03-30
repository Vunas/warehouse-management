<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Services\CategoryService; // Để lấy ds category cho select box
use Illuminate\Http\Request;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Exception;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(Request $request)
    {
        $products = $this->productService->getPaginatedProducts($request->get('per_page', 15));
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        // View form tạo mới cần truyền danh sách category, brand vào
        return view('admin.products.form');
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $this->productService->createProduct($request->validated());
            return redirect()->route('products.index')->with('success', 'Tạo sản phẩm thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $this->productService->updateProduct($product->id, $request->validated());
            return redirect()->route('products.index')->with('success', 'Cập nhật thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->productService->deleteProduct($product->id);
            return redirect()->route('products.index')->with('success', 'Đã chuyển vào thùng rác!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}