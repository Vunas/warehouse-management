<?php

namespace App\Http\Requests\InboundOrder;

use Illuminate\Foundation\Http\FormRequest;

class CompleteInboundOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Mảng ánh xạ [inbound_item_id => shelf_id]
            'shelf_assignments'   => ['required', 'array'],
            'shelf_assignments.*' => ['required', 'integer', 'exists:shelves,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'shelf_assignments.required' => 'Vui lòng chỉ định kệ cho các sản phẩm nhập kho.',
            'shelf_assignments.*.exists' => 'Một trong các kệ được chọn không tồn tại.',
        ];
    }
}