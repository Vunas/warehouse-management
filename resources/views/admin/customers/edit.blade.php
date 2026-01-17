@extends('layouts.admin')

@section('title', 'Cập nhật Khách hàng')
@section('header', 'Chỉnh sửa: ' . $customer->company_name)

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Cột Trái: Thông tin Công ty -->
            <div>
                <h3 class="text-sm font-bold text-blue-600 uppercase mb-4 pb-2 border-b flex items-center">
                    <i class="fa-solid fa-building mr-2"></i> Thông tin Doanh nghiệp
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên Công ty / Tổ chức <span class="text-red-500">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name', $customer->company_name) }}" class="w-full border rounded px-3 py-2 text-sm">
                        @error('company_name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã số thuế <span class="text-red-500">*</span></label>
                        <input type="text" name="tax_code" value="{{ old('tax_code', $customer->tax_code) }}" class="w-full border rounded px-3 py-2 text-sm font-mono">
                        @error('tax_code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại liên hệ <span class="text-red-500">*</span></label>
                        <input type="text" name="billing_phone" value="{{ old('billing_phone', $customer->billing_phone) }}" class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ trụ sở</label>
                        <textarea name="address" rows="3" class="w-full border rounded px-3 py-2 text-sm">{{ old('address', $customer->address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Thông tin Tài khoản User -->
            <div>
                <h3 class="text-sm font-bold text-green-600 uppercase mb-4 pb-2 border-b flex items-center">
                    <i class="fa-solid fa-user-shield mr-2"></i> Tài khoản Đại diện
                </h3>

                <div class="bg-gray-50 p-4 rounded-lg space-y-4 border border-gray-100">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên người đại diện <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $customer->user->full_name) }}" class="w-full border rounded px-3 py-2 text-sm">
                        @error('full_name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email đăng nhập <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $customer->user->email) }}" class="w-full border rounded px-3 py-2 text-sm">
                        @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ $customer->user->is_active ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm text-gray-700 font-medium">Kích hoạt tài khoản</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-6">Bỏ chọn để khóa quyền truy cập của khách hàng này.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-100">
            <a href="{{ route('customers.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-white border border-gray-300 rounded-lg">Hủy bỏ</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm">Cập nhật</button>
        </div>
    </form>
</div>
@endsection