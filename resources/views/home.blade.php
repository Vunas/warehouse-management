<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartWMS - Giải Pháp Kho Vận Hàng Rời & Nguyên Liệu</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    {{-- Google Fonts: Inter & Oswald (Industrial feel) --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Oswald:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Oswald', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#0f172a', // Slate 900
                            primary: '#2563eb', // Blue 600
                            accent: '#f59e0b', // Amber 500
                            surface: '#f8fafc', // Slate 50
                            steel: '#64748b', // Slate 500
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .industrial-text-shadow {
            text-shadow: 2px 2px 0px rgba(0, 0, 0, 0.1);
        }

        /* Hide scrollbar for clean UI */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .card-zoom:hover img {
            transform: scale(1.05);
        }

        .card-zoom img {
            transition: transform 0.5s ease;
        }
    </style>
</head>

<body class="font-sans text-slate-700 bg-brand-surface">

    <!-- NAVBAR -->
    <nav
        class="fixed w-full z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 transition-all duration-300 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-3 group">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <i class="fa-solid fa-boxes-stacked text-2xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight ">ALOVUA</h1>
                            <p class="text-xs  uppercase tracking-widest font-semibold">Logistics Solution
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#solutions"
                        class="text-sm font-semibold text-slate-600 hover:text-brand-primary uppercase tracking-wide transition-colors">Cấu
                        trúc kho</a>
                    <a href="#simulation"
                        class="text-sm font-semibold text-slate-600 hover:text-brand-primary uppercase tracking-wide transition-colors">Tính
                        Slot</a>
                    <a href="#process"
                        class="text-sm font-semibold text-slate-600 hover:text-brand-primary uppercase tracking-wide transition-colors">Vận
                        hành</a>
                    <a href="#gallery"
                        class="text-sm font-semibold text-slate-600 hover:text-brand-primary uppercase tracking-wide transition-colors">Hình
                        ảnh</a>
                </div>

                <!-- CTA -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="login" class="text-slate-900 font-bold hover:text-brand-primary transition-colors">Đăng
                        nhập</a>
                    <a href="#contact"
                        class="bg-brand-primary hover:bg-blue-700 text-white px-5 py-2.5 rounded font-bold shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-0.5">
                        Thuê kho ngay <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <header class="relative pt-24 pb-20 lg:pt-24 lg:pb-32 overflow-hidden bg-slate-50">
        <!-- Decor element: Simple industrial shape instead of grid -->
        <div class="absolute top-0 right-0 w-[40%] h-full bg-slate-200/50 skew-x-12 translate-x-20"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-16">

                <!-- Text Content -->
                <div class="lg:w-1/2 space-y-8">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded border border-slate-300 bg-white text-slate-600 font-bold text-xs uppercase tracking-wider shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Giải pháp kho bãi Heavy Duty
                    </div>

                    <h1
                        class="font-display text-5xl lg:text-7xl font-bold text-slate-900 leading-[1.1] industrial-text-shadow">
                        QUẢN LÝ KHO <br>
                        <span class="text-brand-primary">HÀNG CÔNG NGHIỆP</span>
                    </h1>

                    <p class="text-lg text-slate-600 leading-relaxed max-w-xl border-l-4 border-brand-accent pl-4">
                        Hệ thống chuyên biệt cho <b>Thép cuộn, Gỗ kiện, Vải cây</b>.
                        Tối ưu xếp dỡ theo Lô (Lot) & Slot, tự động tính toán không gian cho hàng quá khổ.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <button onclick="document.getElementById('simulation').scrollIntoView({behavior: 'smooth'})"
                            class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-4 rounded font-bold text-lg shadow-xl shadow-slate-900/20 transition-all flex items-center gap-3">
                            <i class="fa-solid fa-calculator text-brand-accent"></i> Tính Slot Ngay
                        </button>
                        <button
                            class="bg-white border-2 border-slate-200 hover:border-brand-primary text-slate-700 hover:text-brand-primary px-8 py-4 rounded font-bold text-lg transition-all flex items-center gap-3">
                            <i class="fa-solid fa-download"></i> Tải Báo Giá
                        </button>
                    </div>

                    <!-- Key Metrics -->
                    <div class="pt-8 border-t border-slate-200 flex gap-12">
                        <div>
                            <p class="font-display text-4xl font-bold text-slate-900">500<span
                                    class="text-brand-primary text-2xl">+</span></p>
                            <p class="text-xs text-slate-500 font-bold uppercase">Đối tác tin cậy</p>
                        </div>
                        <div>
                            <p class="font-display text-4xl font-bold text-slate-900">99<span
                                    class="text-brand-primary text-2xl">%</span></p>
                            <p class="text-xs text-slate-500 font-bold uppercase">Chính xác tồn kho</p>
                        </div>
                        <div>
                            <p class="font-display text-4xl font-bold text-slate-900">24/7<span
                                    class="text-brand-primary text-2xl"></span></p>
                            <p class="text-xs text-slate-500 font-bold uppercase">Hỗ trợ tận tình</p>
                        </div>
                    </div>
                </div>

                <!-- Hero Image / Visual -->
                <div class="lg:w-1/2 relative group">
                    <div class="relative rounded-xl overflow-hidden shadow-2xl border-4 border-white h-125">
                        <!-- Ảnh kho thép cuộn / công nghiệp nặng -->
                        <img src="https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80"
                            alt="Steel Warehouse Industrial"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

                        <!-- Overlay Gradient -->
                        <div class="absolute inset-0 bg-linear-to-t from-slate-900/80 via-transparent to-transparent">
                        </div>

                        <!-- Floating Tag 1 -->
                        <div
                            class="absolute top-6 left-6 bg-white/90 backdrop-blur px-4 py-2 rounded shadow border-l-4 border-brand-primary">
                            <p class="text-xs font-bold text-slate-500 uppercase">Khu vực A</p>
                            <p class="text-sm font-bold text-slate-900">Thép Cuộn (Steel Coils)</p>
                        </div>

                        <!-- Floating Tag 2 -->
                        <div class="absolute bottom-8 right-8 bg-brand-accent text-white px-4 py-3 rounded shadow-lg flex items-center gap-3 animate-bounce"
                            style="animation-duration: 3s;">
                            <i class="fa-solid fa-dolly text-xl"></i>
                            <div>
                                <p class="text-xs font-bold uppercase opacity-90">Đang xuất hàng</p>
                                <p class="font-bold">Lô #L9921 - Gỗ Sồi</p>
                            </div>
                        </div>
                    </div>

                    <!-- Decor -->
                    <div class="absolute -z-10 top-6 -right-6 w-full h-full border-2 border-slate-300 rounded-xl"></div>
                </div>
            </div>
        </div>
    </header>

    <!-- CATEGORY GALLERY (Minh họa loại hàng) -->
    <section id="gallery" class="py-12 bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest mb-8">Chúng tôi quản lý tốt
                các loại hàng</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Gỗ -->
                <div class="relative h-48 rounded-lg overflow-hidden group cursor-pointer">
                    <img src="/images/woods.jpg"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        alt="Timber Wood">
                    <div
                        class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                        <h3 class="text-white font-display text-2xl font-bold border-b-2 border-brand-accent pb-1">GỖ &
                            VÁN ÉP</h3>
                    </div>
                </div>
                <!-- Sắt Thép -->
                <div class="relative h-48 rounded-lg overflow-hidden group cursor-pointer">
                    <img src="/images/iron.jpg"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        alt="Steel Pipes">
                    <div
                        class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                        <h3 class="text-white font-display text-2xl font-bold border-b-2 border-brand-accent pb-1">SẮT
                            THÉP</h3>
                    </div>
                </div>
                <!-- Vải -->
                <div class="relative h-48 rounded-lg overflow-hidden group cursor-pointer">
                    <img src="/images/fabric.jpg"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        alt="Fabric Rolls">
                    <div
                        class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                        <h3 class="text-white font-display text-2xl font-bold border-b-2 border-brand-accent pb-1">VẢI
                            CUỘN</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- WAREHOUSE STRUCTURE DIAGRAM (WITH IMAGES) -->
    <section id="solutions" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="font-display text-3xl font-bold text-slate-900">Cấu Trúc Hệ Thống Kho</h2>
                <p class="text-slate-600 mt-2">Mô hình phân tầng thông minh giúp tối ưu hóa luân chuyển hàng hóa cồng
                    kềnh.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-stretch">

                <!-- Transit -->
                <div
                    class="group relative bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="h-40 overflow-hidden">
                        <img src="/images/transit-warehouse.jpg"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                            alt="Transit Area">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-truck-ramp-box text-brand-primary"></i>
                            <h3 class="font-bold text-lg text-slate-900">Kho Trung Chuyển</h3>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase mb-3 border-b pb-2">Luôn có 2 đơn vị</p>
                        <p class="text-sm text-slate-600">Khu vực đệm để nhận hàng (Inbound) và tập kết xuất hàng
                            (Outbound). Giảm ùn tắc cho xe container.</p>
                    </div>
                </div>

                <!-- Small Warehouse (Priority) -->
                <div
                    class="group relative bg-white rounded-xl shadow-lg border-2 border-brand-primary overflow-hidden hover:shadow-2xl transition-all hover:-translate-y-2 z-10">
                    <div
                        class="absolute top-3 right-3 bg-brand-primary text-white text-[10px] font-bold px-2 py-1 rounded shadow z-20">
                        ƯU TIÊN</div>
                    <div class="h-40 overflow-hidden">
                        <!-- Ảnh kho chuyên biệt (chỉ chứa 1 loại hàng) -->
                        <img src="/images/small-warehouse.jpg"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                            alt="Dedicated Warehouse">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-warehouse text-brand-primary"></i>
                            <h3 class="font-bold text-lg text-slate-900">Kho Nhỏ (Chuyên)</h3>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase mb-3 border-b pb-2">200m² / Kho</p>
                        <p class="text-sm text-slate-600">Chỉ chứa <b>duy nhất 1 loại hàng</b> (VD: Chỉ Sắt). Ưu tiên
                            xếp đầy kho này trước để dễ quản lý.</p>
                    </div>
                </div>

                <!-- Overflow/General -->
                <div
                    class="group relative bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="h-40 overflow-hidden">
                        <img src="/images/big-warehouse.jpg"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                            alt="General Warehouse">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-city text-slate-600"></i>
                            <h3 class="font-bold text-lg text-slate-900">Kho Tổng (Dư)</h3>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase mb-3 border-b pb-2">1000m² - Trung bình
                        </p>
                        <p class="text-sm text-slate-600">Nơi chứa các lô hàng "lẻ" không đủ lấp đầy kho nhỏ. Tận dụng
                            tối đa không gian thừa.</p>
                    </div>
                </div>

                <!-- Lots -->
                <div
                    class="group relative bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="h-40 overflow-hidden">
                        <img src="/images/lots.webp"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                            alt="Specific Lot">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-th text-slate-600"></i>
                            <h3 class="font-bold text-lg text-slate-900">Lô (Lots)</h3>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase mb-3 border-b pb-2">50m² / Lô</p>
                        <p class="text-sm text-slate-600">Đơn vị diện tích cơ bản. Một kiện hàng lớn có thể chiếm 1
                            hoặc nhiều Slot trong 1 Lô.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="gallery" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="font-display text-3xl font-bold text-brand-dark mb-4">Hình ảnh thực tế từ kho</h2>
                <p class="text-gray-600">Cơ sở vật chất hiện đại, sạch sẽ và an toàn.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 h-96">
                <div class="md:col-span-2 md:row-span-2 relative group overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1553413077-190dd305871c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1035&q=80"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="Kho 1">
                    <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent flex items-end p-6">
                        <p class="text-white font-bold text-lg">Khu vực kệ cao tầng</p>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1580674285054-bed31e145f59?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="Kho 2">
                </div>
                <div class="relative group overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1565793298595-6a879b1d9492?ixlib=rb-4.0.3&auto=format&fit=crop&w=1471&q=80"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="Kho 3">
                </div>
                <div class="md:col-span-2 relative group overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1507925921958-8a62f3d1a50d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1476&q=80"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="Kho 4">
                    <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent flex items-end p-6">
                        <p class="text-white font-bold text-lg">Đội ngũ vận hành chuyên nghiệp</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SIMULATION SECTION (TÍNH TOÁN SLOT) - CORE FEATURE -->
    <section id="simulation" class="py-20 bg-white border-y border-slate-200">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="text-brand-primary font-bold uppercase tracking-widest text-sm">Công nghệ Smart
                    Slot</span>
                <h2 class="font-display text-3xl lg:text-4xl font-bold text-slate-900 mt-2">Tính Toán Không Gian Lưu
                    Trữ</h2>
                <p class="text-slate-600 mt-3">Nhập kích thước kiện hàng (Gỗ, Vải, Thép) để hệ thống tự động quy đổi ra
                    Slot và loại kho phù hợp.</p>
            </div>

            <div
                class="bg-slate-50 rounded-xl shadow-xl overflow-hidden border border-slate-200 flex flex-col md:flex-row">
                <!-- Input Form -->
                <div class="p-8 md:w-1/2 space-y-6">
                    <h3 class="font-bold text-lg text-slate-800 border-b border-slate-200 pb-2">1. Nhập thông số kiện
                        hàng (m)</h3>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Dài (L)</label>
                            <input type="number" id="input-l"
                                class="w-full border border-slate-300 rounded p-2 focus:ring-2 focus:ring-brand-primary outline-none font-mono"
                                placeholder="0.0" step="0.1">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Rộng (W)</label>
                            <input type="number" id="input-w"
                                class="w-full border border-slate-300 rounded p-2 focus:ring-2 focus:ring-brand-primary outline-none font-mono"
                                placeholder="0.0" step="0.1">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cao (H)</label>
                            <input type="number" id="input-h"
                                class="w-full border border-slate-300 rounded p-2 focus:ring-2 focus:ring-brand-primary outline-none font-mono"
                                placeholder="0.0" step="0.1">
                        </div>
                    </div>

                    <div class="bg-blue-100 p-4 rounded text-xs text-blue-900 flex gap-2">
                        <i class="fa-solid fa-circle-info mt-0.5"></i>
                        <p>Hệ thống sẽ tự động làm tròn lên size tiêu chuẩn gần nhất (Ví dụ: 2.3m -> 2.5m).</p>
                    </div>

                    <button onclick="calculateSlot()"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded transition-colors shadow-lg">
                        Quy đổi ngay
                    </button>
                </div>

                <!-- Result Display -->
                <div
                    class="p-8 md:w-1/2 bg-slate-900 text-white flex flex-col justify-center relative overflow-hidden">
                    <!-- Background image for calculator result -->
                    <div class="absolute inset-0 opacity-10">
                        <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            class="w-full h-full object-cover grayscale">
                    </div>

                    <div id="result-placeholder" class="text-center text-slate-500 relative z-10">
                        <i class="fa-solid fa-cube text-4xl mb-3 opacity-20"></i>
                        <p>Kết quả sẽ hiển thị tại đây</p>
                    </div>

                    <div id="result-content" class="hidden space-y-6 relative z-10">
                        <div>
                            <p class="text-xs font-bold text-brand-primary uppercase">Kích thước quy đổi (Làm tròn)</p>
                            <p class="font-mono text-2xl font-bold text-white mt-1" id="res-dims">-- x -- x --</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/10 p-3 rounded border border-white/10">
                                <p class="text-xs text-slate-400 uppercase">Phân loại Size</p>
                                <p class="font-bold text-brand-accent text-lg" id="res-size-name">--</p>
                            </div>
                            <div class="bg-white/10 p-3 rounded border border-white/10">
                                <p class="text-xs text-slate-400 uppercase">Số Slot quy đổi</p>
                                <p class="font-bold text-white text-lg"><span id="res-slots">--</span> Slots</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-white/10">
                            <p class="text-xs font-bold text-slate-400 uppercase mb-2">Đề xuất lưu trữ:</p>
                            <div class="flex items-center gap-2 text-sm font-semibold text-white">
                                <i class="fa-solid fa-arrow-right-to-bracket text-brand-primary"></i>
                                <span id="res-suggestion">Kho Nhỏ (200m²)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- OPERATIONAL LOGIC (TABBED) -->
    <section id="process" class="py-20 bg-slate-900 text-white overflow-hidden relative">
        <div
            class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

                <div>
                    <h2 class="font-display text-3xl font-bold mb-6 border-l-4 border-brand-primary pl-4">Logic Vận
                        Hành Thông Minh</h2>

                    <div class="space-y-8">
                        <!-- INBOUND -->
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-green-500/20 shrink-0 flex items-center justify-center text-green-500 font-bold border border-green-500/50">
                                IN</div>
                            <div>
                                <h3 class="font-bold text-xl text-white">Quy trình Nhập Kho (Inbound)</h3>
                                <p class="text-slate-400 text-sm mt-1 mb-2">Ưu tiên lấp đầy Kho Nhỏ chuyên biệt.</p>
                                <ul class="text-sm space-y-2 text-slate-300">
                                    <li class="flex items-center"><i
                                            class="fa-solid fa-check text-green-500 mr-2"></i> Hàng vào Kho Trung
                                        Chuyển (Nhận)</li>
                                    <li class="flex items-center"><i
                                            class="fa-solid fa-arrow-right text-brand-primary mr-2"></i> Chuyển vào Kho
                                        Nhỏ (nếu cùng loại & còn chỗ)</li>
                                    <li class="flex items-center"><i
                                            class="fa-solid fa-exclamation-circle text-brand-accent mr-2"></i> Phần dư
                                        lẻ chuyển về Kho Tổng</li>
                                </ul>
                            </div>
                        </div>

                        <!-- OUTBOUND -->
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-brand-primary/20 shrink-0 flex items-center justify-center text-brand-primary font-bold border border-brand-primary/50">
                                OUT</div>
                            <div>
                                <h3 class="font-bold text-xl text-white">Quy trình Xuất Kho (Outbound)</h3>
                                <p class="text-slate-400 text-sm mt-1 mb-2">Ưu tiên FIFO & Giải phóng Kho Tổng.</p>
                                <ul class="text-sm space-y-2 text-slate-300">
                                    <li class="flex items-center"><i
                                            class="fa-solid fa-clock text-brand-primary mr-2"></i> Priority 0: Lấy hàng
                                        gửi lâu nhất (FIFO)</li>
                                    <li class="flex items-center"><i
                                            class="fa-solid fa-box-open text-brand-primary mr-2"></i> Priority 1: Ưu
                                        tiên lấy từ Kho Tổng trước (để dọn sạch)</li>
                                    <li class="flex items-center"><i
                                            class="fa-solid fa-truck-moving text-brand-primary mr-2"></i> Gom hàng ra
                                        Kho Trung Chuyển (Xuất)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visual Stats/Dashboard mockup -->
                <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="font-bold uppercase text-xs tracking-widest text-slate-400">Live Monitor</h4>
                        <div class="flex gap-2">
                            <span class="w-3 h-3 bg-red-500 rounded-full" title="Maintenance"></span>
                            <span class="w-3 h-3 bg-green-500 rounded-full" title="Active"></span>
                            <span class="w-3 h-3 bg-slate-500 rounded-full" title="Locked"></span>
                        </div>
                    </div>

                    <!-- Warehouse Grid Mockup -->
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="bg-brand-primary/80 p-4 rounded text-center">
                            <span class="block text-2xl font-bold">A1</span>
                            <span class="text-[10px]">Kho Nhỏ (Sắt)</span>
                        </div>
                        <div
                            class="bg-brand-primary/40 p-4 rounded text-center border border-brand-primary/50 border-dashed">
                            <span class="block text-2xl font-bold">A2</span>
                            <span class="text-[10px]">Kho Nhỏ (Trống)</span>
                        </div>
                        <div class="bg-slate-700 p-4 rounded text-center relative overflow-hidden">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/50">
                                <i class="fa-solid fa-lock text-white"></i>
                            </div>
                            <span class="block text-2xl font-bold text-slate-500">B1</span>
                            <span class="text-[10px] text-slate-500">Khóa</span>
                        </div>
                        <div
                            class="col-span-3 bg-brand-accent/20 p-4 rounded border border-brand-accent/30 flex justify-between items-center">
                            <div class="text-left">
                                <span class="block text-lg font-bold text-brand-accent">Kho Tổng (Overflow)</span>
                                <span class="text-[10px] text-slate-300">Đang chứa: 15 Lô lẻ</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-white">45%</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-200 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-1">
                    <a href="#" class="flex items-center gap-2 mb-4">
      <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <i class="fa-solid fa-boxes-stacked text-2xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight ">ALOVUA</h1>
                            <p class="text-xs  uppercase tracking-widest font-semibold">Logistics Solution
                            </p>
                        </div>
                    </div>
                    </a>
                    <p class="text-slate-500 text-sm">
                        Giải pháp kho vận hàng đầu cho nguyên vật liệu công nghiệp. Quản lý chính xác, vận hành thông
                        minh.
                    </p>
                </div>

                <div>
                    <h5 class="font-bold text-slate-900 mb-4 uppercase text-xs tracking-wider">Hỗ trợ</h5>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="#" class="hover:text-brand-primary">Trung tâm trợ giúp</a></li>
                        <li><a href="#" class="hover:text-brand-primary">Tài liệu API</a></li>
                        <li><a href="#" class="hover:text-brand-primary">Báo giá dịch vụ</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="font-bold text-slate-900 mb-4 uppercase text-xs tracking-wider">Pháp lý</h5>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="#" class="hover:text-brand-primary">Điều khoản sử dụng</a></li>
                        <li><a href="#" class="hover:text-brand-primary">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-brand-primary">Quy định bồi thường</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="font-bold text-slate-900 mb-4 uppercase text-xs tracking-wider">Liên hệ</h5>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center"><i class="fa-solid fa-phone mr-2 text-brand-primary"></i> 1900
                            8888</li>
                        <li class="flex items-center"><i class="fa-solid fa-envelope mr-2 text-brand-primary"></i>
                            sales@smartwms.vn</li>
                        <li class="flex items-center"><i class="fa-solid fa-location-dot mr-2 text-brand-primary"></i>
                            Khu Công Nghiệp ABC</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-100 mt-12 pt-8 text-center text-sm text-slate-400">
                &copy; 2026 ALOVUA System.. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- JAVASCRIPT LOGIC CHO TÍNH TOÁN SLOT -->
    <script>
        function calculateSlot() {
            // Lấy giá trị input
            let l = parseFloat(document.getElementById('input-l').value);
            let w = parseFloat(document.getElementById('input-w').value);
            let h = parseFloat(document.getElementById('input-h').value);

            // Validate
            if (!l || !w || !h) {
                alert("Vui lòng nhập đầy đủ Dài, Rộng, Cao!");
                return;
            }

            // Logic Quy Đổi (Mô phỏng yêu cầu)
            // Tìm cạnh lớn nhất để quyết định hệ quy chiếu
            let maxDim = Math.max(l, w, h);

            let sizeName = "";
            let roundedL, roundedW, roundedH;
            let slots = 0;

            // Logic làm tròn:
            // Nhỏ: <= 1m
            // Vừa: <= 2m
            // Lớn Vừa: <= 2.5m
            // Lớn Cao: <= 3m
            // Quá khổ: > 3m (Vi phạm)

            if (maxDim <= 1) {
                sizeName = "Cỡ Nhỏ (Small)";
                roundedL = roundedW = roundedH = 1;
                slots = 1;
            } else if (maxDim <= 2) {
                sizeName = "Cỡ Vừa (Medium)";
                roundedL = roundedW = roundedH = 2;
                slots = 3; // Ví dụ quy ước
            } else if (maxDim <= 2.5) {
                sizeName = "Cỡ Lớn Vừa (Large-Medium)";
                roundedL = roundedW = roundedH = 2.5;
                slots = 4; // Ví dụ quy ước
            } else if (maxDim <= 3) {
                sizeName = "Cỡ Lớn Cao (Large-High)";
                roundedL = roundedW = roundedH = 3;
                slots = 6;
            } else {
                alert("CẢNH BÁO: Kích thước vượt quá quy định Max (3m). Không thể nhập kho!");
                return;
            }

            // Hiển thị kết quả
            document.getElementById('result-placeholder').classList.add('hidden');
            document.getElementById('result-content').classList.remove('hidden');

            document.getElementById('res-dims').innerText = `${roundedL}m x ${roundedW}m x ${roundedH}m`;
            document.getElementById('res-size-name').innerText = sizeName;
            document.getElementById('res-slots').innerText = slots;

            // Logic gợi ý kho
            let suggestion = "Kho Nhỏ (200m²)";
            if (slots <= 2) {
                suggestion = "Kho Tổng (Lô lẻ)"; // Hàng nhỏ có thể nhét kho tổng
            }
            document.getElementById('res-suggestion').innerText = suggestion;
        }
    </script>
</body>

</html>
