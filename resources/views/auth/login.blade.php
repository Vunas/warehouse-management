<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống - WMS PRO</title>

    {{-- VITE (BẮT BUỘC) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    
    {{-- Google Fonts: Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CDN (Dùng tạm) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Glassmorphism subtle effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        /* Animation cho Input */
        .input-transition {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-slate-50 max-h-screen w-screen flex items-center justify-center p-4 lg:p-0">

    <div class="w-screen h-screen bg-white overflow-hidden flex flex-col lg:flex-row border border-slate-100 relative">
        
        <div class="hidden  lg:w-6/12 lg:flex relative bg-slate-900 overflow-hidden flex-col justify-between p-12 text-white">
            
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" 
                     alt="Warehouse" 
                     class="w-full h-full object-cover opacity-40 mix-blend-overlay">
                <div class="absolute inset-0 bg-linear-to-t from-slate-900 via-blue-900/80 to-slate-900/90"></div>
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-boxes-stacked text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-white">WMS PRO</h1>
                        <p class="text-xs text-blue-200 uppercase tracking-widest font-semibold">Logistics Solution</p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mb-10">
                <h2 class="text-4xl font-bold leading-tight mb-6">
                    Quản lý vận hành kho <br>
                    <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-400 to-cyan-300">
                        Chính xác & Tối ưu
                    </span>
                </h2>
                
                <div class="grid grid-cols-2 gap-6 mt-8">
                    <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10 hover:bg-white/20 transition duration-300">
                        <i class="fa-solid fa-chart-pie text-blue-400 text-xl mb-2"></i>
                        <h4 class="font-semibold text-lg">Real-time Data</h4>
                        <p class="text-xs text-slate-300 mt-1">Cập nhật tồn kho theo thời gian thực.</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10 hover:bg-white/20 transition duration-300">
                        <i class="fa-solid fa-robot text-blue-400 text-xl mb-2"></i>
                        <h4 class="font-semibold text-lg">Smart AI</h4>
                        <p class="text-xs text-slate-300 mt-1">Gợi ý vị trí sắp xếp thông minh.</p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 flex justify-between items-end text-xs text-slate-400 border-t border-white/10 pt-6">
                <p>&copy; {{ date('Y') }} WMS Pro System.</p>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-white transition">Điều khoản</a>
                    <a href="#" class="hover:text-white transition">Bảo mật</a>
                    <a href="#" class="hover:text-white transition">Hỗ trợ</a>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-6/12 bg-white p-8 md:p-16 flex flex-col justify-center relative">
            
            <div class="lg:hidden mb-8 flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-boxes-stacked text-white text-sm"></i>
                </div>
                <span class="font-bold text-slate-800 text-lg">WMS PRO</span>
            </div>

            <div class="max-w-md w-full mx-auto">
                <div class="mb-10">
                    <h3 class="text-3xl font-extrabold text-slate-900 mb-2">Xin chào! 👋</h3>
                    <p class="text-slate-500">Đăng nhập để truy cập hệ thống quản lý.</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    {{-- Thông báo lỗi --}}
                    @if ($errors->any())
                        <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm border border-red-100 flex items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            <div>
                                <span class="font-bold block">Lỗi đăng nhập</span>
                                {{ $errors->first() }}
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 ml-1">Tên đăng nhập / Mã NV</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-regular fa-envelope text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                            </div>
                            <input type="text" name="username" value="{{ old('username') }}" 
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 input-transition" 
                                placeholder="VD: NV001 hoặc email@company.com" required autofocus>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2 ml-1">
                            <label class="block text-sm font-semibold text-slate-700">Mật khẩu</label>
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-semibold hover:underline">Quên mật khẩu?</a>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock-open text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                            </div>
                            <input type="password" name="password" 
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 input-transition" 
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="remember" type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
                        <label for="remember" class="ml-2 block text-sm text-slate-600 cursor-pointer select-none">
                            Ghi nhớ phiên đăng nhập
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-lg shadow-sm shadow-blue-600/30 transition-all duration-300 transform active:scale-[0.98] flex justify-center items-center gap-2">
                        <span>Đăng nhập</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </button>
                </form>

                <div class="relative flex py-8 items-center">
                    <div class="grow border-t border-slate-200"></div>
                    <span class="shrink-0 mx-4 text-slate-400 text-xs font-medium uppercase tracking-wider">Hoặc đăng nhập với</span>
                    <div class="grow border-t border-slate-200"></div>
                </div>

                {{-- <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-3 bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900 py-3.5 rounded-xl transition-all duration-300">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_1_2)">
                        <path d="M23.7663 12.2764C23.7663 11.4607 23.6999 10.6406 23.5588 9.83807H12.2402V14.4591H18.722C18.4528 15.9494 17.5887 17.2678 16.3233 18.1056V21.1039H20.1903C22.4611 19.0139 23.7663 15.9274 23.7663 12.2764Z" fill="#4285F4"/>
                        <path d="M12.24 24.0008C15.4764 24.0008 18.2059 22.9382 20.19 21.1039L16.323 18.1055C15.2516 18.8375 13.8626 19.252 12.2443 19.252C9.11368 19.252 6.45926 17.1399 5.50685 14.3003H1.5164V17.3912C3.55351 21.4434 7.7027 24.0008 12.24 24.0008Z" fill="#34A853"/>
                        <path d="M5.50277 14.3003C5.00209 12.8099 5.00209 11.1961 5.50277 9.70575V6.61481H1.5166C-0.185719 10.0056 -0.185719 14.0004 1.5166 17.3912L5.50277 14.3003Z" fill="#FBBC05"/>
                        <path d="M12.24 4.74966C13.9508 4.7232 15.6042 5.36697 16.8433 6.54867L20.2693 3.12262C18.0999 1.0855 15.2206 -0.0344664 12.24 0.000808666C7.7027 0.000808666 3.55351 2.55822 1.5164 6.61049L5.50257 9.70543C6.45044 6.87142 9.10499 4.74966 12.24 4.74966Z" fill="#EA4335"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_1_2">
                        <rect width="24" height="24" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>
                    <span>Google Workspace</span>
                </a> --}}
                
                <p class="mt-8 text-center text-xs text-slate-400">
                    Bảo mật bởi WMS Pro Security Team. <br>
                </p>

            </div>
        </div>
    </div>

</body>
</html>