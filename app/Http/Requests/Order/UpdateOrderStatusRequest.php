<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Phải nằm trong list ENUM của DB
            'status' => ['required', 'string', 'in:pending,paid,shipping,completed,cancelled'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Trạng thái đơn hàng không hợp lệ.',
        ];
    }
}