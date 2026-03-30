<?php

namespace App\Http\Requests\InboundOrder;

use Illuminate\Foundation\Http\FormRequest;

class StoreInboundOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'supplier_id.exists'   => 'Nhà cung cấp không tồn tại trong hệ thống.',
        ];
    }
}