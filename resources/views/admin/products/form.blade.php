@extends('layouts.admin')

@php
    $isEdit = isset($product);
    $action = $isEdit ? route('products.update', $product->id) : route('products.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Cập nhật Sản phẩm' : 'Thêm Sản phẩm mới';
@endphp

@section('content')
<x-crud.form
    :title="$title"
    :action="$action"
    :method="$method"
    cancelRoute="{{ route('products.index') }}">
    
    <!-- Cột 1 -->
    <div class="space-y-6">
        <x-ui.input name="name" label="Tên sản phẩm" :value="old('name', $product->name ?? '')" required="true" />
        <x-ui.input name="price" type="number" label="Giá bán (VNĐ)" :value="old('price', $product->price ?? '')" required="true" />
        
        <!-- Danh mục -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Danh mục *</label>
            <select name="category_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">-- Chọn danh mục --</option>
                @foreach(\App\Models\Category::all() as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <!-- Cột 2 -->
    <div class="space-y-6">
        <!-- Thương hiệu -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Thương hiệu *</label>
            <select name="brand_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">-- Chọn thương hiệu --</option>
                @foreach(\App\Models\Brand::all() as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
            </select>
            @error('brand_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Mô tả sản phẩm</label>
            <textarea name="description" rows="4" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description', $product->description ?? '') }}</textarea>
            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <!-- Trạng thái -->
    <div class="md:col-span-2 mt-2">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng thái kinh doanh</label>
        <div class="flex items-center space-x-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="is_active" value="1" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('is_active', $product->is_active ?? 1) == 1 ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700 font-medium">Đang kinh doanh</span>
            </label>
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="is_active" value="0" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500" {{ old('is_active', $product->is_active ?? 1) == 0 ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700 font-medium">Ngừng kinh doanh</span>
            </label>
        </div>
    </div>
</x-crud.form>
@endsection