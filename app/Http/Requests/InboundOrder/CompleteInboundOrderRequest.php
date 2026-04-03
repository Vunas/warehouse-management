<?php

namespace App\Http\Requests\InboundOrder;

use Illuminate\Foundation\Http\FormRequest;

class CompleteInboundOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'assignments'                      => 'required|array',
            'assignments.*.location_id'        => 'required|exists:locations,id',
            'assignments.*.batch_code'         => 'nullable|string|max:100',
            'assignments.*.manufacture_date'   => 'nullable|date',
            'assignments.*.expiry_date'        => 'nullable|date|after_or_equal:assignments.*.manufacture_date',
            'assignments.*.batch_id' => 'nullable|exists:product_batches,id',

        ];
    }

    public function messages()
    {
        return [
            'assignments.*.location_id.required'         => 'Vui lòng chọn kệ lưu trữ cho tất cả sản phẩm.',
            'assignments.*.expiry_date.after_or_equal'   => 'Hạn sử dụng phải lớn hơn hoặc bằng Ngày sản xuất.'
        ];
    }
}
