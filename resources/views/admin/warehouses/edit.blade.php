@extends('layouts.admin')

@section('title', 'Sửa Kho')
@section('header', 'Cập nhật: ' . $warehouse->name)

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('warehouses.update', $warehouse->id) }}" method="POST">
        @csrf @method('PUT')
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên Kho <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $warehouse->name) }}" class="w-full border rounded px-3 py-2 text-sm">
            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
            <select name="status" class="w-full border rounded px-3 py-2 text-sm bg-white">
                <option value="active" {{ $warehouse->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="maintenance" {{ $warehouse->status == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                <option value="locked" {{ $warehouse->status == 'locked' ? 'selected' : '' }}>Đóng cửa (Khóa)</option>
            </select>
            @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="bg-yellow-50 p-4 rounded text-sm text-yellow-800 mb-6">
            <i class="fa-solid fa-info-circle mr-1"></i> Lưu ý: Không thể thay đổi Loại kho hoặc Số lượng Block sau khi đã tạo để đảm bảo toàn vẹn dữ liệu tồn kho.
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('warehouses.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 rounded-lg">Hủy</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm">Cập nhật</button>
        </div>
    </form>
</div>
@endsection