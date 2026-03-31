@extends('layouts.customer')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Tiêu đề -->
    <div class="mb-8">
        <a href="{{ route('customer.address.index') }}" class="text-blue-600 hover:text-blue-800 font-medium mb-4 inline-block">
            <i class="fa-solid fa-arrow-left mr-2"></i>Quay lại
        </a>
        <h1 class="text-3xl font-black text-gray-800">Sửa Địa Chỉ Giao Hàng</h1>
        <p class="text-gray-600 mt-2">Cập nhật thông tin địa chỉ của bạn</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800 font-bold mb-2"><i class="fa-solid fa-exclamation-circle mr-2"></i>Lỗi:</p>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('customer.address.update', $address) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="city" class="block text-sm font-bold text-gray-700 mb-2">Tỉnh / Thành Phố</label>
                <select id="city" onchange="loadDistricts(this.value)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Chọn Tỉnh / Thành Phố --</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ $city->id == $selectedCity ? 'selected' : '' }}>{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="district" class="block text-sm font-bold text-gray-700 mb-2">Quận / Huyện</label>
                <select id="district" onchange="loadWards(this.value)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Chọn Quận / Huyện --</option>
                    @foreach($address->ward->district->city->districts as $district)
                        <option value="{{ $district->id }}" {{ $district->id == $selectedDistrict ? 'selected' : '' }}>{{ $district->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="ward_id" class="block text-sm font-bold text-gray-700 mb-2">Phường / Xã</label>
                <select id="ward_id" name="ward_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Chọn Phường / Xã --</option>
                    @foreach($address->ward->district->wards as $ward)
                        <option value="{{ $ward->id }}" {{ $ward->id == $address->ward_id ? 'selected' : '' }}>{{ $ward->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="detail" class="block text-sm font-bold text-gray-700 mb-2">Chi Tiết Địa Chỉ</label>
                <textarea name="detail" id="detail" rows="4" placeholder="Nhập số nhà, tên đường, tòa nhà, etc." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>{{ old('detail', $address->detail) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Ví dụ: Tòa nhà A, 123 Đường Nguyễn Huệ</p>
            </div>

            <label class="flex items-center gap-3 p-4 border-2 rounded-lg hover:border-blue-300 cursor-pointer transition {{ $address->is_default ? 'border-blue-300 bg-blue-50' : 'border-gray-200' }}">
                <input type="checkbox" name="is_default" value="1" {{ $address->is_default ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                <span class="font-bold text-gray-800">Đặt làm địa chỉ mặc định</span>
            </label>

            <div class="flex gap-3 pt-4">
                <a href="{{ route('customer.address.index') }}" class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 transition text-center">
                    Hủy
                </a>
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                    <i class="fa-solid fa-save mr-2"></i>Cập Nhật Địa Chỉ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function loadDistricts(cityId) {
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward_id');

        if (!cityId) {
            districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
            wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            return;
        }

        fetch(`/customer/address-api/districts/${cityId}`)
            .then(response => response.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                data.forEach(district => {
                    districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
                });
            });
    }

    function loadWards(districtId) {
        const wardSelect = document.getElementById('ward_id');

        if (!districtId) {
            wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            return;
        }

        fetch(`/customer/address-api/wards/${districtId}`)
            .then(response => response.json())
            .then(data => {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
                data.forEach(ward => {
                    wardSelect.innerHTML += `<option value="${ward.id}">${ward.name}</option>`;
                });
            });
    }
</script>
@endsection
