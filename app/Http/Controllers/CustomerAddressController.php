<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Ward;
use App\Models\District;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAddressController extends Controller
{
    /**
     * Hiển thị danh sách địa chỉ
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->with('ward.district.city')->get();
        return view('customer.address.index', compact('addresses'));
    }

    /**
     * Hiển thị form thêm địa chỉ
     */
    public function create()
    {
        $cities = City::all();
        return view('customer.address.create', compact('cities'));
    }

    /**
     * Lưu địa chỉ mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ward_id' => ['required', 'exists:wards,id'],
            'detail' => ['required', 'string', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ], [
            'ward_id.required' => 'Vui lòng chọn phường/xã.',
            'detail.required' => 'Vui lòng nhập chi tiết địa chỉ.',
        ]);

        $userId = Auth::id();
        
        // Nếu đánh dấu là mặc định, bỏ flag mặc định từ các địa chỉ khác
        if ($request->has('is_default') && $request->is_default) {
            Address::where('user_id', $userId)->update(['is_default' => false]);
            $validated['is_default'] = true;
        } else {
            $validated['is_default'] = false;
        }

        Auth::user()->addresses()->create($validated);

        return redirect()->route('customer.address.index')
            ->with('success', 'Thêm địa chỉ thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa
     */
    public function edit(Address $address)
    {
        $this->authorize('update', $address);
        
        $cities = City::all();
        $selectedDistrict = $address->ward->district_id;
        $selectedCity = $address->ward->district->city_id;
        
        return view('customer.address.edit', compact('address', 'cities', 'selectedCity', 'selectedDistrict'));
    }

    /**
     * Cập nhật địa chỉ
     */
    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);
        
        $validated = $request->validate([
            'ward_id' => ['required', 'exists:wards,id'],
            'detail' => ['required', 'string', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $userId = Auth::id();
        
        // Nếu đánh dấu là mặc định, bỏ flag mặc định từ các địa chỉ khác
        if ($request->has('is_default') && $request->is_default) {
            Address::where('user_id', $userId)->where('id', '!=', $address->id)->update(['is_default' => false]);
            $validated['is_default'] = true;
        } else {
            $validated['is_default'] = false;
        }

        $address->update($validated);

        return redirect()->route('customer.address.index')
            ->with('success', 'Cập nhật địa chỉ thành công!');
    }

    /**
     * Xóa địa chỉ
     */
    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);
        
        $address->delete();

        return back()->with('success', 'Đã xóa địa chỉ!');
    }

    /**
     * AJAX: Lấy danh sách districts theo city
     */
    public function getDistricts($cityId)
    {
        $districts = District::where('city_id', $cityId)->get(['id', 'name']);
        return response()->json($districts);
    }

    /**
     * AJAX: Lấy danh sách wards theo district
     */
    public function getWards($districtId)
    {
        $wards = Ward::where('district_id', $districtId)->get(['id', 'name']);
        return response()->json($wards);
    }
}
