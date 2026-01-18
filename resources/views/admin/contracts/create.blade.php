@extends('layouts.admin')

@section('title', 'Tạo Hợp đồng')
@section('header', 'Đăng ký Hợp đồng Thuê Kho')

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('contracts.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cột Trái: Thông tin chung -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Thông tin Khách hàng</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Khách hàng <span class="text-red-500">*</span></label>
                        <select name="customer_id" class="w-full border rounded px-3 py-2 text-sm bg-gray-50">
                            <option value="">-- Chọn khách hàng --</option>
                            @foreach($customers as $cus)
                                <option value="{{ $cus->id }}">{{ $cus->company_name }} ({{ $cus->tax_code }})</option>
                            @endforeach
                        </select>
                        @error('customer_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã hợp đồng <span class="text-red-500">*</span></label>
                        <input type="text" name="contract_code" class="w-full border rounded px-3 py-2 text-sm uppercase font-mono" placeholder="CTR-YYYY-XXX">
                        @error('contract_code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Thời hạn & Phí</h3>
                    
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Bắt đầu</label>
                            <input type="date" name="start_date" class="w-full border rounded px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Kết thúc</label>
                            <input type="date" name="end_date" class="w-full border rounded px-2 py-1.5 text-sm">
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phí phạt (%)</label>
                        <input type="number" name="penalty_markup" value="0" class="w-full border rounded px-3 py-2 text-sm">
                        <p class="text-[10px] text-gray-400 mt-1">Áp dụng khi quá hạn hoặc vi phạm</p>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Chọn Lô (Block) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm">
                <h3 class="font-bold text-gray-700 mb-4 border-b pb-2 flex justify-between items-center">
                    <span>Chọn Lô/Kệ thuê</span>
                    <span class="text-xs font-normal text-blue-600 bg-blue-50 px-2 py-1 rounded">Chỉ hiện lô trống</span>
                </h3>

                <div class="overflow-y-auto h-96 border rounded-lg p-2 bg-gray-50">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-100 text-xs text-gray-500 sticky top-0">
                            <tr>
                                <th class="p-2">Chọn</th>
                                <th class="p-2">Mã Lô</th>
                                <th class="p-2">Kho</th>
                                <th class="p-2">Sức chứa</th>
                                <th class="p-2">Giá thuê (VND)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($availableBlocks as $block)
                            <tr>
                                <td class="p-2 text-center">
                                    <input type="checkbox" name="blocks[{{ $loop->index }}][id]" value="{{ $block->id }}" class="w-4 h-4 text-blue-600">
                                </td>
                                <td class="p-2 font-mono font-bold">{{ $block->block_code }}</td>
                                <td class="p-2 text-xs">{{ $block->warehouse->name }}</td>
                                <td class="p-2">
                                    {{ $block->total_slots }} slots
                                    <input type="hidden" name="blocks[{{ $loop->index }}][slots_committed]" value="{{ $block->total_slots }}">
                                </td>
                                <td class="p-2">
                                    <input type="number" name="blocks[{{ $loop->index }}][price]" class="w-28 border rounded px-2 py-1 text-right text-xs" placeholder="Nhập giá">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @error('blocks') <div class="text-xs text-red-500 mt-2">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="flex justify-end mt-6 gap-3">
            <a href="{{ route('contracts.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Hủy</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700">Tạo Hợp đồng</button>
        </div>
    </form>
</div>
@endsection