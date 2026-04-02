@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-800">Tổng quan hệ thống</h1>
            <p class="text-gray-600 mt-1">Quản lý kho hàng thông minh và hiệu quả</p>
        </div>

        <div class="text-sm bg-white px-4 py-2 rounded-lg shadow border">
            <i class="fa-regular fa-clock mr-1"></i>
            {{ now()->format('H:i - d/m/Y') }}
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- IMAGE -->
        <div class="bg-white rounded-2xl shadow-sm border p-4 overflow-hidden">
            <img 
                src="https://www.phathuyauto.com/wp-content/uploads/elementor/thumbs/background-xe-oto-3-1600x900-1-r56ddk2kbx5gvz36wd1nquu3tk6wq58tabc54umtfs.jpg" 
                class="w-full h-72 object-cover rounded-xl transition duration-300">
        </div>

        <!-- FEATURES -->
        <div class="bg-white rounded-2xl shadow-sm border p-6 space-y-6">

            <div class="flex gap-4 items-start">
                <div class="w-14 h-14 bg-blue-300 flex items-center justify-center rounded-xl">
                    <i class="fa-solid fa-layer-group text-blue-800"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">HỆ THỐNG HIỆN ĐẠI</h3>
                    <p class="text-sx text-gray-600">
                        Tự động hóa quy trình nhập hàng, giảm sai sót và tiết kiệm thời gian
                    </p>
                </div>
            </div>

            <div class="border-t"></div>

            <div class="flex gap-4 items-start">
                <div class="w-14 h-14 bg-green-300 flex items-center justify-center rounded-xl">
                    <i class="fa-solid fa-shield-halved text-green-800"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">BẢO MẬT TUYỆT ĐỐI</h3>
                    <p class="text-sx text-gray-600">
                        Phân quyền rõ ràng, mã hóa dữ liệu và sao lưu định kỳ
                    </p>
                </div>
            </div>

            <div class="border-t"></div>

            <div class="flex gap-4 items-start">
                <div class="w-14 h-14 bg-purple-300 flex items-center justify-center rounded-xl">
                    <i class="fa-solid fa-handshake text-purple-800"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">CHUYÊN NGHIỆP - ĐÁNG TIN CẬY</h3>
                    <h1 class="text-sx text-gray-600">
                        Phù hợp cho mọi quy mô doanh nghiệp, dễ mở rộng
                    </h1>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection