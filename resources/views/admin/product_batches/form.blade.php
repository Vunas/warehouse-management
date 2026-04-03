@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ isset($batch) ? 'Cập nhật Lô hàng' : 'Thêm Lô hàng mới' }}</h1>
        <a href="{{ route('product-batches.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 mt-2 inline-block">&larr; Quay lại danh sách</a>
    </div>

    <div class="bg-white shadow sm:rounded-lg p-6 border border-gray-200">
        <form action="{{ isset($batch) ? route('product-batches.update', $batch->id) : route('product-batches.store') }}" method="POST">
            @csrf
            @if(isset($batch))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                
                <div class="sm:col-span-2">
                    <label for="product_id" class="block text-sm font-medium text-gray-700">Sản phẩm <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <select id="product_id" name="product_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border">
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id', $batch->product_id ?? '') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('product_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="batch_code" class="block text-sm font-medium text-gray-700">Mã Lô (Batch Code)</label>
                    <div class="mt-1">
                        <input type="text" name="batch_code" id="batch_code" value="{{ old('batch_code', $batch->batch_code ?? '') }}" placeholder="Bỏ trống để hệ thống tự tạo (VD: Lô Nhớt Castrol tháng 10...)" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border">
                    </div>
                    @error('batch_code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="manufacture_date" class="block text-sm font-medium text-gray-700">Ngày sản xuất (NSX)</label>
                    <div class="mt-1">
                        <input type="date" name="manufacture_date" id="manufacture_date" value="{{ old('manufacture_date', isset($batch) && $batch->manufacture_date ? $batch->manufacture_date->format('Y-m-d') : '') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border">
                    </div>
                    @error('manufacture_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700">Hạn sử dụng (HSD)</label>
                    <div class="mt-1">
                        <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', isset($batch) && $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : '') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border">
                    </div>
                    @error('expiry_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{ isset($batch) ? 'Cập nhật Lô hàng' : 'Lưu Lô hàng' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection