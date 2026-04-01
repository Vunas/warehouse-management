@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow border p-6 flex justify-between">
        <div>
            <h2 class="text-2xl font-black text-gray-800">📦 Pick List: OUT-{{ str_pad($outbound->id, 5, '0', STR_PAD_LEFT) }}</h2>
            <p class="mt-2 text-gray-600 font-medium">Kho lấy hàng: <span class="text-indigo-600">{{ $outbound->warehouse->name }}</span></p>
            <p class="text-red-500 italic text-sm mt-1">*Hệ thống đã tự động tìm và giữ chỗ hàng hóa (Auto-Allocated) dựa trên hạn sử dụng (FEFO).</p>
        </div>
        <div class="text-right">
            @if($outbound->status === 'pending')
                <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full font-bold">Chờ xuất kho</span>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-indigo-50 border-b border-indigo-100">
                <tr>
                    <th class="px-6 py-4 text-left font-bold text-indigo-900">1. Đến Kệ / Vị trí</th>
                    <th class="px-6 py-4 text-left font-bold text-indigo-900">2. Lấy Sản phẩm</th>
                    <th class="px-6 py-4 text-left font-bold text-indigo-900">3. Tìm đúng Lô (Batch)</th>
                    <th class="px-6 py-4 text-center font-bold text-red-600">4. Số Lượng Lấy</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($outbound->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-gray-200 text-gray-800 rounded font-bold uppercase">{{ $item->location->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $item->product->name }}</td>
                        <td class="px-6 py-4">
                            @if($item->batch)
                                <div class="font-mono text-sm font-bold">{{ $item->batch->batch_code }}</div>
                                <div class="text-xs text-gray-500">HSD: {{ $item->batch->expiry_date ? \Carbon\Carbon::parse($item->batch->expiry_date)->format('d/m/Y') : 'Không có' }}</div>
                            @else
                                <span class="text-gray-400 italic">Không quản lý lô</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-2xl font-black text-red-600">{{ $item->quantity }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($outbound->status === 'pending')
            <div class="p-6 bg-gray-50 border-t flex justify-end gap-4">
                <form action="{{ route('outbounds.destroy', $outbound->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-6 py-3 border border-red-300 text-red-600 bg-white rounded-lg font-bold">Hủy & Nhả Tồn Kho</button>
                </form>

                <form action="{{ route('outbounds.complete', $outbound->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-lg font-bold shadow-lg hover:bg-green-700">
                        ✔ HOÀN TẤT ĐI NHẶT HÀNG (TRỪ KHO THỰC TẾ)
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection