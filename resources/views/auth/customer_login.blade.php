<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập khách hàng - WMS PRO</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-10 rounded-2xl shadow-xl w-full max-w-md">

    <h2 class="text-3xl font-bold text-center mb-2">
        Đăng nhập khách hàng
    </h2>

    <p class="text-center text-slate-500 mb-6">
        Vui lòng đăng nhập để tiếp tục
    </p>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="bg-green-50 text-green-600 p-4 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Lỗi --}}
    @if($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-4 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/customer/login" class="space-y-4">
        @csrf

        <!-- Username -->
        <div>
            <label class="text-sm font-semibold">
                Tên đăng nhập
            </label>
            <input type="text" name="username"
                value="{{ old('username') }}"
                placeholder="Nhập username"
                class="w-full border p-3 rounded-lg focus:ring focus:ring-blue-200">
        </div>

        <!-- Password -->
        <div>
            <label class="text-sm font-semibold">
                Mật khẩu
            </label>
            <input type="password" name="password"
                placeholder="Nhập mật khẩu"
                class="w-full border p-3 rounded-lg focus:ring focus:ring-blue-200">
        </div>

        <!-- Remember -->
        <div class="flex items-center">
            <input type="checkbox" name="remember" class="mr-2">
            <span class="text-sm text-slate-600">
                Ghi nhớ đăng nhập
            </span>
        </div>

        <button
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-bold transition">
            Đăng nhập
        </button>
    </form>

    <p class="text-center mt-6 text-sm text-slate-600">
        Chưa có tài khoản?
        <a href="/customer/register"
           class="text-blue-600 font-semibold hover:underline">
            Đăng ký ngay
        </a>
    </p>

</div>

</body>
</html>
