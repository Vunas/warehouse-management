@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-black text-gray-800">Tổng quan hệ thống</h1>
        <p class="text-gray-500">Giải pháp quản lý kho hàng thông minh</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Ảnh -->
        <div class="bg-white rounded-2xl shadow-sm border p-6 flex items-center justify-center">
            <img src="https://www.phathuyauto.com/wp-content/uploads/elementor/thumbs/background-xe-oto-3-1600x900-1-r56ddk2kbx5gvz36wd1nquu3tk6wq58tabc54umtfs.jpg" class="max-h-64">
        </div>

        <!-- Nội dung -->
        <div class="bg-white rounded-2xl shadow-sm border p-6 space-y-6">

            <div class="flex gap-4 border-b pb-4">
                <div class="w-12 h-12 bg-blue-50 flex items-center justify-center rounded">
                    <i class="fa-solid fa-layer-group text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-bold">HỆ THỐNG HIỆN ĐẠI</h3>
                    <p class="text-sm text-gray-500">
                        Quản lý kho thông minh, tự động hóa nhập - xuất - tồn
                    </p>
                </div>
            </div>

            <div class="flex gap-4 border-b pb-4">
                <div class="w-12 h-12 bg-green-50 flex items-center justify-center rounded">
                    <i class="fa-solid fa-shield-halved text-green-600"></i>
                </div>
                <div>
                    <h3 class="font-bold">BẢO MẬT TUYỆT ĐỐI</h3>
                    <p class="text-sm text-gray-500">
                        Phân quyền, mã hóa và sao lưu dữ liệu an toàn
                    </p>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="w-12 h-12 bg-purple-50 flex items-center justify-center rounded">
                    <i class="fa-solid fa-handshake text-purple-600"></i>
                </div>
                <div>
                    <h3 class="font-bold">CHUYÊN NGHIỆP - ĐÁNG TIN CẬY</h3>
                    <p class="text-sm text-gray-500">
                        Phù hợp mọi mô hình doanh nghiệp
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection