<?php

namespace App\Http\Requests\InboundOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInboundItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quantity' => 'required|integer|min:1',
            'price'    => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.min'      => 'Số lượng phải lớn hơn hoặc bằng 1.',
            'price.required'    => 'Vui lòng nhập đơn giá.',
            'price.min'         => 'Đơn giá không được âm.',
        ];
    }
}