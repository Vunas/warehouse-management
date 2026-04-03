<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập khách hàng - WMS PRO</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="h-screen flex">

    <!-- LEFT -->
    <div class="hidden lg:flex w-1/2 relative">
        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70"
            class="w-full h-full object-cover">

        <div class="absolute inset-0 bg-black/50 flex flex-col justify-between p-10">
            <div>
                <h1 class="text-3xl font-bold text-white">ALOVUA</h1>
                <p class="text-gray-300 mt-2">Chuyên cung cấp phụ tùng ô tô số lượng lớn</p>
            </div>

            <div class="text-sm text-gray-400 space-x-3">
                <a href="/terms" class="hover:text-white">Điều khoản</a>
                <span>|</span>
                <a href="/privacy" class="hover:text-white">Bảo mật</a>
                <span>|</span>
                <a href="/support" class="hover:text-white">Hỗ trợ</a>

                <p class="mt-3 text-xs">
                    © {{ date('Y') }} ALOVUA
                </p>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-900">
        <div class="w-full max-w-md bg-gray-800 p-8 rounded-xl shadow-xl">

            <h2 class="text-2xl font-bold text-white mb-2">Đăng nhập</h2>
            <p class="text-gray-400 mb-6">Nhập thông tin để truy cập hệ thống</p>

            {{-- ERROR --}}
            @if ($errors->any())
                <div class="bg-red-500/20 text-red-400 p-3 rounded mb-4 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label class="text-gray-300 text-sm">Gmail</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full mt-1 px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-teal-400 outline-none"
                        placeholder="vd: customer@example.com" required>
                </div>

                <!-- Password -->
                <div>
                    <label class="text-gray-300 text-sm">Mật khẩu</label>
                    <input type="password" name="password"
                        class="w-full mt-1 px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-teal-400 outline-none"
                        placeholder="••••••••" required>
                </div>
                <input type="hidden" name="type" value="customer">
                <!-- Remember -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-gray-400">
                        <input type="checkbox" name="remember" class="mr-2">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <!-- Button -->
                <button
                    class="w-full py-2.5 bg-teal-500 hover:bg-teal-600 text-white rounded-lg font-semibold transition">
                    Đăng nhập
                </button>
            </form>

        </div>
    </div>

</body>

</html>