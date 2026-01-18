@extends('layouts.admin')

@section('title', 'Sửa Hợp đồng')
@section('header', 'Cập nhật Hợp đồng: ' . $contract->contract_code)

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('contracts.update', $contract->id) }}" method="POST">
        @csrf @method('PUT')
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
            <select name="status" class="w-full border rounded px-3 py-2 text-sm bg-white">
                <option value="active" {{ $contract->status == 'active' ? 'selected' : '' }}>Active (Hiệu lực)</option>
                <option value="expired" {{ $contract->status == 'expired' ? 'selected' : '' }}>Expired (Hết hạn)</option>
                <option value="suspended" {{ $contract->status == 'suspended' ? 'selected' : '' }}>Suspended (Đình chỉ)</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu</label>
                <input type="date" name="start_date" value="{{ $contract->start_date->format('Y-m-d') }}" class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc</label>
                <input type="date" name="end_date" value="{{ $contract->end_date->format('Y-m-d') }}" class="w-full border rounded px-3 py-2 text-sm">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Phí phạt (%)</label>
            <input type="number" name="penalty_markup" value="{{ $contract->penalty_markup }}" class="w-full border rounded px-3 py-2 text-sm">
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('contracts.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 rounded-lg">Hủy</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm">Cập nhật</button>
        </div>
    </form>
</div>
@endsection