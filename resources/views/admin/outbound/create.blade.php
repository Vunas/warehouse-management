@extends('layouts.admin')

@section('title', 'Tạo Phiếu Xuất')
@section('header', 'Yêu cầu Xuất kho')

@section('content')
<form action="{{ route('outbound_tickets.store') }}" method="POST">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Thông tin chung -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Thông tin Hợp đồng</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn Hợp đồng <span class="text-red-500">*</span></label>
                    <select name="contract_id" class="w-full border rounded px-3 py-2 text-sm bg-gray-50 focus:ring-orange-500">
                        <option value="">-- Chọn hợp đồng --</option>
                        @foreach($contracts as $con)
                            <option value="{{ $con->id }}">{{ $con->contract_code }} - {{ $con->customer->company_name }}</option>
                        @endforeach
                    </select>
                    @error('contract_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày yêu cầu xuất <span class="text-red-500">*</span></label>
                    <input type="date" name="requested_date" value="{{ old('requested_date') }}" class="w-full border rounded px-3 py-2 text-sm">
                    @error('requested_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Danh sách hàng hóa -->
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="font-bold text-gray-700">Chi tiết Hàng cần xuất</h3>
                <button type="button" onclick="addProductRow()" class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded hover:bg-orange-200 border border-orange-300">
                    <i class="fa-solid fa-plus mr-1"></i> Thêm dòng
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left" id="product-table">
                    <thead class="bg-gray-100 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="p-2 w-1/2">Sản phẩm</th>
                            <th class="p-2 w-32">Số lượng</th>
                            <th class="p-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr id="row-template" class="hidden">
                            <td class="p-2">
                                <select name="products[INDEX][product_id]" class="w-full border rounded px-2 py-1 text-sm" disabled>
                                    <option value="">-- SP --</option>
                                    @foreach(\App\Models\Product::all() as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->sku }} - {{ $prod->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-2">
                                <input type="number" name="products[INDEX][quantity]" class="w-full border rounded px-2 py-1 text-center" min="1" disabled>
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @error('products') <div class="text-xs text-red-500 mt-2">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="flex justify-end mt-6 gap-3">
        <a href="{{ route('outbound_tickets.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Hủy</a>
        <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg text-sm font-bold hover:bg-orange-700 shadow-sm">
            <i class="fa-solid fa-truck-fast mr-2"></i> Tạo Phiếu Xuất
        </button>
    </div>
</form>

<script>
    let rowIndex = 0;
    function addProductRow() {
        const template = document.getElementById('row-template');
        const tbody = document.querySelector('#product-table tbody');
        const clone = template.cloneNode(true);
        clone.classList.remove('hidden');
        clone.removeAttribute('id');
        clone.innerHTML = clone.innerHTML.replace(/INDEX/g, rowIndex);
        clone.querySelectorAll('input, select').forEach(input => input.disabled = false);
        tbody.appendChild(clone);
        rowIndex++;
    }
    function removeRow(btn) { btn.closest('tr').remove(); }
    document.addEventListener('DOMContentLoaded', () => { addProductRow(); });
</script>
@endsection