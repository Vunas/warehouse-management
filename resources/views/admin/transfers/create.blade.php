@extends('layouts.admin')

@section('title', 'Tạo Lệnh Chuyển')
@section('header', 'Điều chuyển Hàng hóa')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('transfers.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Nguồn -->
            <div class="p-4 bg-red-50 rounded-lg border border-red-100">
                <h3 class="font-bold text-red-700 mb-3 uppercase text-xs flex items-center">
                    <i class="fa-solid fa-box-open mr-2"></i> Chuyển Từ (Nguồn)
                </h3>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn Lô/Kệ <span class="text-red-500">*</span></label>
                    <select name="from_block_id" class="w-full border rounded px-3 py-2 text-sm bg-white">
                        <option value="">-- Chọn lô nguồn --</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}" {{ old('from_block_id') == $block->id ? 'selected' : '' }}>
                                {{ $block->block_code }} ({{ $block->warehouse->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('from_block_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Đích -->
            <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                <h3 class="font-bold text-green-700 mb-3 uppercase text-xs flex items-center">
                    <i class="fa-solid fa-box mr-2"></i> Chuyển Đến (Đích)
                </h3>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn Lô/Kệ <span class="text-red-500">*</span></label>
                    <select name="to_block_id" class="w-full border rounded px-3 py-2 text-sm bg-white">
                        <option value="">-- Chọn lô đích --</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}" {{ old('to_block_id') == $block->id ? 'selected' : '' }}>
                                {{ $block->block_code }} ({{ $block->warehouse->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('to_block_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Lý do điều chuyển</label>
            <textarea name="trigger_reason" rows="2" class="w-full border rounded px-3 py-2 text-sm" placeholder="VD: Sắp xếp lại kho, Hàng hư hỏng chuyển về khu bảo trì...">{{ old('trigger_reason') }}</textarea>
        </div>

        <!-- Danh sách hàng -->
        <div class="border-t border-gray-200 pt-4">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-gray-700">Chi tiết Hàng cần chuyển</h3>
                <button type="button" onclick="addItemRow()" class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded hover:bg-indigo-200 font-bold">
                    <i class="fa-solid fa-plus mr-1"></i> Thêm Hàng
                </button>
            </div>
            
            <div class="bg-yellow-50 p-3 rounded text-xs text-yellow-800 mb-3 flex items-start">
                <i class="fa-solid fa-circle-info mr-2 mt-0.5"></i>
                <div>
                    <b>Lưu ý:</b> Bạn cần nhập chính xác <b>ID của Inventory Item</b> (Xem tại trang Tồn kho). <br>
                    Hệ thống sẽ tự động trừ hàng từ lô Nguồn và cộng vào lô Đích sau khi hoàn tất.
                </div>
            </div>

            <table class="w-full text-sm text-left" id="items-table">
                <thead class="bg-gray-100 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="p-2 w-1/2">Inventory Item ID</th>
                        <th class="p-2 w-32">Số lượng</th>
                        <th class="p-2 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr id="item-template" class="hidden">
                        <td class="p-2">
                            <input type="number" name="items[INDEX][inventory_item_id]" class="w-full border rounded px-3 py-2 text-sm" placeholder="Nhập ID (VD: 105)" disabled>
                        </td>
                        <td class="p-2">
                            <input type="number" name="items[INDEX][quantity]" class="w-full border rounded px-3 py-2 text-center" min="1" value="1" disabled>
                        </td>
                        <td class="p-2 text-center">
                            <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
            @error('items') <div class="text-xs text-red-500 mt-2">{{ $message }}</div> @enderror
        </div>

        <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
            <a href="{{ route('transfers.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 rounded-lg">Hủy bỏ</a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-sm">
                <i class="fa-solid fa-save mr-2"></i> Tạo Lệnh Chuyển
            </button>
        </div>
    </form>
</div>

<script>
    let itemIndex = 0;
    function addItemRow() {
        const template = document.getElementById('item-template');
        const tbody = document.querySelector('#items-table tbody');
        const clone = template.cloneNode(true);
        clone.classList.remove('hidden');
        clone.removeAttribute('id');
        clone.innerHTML = clone.innerHTML.replace(/INDEX/g, itemIndex);
        clone.querySelectorAll('input').forEach(input => input.disabled = false);
        tbody.appendChild(clone);
        itemIndex++;
    }
    function removeRow(btn) { btn.closest('tr').remove(); }
    document.addEventListener('DOMContentLoaded', () => { addItemRow(); });
</script>
@endsection