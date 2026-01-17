@extends('layouts.admin')

@section('title', 'Quản lý Khách hàng')
@section('header', 'Danh sách Khách hàng (Đối tác)')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <form action="" method="GET" class="flex gap-2">
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                <input type="text" name="search" placeholder="Tên công ty, MST..." class="border rounded-lg pl-8 pr-3 py-2 text-sm w-64 focus:outline-none focus:border-blue-500">
            </div>
            <button type="submit" class="bg-gray-100 px-3 py-2 rounded-lg text-sm hover:bg-gray-200">Tìm kiếm</button>
        </form>
        
        <a href="{{ route('customers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 shadow-sm">
            <i class="fa-solid fa-plus mr-1"></i> Thêm Khách hàng
        </a>
    </div>

    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 uppercase text-xs text-gray-500">
            <tr>
                <th class="px-6 py-3">Công ty / Tổ chức</th>
                <th class="px-6 py-3">Người đại diện (User)</th>
                <th class="px-6 py-3">Mã số thuế</th>
                <th class="px-6 py-3">Liên hệ</th>
                <th class="px-6 py-3">Trạng thái</th>
                <th class="px-6 py-3 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($customers as $customer)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-800">{{ $customer->company_name }}</div>
                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ $customer->address }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold mr-2">
                            {{ substr($customer->user->full_name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $customer->user->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $customer->user->username }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $customer->tax_code }}</td>
                <td class="px-6 py-4">
                    <div class="text-xs"><i class="fa-solid fa-phone text-gray-400 mr-1"></i> {{ $customer->billing_phone }}</div>
                    <div class="text-xs"><i class="fa-solid fa-envelope text-gray-400 mr-1"></i> {{ $customer->user->email }}</div>
                </td>
                <td class="px-6 py-4">
                    @if($customer->user->is_active)
                        <span class="text-green-600 bg-green-50 px-2 py-1 rounded-full text-xs font-bold">Active</span>
                    @else
                        <span class="text-red-600 bg-red-50 px-2 py-1 rounded-full text-xs font-bold">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('customers.edit', $customer->id) }}" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen"></i></a>
                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Xóa khách hàng này? Lưu ý: Hợp đồng liên quan cũng có thể bị ảnh hưởng.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="p-4 border-t border-gray-100">
        {{ $customers->links() }}
    </div>
</div>
@endsection