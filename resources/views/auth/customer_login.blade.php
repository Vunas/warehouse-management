<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Đăng nhập đối tác | CoreParts</title>

    {{ Vite::useBuildDirectory('build') }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .grid-pattern {
            background-image:
                linear-gradient(to right, rgba(15, 23, 42, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(15, 23, 42, 0.04) 1px, transparent 1px);
            background-size: 36px 36px;
        }

        .glow {
            background: radial-gradient(circle at center,
                    rgba(59, 130, 246, 0.18),
                    transparent 65%);
            filter: blur(60px);
        }
    </style>
</head>

<body class="min-h-screen bg-white text-slate-900 antialiased overflow-hidden">

    <div class="flex min-h-screen">

        <!-- LEFT -->
        <div class="w-full lg:w-[46%] relative flex flex-col justify-center px-8 sm:px-14 lg:px-20 xl:px-28 bg-white">

            <!-- subtle background -->
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-30 -left-30 w-[320px] h-80 glow"></div>
            </div>

            <!-- logo -->
            <div class="absolute top-8 left-8 sm:top-10 sm:left-14 lg:left-20 xl:left-28">
                <a href="/" class="flex items-center gap-2">
                    <div
                        class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                        C
                    </div>

                    <span class="text-xl font-semibold tracking-tight">
                        CoreParts
                    </span>
                </a>
            </div>

            <!-- form -->
            <div class="relative z-10 w-full max-w-md">

                <!-- heading -->
                <div class="mb-10">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-slate-200 bg-slate-50 text-xs font-medium text-slate-600 mb-5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Hệ thống đối tác phân phối
                    </div>

                    <h1 class="text-4xl font-semibold tracking-tight text-slate-950 leading-tight">
                        Đăng nhập vào
                        <span class="text-blue-600">CoreParts</span>
                    </h1>

                    <p class="mt-3 text-slate-500 leading-relaxed">
                        Quản lý đơn hàng, bảng giá sỉ và theo dõi hoạt động phân phối
                        trong một nền tảng thống nhất.
                    </p>
                </div>

                <!-- error -->
                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- form -->
                <form action="/login" method="POST" class="space-y-5">
                    @csrf

                    <input type="hidden" name="type" value="customer">

                    <!-- email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                            Email
                        </label>

                        <div class="relative">
                            <input type="email" name="email" id="email"
                                value="{{ old('email', 'customer@example.com') }}" required autofocus
                                class="w-full h-12 rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition-all duration-200 focus:border-slate-900 focus:ring-4 focus:ring-slate-100"
                                placeholder="name@company.com">

                            <div class="absolute inset-y-0 right-4 flex items-center text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12H8m0 0l3-3m-3 3l3 3" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-medium text-slate-700">
                                Mật khẩu
                            </label>

                        </div>

                        <div class="relative">
                            <input type="password" name="password" id="password"
                                value="{{ old('password', '123456') }}" required
                                class="w-full h-12 rounded-2xl border border-slate-200 bg-white px-4 pr-12 text-sm text-slate-900 outline-none transition-all duration-200 focus:border-slate-900 focus:ring-4 focus:ring-slate-100"
                                placeholder="••••••••">

                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-slate-700 transition-colors">

                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- remember -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-3 text-sm text-slate-600 cursor-pointer">
                            <input type="checkbox"
                                class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">

                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                    <!-- submit -->
                    <button type="submit"
                        class="group relative w-full h-12 overflow-hidden rounded-2xl bg-slate-900 text-sm font-medium text-white transition-all duration-300 hover:bg-slate-800">

                        <span
                            class="absolute inset-0 bg-linear-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></span>

                        <span class="relative z-10">
                            Đăng nhập hệ thống
                        </span>
                    </button>
                </form>

                <!-- footer -->
                <div class="mt-10 flex items-center gap-4 text-xs text-slate-400">
                    <span>© 2026 CoreParts</span>

                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>

                    <a href="#" class="hover:text-slate-600 transition-colors">
                        Điều khoản
                    </a>

                    <a href="#" class="hover:text-slate-600 transition-colors">
                        Bảo mật
                    </a>
                </div>
            </div>
        </div>

        <!-- RIGHT -->
        <div
            class="hidden lg:flex flex-1 relative overflow-hidden border-l border-slate-200 bg-linear-to-br from-slate-50 via-white to-blue-50">

            <!-- grid -->
            <div class="absolute inset-0 grid-pattern"></div>

            <!-- glow -->
            <div class="absolute top-[10%] right-[10%] w-105 h-105 rounded-full bg-blue-200/30 blur-3xl">
            </div>

            <!-- content -->
            <div class="relative z-10 flex flex-col justify-center px-20 xl:px-28 max-w-2xl">

                <!-- badge -->
                <div
                    class="mb-6 inline-flex w-fit items-center gap-2 rounded-full border border-blue-100 bg-white/80 backdrop-blur px-4 py-2 text-sm font-medium text-slate-700 shadow-sm">

                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse">
                    </div>

                    Hệ thống hoạt động ổn định
                </div>

                <!-- heading -->
                <h2 class="text-5xl font-semibold tracking-tight leading-[1.1] text-slate-950">
                    Nền tảng quản lý
                    phân phối hiện đại
                    dành cho đại lý.
                </h2>

                <!-- desc -->
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-500">
                    Theo dõi đơn hàng, quản lý công nợ, cập nhật bảng giá
                    theo thời gian thực và vận hành hệ thống phân phối
                    hiệu quả hơn.
                </p>

                <!-- cards -->
                <div class="mt-12 grid grid-cols-2 gap-5">

                    <div class="rounded-3xl border border-white/70 bg-white/70 backdrop-blur p-6 shadow-sm">
                        <div class="text-3xl font-semibold tracking-tight">
                            500+
                        </div>

                        <div class="mt-2 text-sm text-slate-500">
                            Đại lý đang hoạt động
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/70 bg-white/70 backdrop-blur p-6 shadow-sm">
                        <div class="text-3xl font-semibold tracking-tight">
                            99.9%
                        </div>

                        <div class="mt-2 text-sm text-slate-500">
                            Thời gian uptime hệ thống
                        </div>
                    </div>
                </div>

                <!-- logos -->
                <div class="mt-14">
                    <p class="mb-5 text-sm font-medium text-slate-500">
                        Công nghệ vận hành
                    </p>

                    <div class="flex items-center gap-8 opacity-60 grayscale">

                        <img src="https://upload.wikimedia.org/wikipedia/commons/a/a7/React-icon.svg"
                            class="h-8 object-contain" alt="React">

                        <img src="https://upload.wikimedia.org/wikipedia/commons/d/d9/Node.js_logo.svg"
                            class="h-8 object-contain" alt="NodeJS">

                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/27/PHP-logo.svg"
                            class="h-8 object-contain" alt="PHP">

                        <img src="https://upload.wikimedia.org/wikipedia/commons/9/96/Sass_Logo_Color.svg"
                            class="h-8 object-contain" alt="Sass">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');

            if (input.type === 'password') {
                input.type = 'text';

                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M3 3l18 18" />
                `;
            } else {
                input.type = 'password';

                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>
</body>

</html>
