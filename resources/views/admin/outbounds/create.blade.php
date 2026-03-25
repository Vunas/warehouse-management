@extends('layouts.admin')

@section('content')
<x-crud.form 
    title="Tạo Phiếu Xuất Kho (Pick List)" 
    action="{{ route('outbounds.store') }}" 
    method="POST" 
    cancelRoute="{{ route('outbounds.index') }}"
>
    <div class="md:col-span-2 space-y-6 max-w-2xl">
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
            <p class="text-sm text-blue-700 font-medium">Phiếu xuất kho được lập dựa trên các Đơn hàng (Sales Orders) đã được thanh toán hoặc đang chờ xử lý.</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Chọn Đơn hàng cần xuất kho *</label>
            <select name="order_id" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">-- Chọn mã đơn hàng --</option>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}">
                        ĐH #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }} 
                        - Khách hàng: {{ $order->user->full_name ?? 'N/A' }} 
                        (Trạng thái: {{ strtoupper($order->status) }})
                    </option>
                @endforeach
            </select>
            @error('order_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</x-crud.form>
@endsection