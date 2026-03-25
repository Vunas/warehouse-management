<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Exception;

class BrandController extends Controller
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
        $this->authorizeResource(Brand::class, 'brand');
    }

    public function index(Request $request)
    {
        $brands = $this->brandService->getPaginatedBrands($request->get('per_page', 15), $request->get('search'));
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:150|unique:brands,name']);
        try {
            $this->brandService->createBrand($validated);
            return redirect()->route('brands.index')->with('success', 'Thêm thương hiệu thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.form', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate(['name' => 'required|string|max:150|unique:brands,name,' . $brand->id]);
        try {
            $this->brandService->updateBrand($brand->id, $validated);
            return redirect()->route('brands.index')->with('success', 'Cập nhật thương hiệu thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Brand $brand)
    {
        try {
            $this->brandService->deleteBrand($brand->id);
            return back()->with('success', 'Đã xóa thương hiệu!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}