@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

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

    <!-- MAIN -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- SLIDER -->
        <div class="bg-white rounded-2xl shadow-sm p-4">
            <div class="relative overflow-hidden rounded-xl">

                <img id="sliderImage"
                    src="{{ asset('images/overview1.PNG') }}"
                    class="w-full h-72 object-cover rounded-xl transition-opacity duration-700 opacity-100">

                <!-- Nút -->
                <button onclick="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 text-white px-3 py-1 rounded">
                    ‹
                </button>

                <button onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 text-white px-3 py-1 rounded">
                    ›
                </button>

            </div>
        </div>

        <!-- FEATURES -->
        <div class="bg-white rounded-2xl shadow-lg p-6 space-y-6">

            <div class="flex gap-4 items-start">
                <div class="w-14 h-14 bg-blue-300 flex items-center justify-center rounded-xl">
                    <i class="fa-solid fa-layer-group text-blue-800"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">HỆ THỐNG HIỆN ĐẠI</h3>
                    <p class="text-sm text-gray-600">
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
                    <p class="text-sm text-gray-600">
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
                    <p class="text-sm text-gray-600">
                        Phù hợp cho mọi quy mô doanh nghiệp, dễ mở rộng
                    </p>
                </div>
            </div>

        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-slate-900 text-white p-5 rounded-xl flex items-center gap-3 shadow-lg">
            <i class="fa-solid fa-car text-2xl"></i>
            <div>
                <h4 class="font-bold">ĐA DẠNG SẢN PHẨM</h4>
                <p class="text-xs">Phụ tùng ô tô - Nội thất - Đồ chơi xe</p>
            </div>
        </div>

        <div class="bg-slate-900 text-white p-5 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-snowflake text-2xl"></i>
            <div>
                <h4 class="font-bold">SẢN PHẨM THẾ MẠNH</h4>
                <p class="text-xs">Điều hòa ô tô - Hàng nhập khẩu</p>
            </div>
        </div>

        <div class="bg-slate-900 text-white p-5 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-gears text-2xl"></i>
            <div>
                <h4 class="font-bold">DỊCH VỤ HÀNG ĐẦU</h4>
                <p class="text-xs">Chất lượng là ưu tiên hàng đầu</p>
            </div>
        </div>

        <div class="bg-slate-900 text-white p-5 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-paper-plane text-2xl"></i>
            <div>
                <h4 class="font-bold">THƯƠNG HIỆU UY TÍN</h4>
                <p class="text-xs">Niềm tin và chất lượng</p>
            </div>
        </div>

    </div>

</div>

<!-- SCRIPT SLIDER -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const images = [
        "{{ asset('images/overview1.PNG') }}",
        "{{ asset('images/overview2.PNG') }}",
        "{{ asset('images/overview3.PNG') }}"
    ];

    let currentIndex = 0;
    const slider = document.getElementById("sliderImage");

    function changeSlide(index) {
        if (!slider) return;

        // fade out
        slider.classList.remove("opacity-100");
        slider.classList.add("opacity-0");

        setTimeout(() => {
            slider.src = images[index];

            // fade in
            slider.classList.remove("opacity-0");
            slider.classList.add("opacity-100");
        }, 600);
    }

    window.nextSlide = function () {
        currentIndex = (currentIndex + 1) % images.length;
        changeSlide(currentIndex);
    }

    window.prevSlide = function () {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        changeSlide(currentIndex);
    }

    // auto slide
    let interval = setInterval(nextSlide, 6000);

    // pause khi hover (UX xịn hơn)
    slider.addEventListener("mouseenter", () => clearInterval(interval));
    slider.addEventListener("mouseleave", () => {
        interval = setInterval(nextSlide, 6000);
    });

});
</script>

@endsection