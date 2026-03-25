@extends('layouts.admin')

@section('content')
    <x-crud.form 
        title="Tạo Phiếu Điều Chuyển Kho Mới" 
        action="{{ route('transfers.store') }}" 
        method="POST" 
        cancelRoute="{{ route('transfers.index') }}"
    >
        <div class="md:col-span-2 space-y-6 max-w-2xl">
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                <p class="text-sm text-blue-700 font-medium"><strong>Bước 1:</strong> Chọn vị trí xuất và nhập để khởi tạo phiếu luân chuyển (Chỉ hiển thị vị trí được phép chứa hàng). <br><strong>Bước 2:</strong> Chọn sản phẩm thực tế cần chuyển ở trang sau.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lấy hàng từ Vị trí (From Location) *</label>
                <input type="text" id="search_from" placeholder="🔍 Tìm vị trí xuất..." class="mb-2 block w-full px-4 py-2 border border-gray-300 rounded-lg text-sm" onkeyup="filterSelect('search_from', 'from_location')">
                <select name="from_location_id" id="from_location" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 text-sm">
                    <option value="">-- Chọn vị trí xuất --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->warehouse->name ?? '' }} - {{ $loc->name }} ({{ strtoupper($loc->type) }})</option>
                    @endforeach
                </select>
                @error('from_location_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Chuyển đến Vị trí (To Location) *</label>
                <input type="text" id="search_to" placeholder="🔍 Tìm vị trí nhập..." class="mb-2 block w-full px-4 py-2 border border-gray-300 rounded-lg text-sm" onkeyup="filterSelect('search_to', 'to_location')">
                <select name="to_location_id" id="to_location" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 text-sm">
                    <option value="">-- Chọn vị trí nhập --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->warehouse->name ?? '' }} - {{ $loc->name }} ({{ strtoupper($loc->type) }})</option>
                    @endforeach
                </select>
                @error('to_location_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </x-crud.form>

    <script>
        function filterSelect(inputId, selectId) {
            let filter = document.getElementById(inputId).value.toLowerCase();
            let options = document.getElementById(selectId).options;
            for (let i = 1; i < options.length; i++) {
                let text = options[i].text.toLowerCase();
                options[i].style.display = text.includes(filter) ? '' : 'none';
            }
        }
    </script>
@endsection