<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - ALOVUA</title>

{{ Vite::useBuildDirectory('build') }}
@vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-10 rounded-2xl shadow-xl w-full max-w-md">

    <h2 class="text-3xl font-bold text-center mb-2">Tạo tài khoản</h2>
    <p class="text-center text-slate-500 mb-6">
        Đăng ký để sử dụng hệ thống
    </p>

    {{-- Lỗi --}}
    @if($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-4 text-sm">
            @foreach($errors->all() as $err)
                <p>• {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="/customer/register" class="space-y-4">
        @csrf

        <!-- Username -->
        <div>
            <label class="text-sm font-semibold">Tên đăng nhập *</label>
            <input type="text" name="username"
                value="{{ old('username') }}"
                placeholder="VD: khach123"
                class="w-full border p-3 rounded-lg focus:ring focus:ring-blue-200">
        </div>

        <!-- Full name -->
        <div>
            <label class="text-sm font-semibold">Họ và tên *</label>
            <input type="text" name="full_name"
                value="{{ old('full_name') }}"
                placeholder="Nguyễn Văn A"
                class="w-full border p-3 rounded-lg focus:ring focus:ring-blue-200">
        </div>

        <!-- Email -->
        <div>
            <label class="text-sm font-semibold">
                Email (bắt buộc)
            </label>
            <input type="email" name="email"
                value="{{ old('email') }}"
                placeholder="email@example.com"
                class="w-full border p-3 rounded-lg focus:ring focus:ring-blue-200">
        </div>

        <!-- Password -->
        <div>
            <label class="text-sm font-semibold">Mật khẩu *</label>
            <input type="password" name="password"
                placeholder="Ít nhất 6 ký tự"
                class="w-full border p-3 rounded-lg focus:ring focus:ring-blue-200">
        </div>

        <button
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-bold transition">
            Đăng ký
        </button>
    </form>

    <p class="text-center mt-6 text-sm text-slate-600">
        Đã có tài khoản?
        <a href="/customer/login" class="text-blue-600 font-semibold hover:underline">
            Đăng nhập
        </a>
    </p>

</div>

</body>
</html>
