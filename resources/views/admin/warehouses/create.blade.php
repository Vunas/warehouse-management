@extends('layouts.admin')

@section('title', 'Thêm Kho Mới')
@section('header', 'Tạo Cấu Trúc Kho')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('warehouses.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Thông tin cơ bản -->
            <div class="space-y-4">
                <h3 class="font-bold text-gray-700 border-b pb-2">Thông tin chung</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên Kho <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2 text-sm" placeholder="VD: Kho Tổng A">
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại hình <span class="text-red-500">*</span></label>
                    <select name="type_id" class="w-full border rounded px-3 py-2 text-sm bg-white">
                        <option value="">-- Chọn loại kho --</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->type_code }} - {{ Str::limit($type->description, 30) }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Cấu hình Block/Slot -->
            <div class="space-y-4 bg-blue-50 p-4 rounded-lg border border-blue-100">
                <h3 class="font-bold text-blue-700 border-b border-blue-200 pb-2">Cấu hình Sức chứa</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng Kệ/Lô (Blocks) <span class="text-red-500">*</span></label>
                    <input type="number" name="total_blocks" value="{{ old('total_blocks', 1) }}" min="1" max="50" class="w-full border rounded px-3 py-2 text-sm">
                    <p class="text-[10px] text-gray-500 mt-1">Hệ thống sẽ tạo tự động mã Block (A-01, A-02...)</p>
                    @error('total_blocks') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số Slot mỗi Kệ <span class="text-red-500">*</span></label>
                    <input type="number" name="slots_per_block" value="{{ old('slots_per_block', 100) }}" min="10" class="w-full border rounded px-3 py-2 text-sm">
                    @error('slots_per_block') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('warehouses.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 rounded-lg">Hủy</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm">
                <i class="fa-solid fa-wand-magic-sparkles mr-2"></i> Tạo & Sinh cấu trúc
            </button>
        </div>
    </form>
</div>
@endsection