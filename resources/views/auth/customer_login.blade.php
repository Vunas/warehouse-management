<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập khách hàng - WMS PRO</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 max-h-screen w-screen flex items-center justify-center p-4 lg:p-0">

    <div class="w-screen h-screen bg-white overflow-hidden flex flex-col lg:flex-row">

        {{-- LEFT BANNER --}}
        <div class="hidden lg:w-6/12 lg:flex bg-slate-900 text-white p-12 relative">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d"
                class="absolute inset-0 w-full h-full object-cover opacity-40">

            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-4">WMS PRO</h1>
                <p class="text-slate-300">Hệ thống quản lý kho thông minh</p>
            </div>
            <!-- Divider -->
            <div class="my-6 border-t"></div>

            <!-- Terms -->
            <div class="flex justify-center gap-4 text-xs text-slate-500 absolute bottom-10 right-5">
                <a href="/terms" class="hover:text-blue-600 hover:underline text-[20px] font-bold">
                    Điều khoản
                </a>
                <span>|</span>
                <a href="/privacy" class="hover:text-blue-600 hover:underline text-[20px] font-bold">
                    Bảo mật
                </a>
                <span>|</span>
                <a href="/support" class="hover:text-blue-600 hover:underline text-[20px] font-bold">
                    Hỗ trợ
                </a>
            </div>

            <p class="text-center text-xs text-slate-400 mt-3 absolute bottom-4 right-5">
                © {{ date('Y') }} WMS PRO. All rights reserved.
            </p>

        </div>





        {{-- RIGHT FORM --}}
        <div class="w-full lg:w-6/12 p-10 flex items-center">
            <div class="max-w-md w-full mx-auto">

                <h2 class="text-3xl font-bold mb-2">Đăng nhập</h2>
                <p class="text-slate-500 mb-8">Nhập thông tin để truy cập hệ thống</p>

                {{-- ERROR --}}
                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- FORM --}}
                <form action="/customer/login" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold mb-2">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="vd: nguyenvana"
                            class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">Mật khẩu</label>
                        <input type="password" name="password" placeholder="••••••••"
                            class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        <span class="text-sm text-slate-600">Ghi nhớ đăng nhập</span>
                    </div>

                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg">
                        Đăng nhập
                    </button>
                </form>

                <p class="mt-6 text-center text-sm">
                    Chưa có tài khoản?
                    <a href="/customer/register" class="text-blue-600 font-semibold">
                        Đăng ký
                    </a>
                </p>

            </div>
        </div>
    </div>

</body>

</html>