@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Tiêu đề -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-gray-800">Địa Chỉ Giao Hàng</h1>
            <p class="text-gray-600 mt-2">Quản lý các địa chỉ giao hàng của bạn</p>
        </div>
        <a href="{{ route('customer.address.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
            <i class="fa-solid fa-plus mr-2"></i>Thêm Địa Chỉ
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800 font-bold mb-2"><i class="fa-solid fa-exclamation-circle mr-2"></i>Lỗi:</p>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center">
            <i class="fa-solid fa-check-circle text-green-600 mr-3 text-xl"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if($addresses->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fa-solid fa-map-location-dot text-5xl text-gray-300 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Chưa Có Địa Chỉ</h2>
            <p class="text-gray-600 mb-6">Hãy thêm một địa chỉ giao hàng để có thể mua sắm</p>
            <a href="{{ route('customer.address.create') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus mr-2"></i>Thêm Địa Chỉ Đầu Tiên
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($addresses as $address)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $address->ward->name }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ $address->ward->district->name }}, {{ $address->ward->district->city->name }}
                            </p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold">
                            <i class="fa-solid fa-check-circle mr-1"></i>
                        </span>
                    </div>

                    <p class="text-gray-700 mb-4 leading-relaxed">{{ $address->detail }}</p>

                    <div class="flex gap-2">
                        <a href="{{ route('customer.address.edit', $address) }}" class="flex-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg font-bold text-sm transition text-center">
                            <i class="fa-solid fa-edit mr-2"></i>Sửa
                        </a>
                        <form action="{{ route('customer.address.destroy', $address) }}" method="POST" class="flex-1" onsubmit="return confirm('Bạn chắc chắn muốn xóa địa chỉ này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-4 py-2 rounded-lg font-bold text-sm transition">
                                <i class="fa-solid fa-trash mr-2"></i>Xóa
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
