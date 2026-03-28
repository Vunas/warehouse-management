@extends('layouts.admin')

@section('content')
<x-crud.form title="Khởi tạo Phiếu Nhập Kho" action="{{ route('inbounds.store') }}" method="POST" cancelRoute="{{ route('inbounds.index') }}">
    <div class="md:col-span-2 space-y-6">
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
            <p class="text-sm text-blue-700 font-medium">Bước 1: Khởi tạo phiếu nhập với Nhà cung cấp. Bước 2: Thêm sản phẩm và chỉ định Vị trí lưu trữ.</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Chọn Nhà cung cấp *</label>
            <select name="supplier_id" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">-- Chọn Nhà cung cấp --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->phone }})</option>
                @endforeach
            </select>
            @error('supplier_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</x-crud.form>
@endsection