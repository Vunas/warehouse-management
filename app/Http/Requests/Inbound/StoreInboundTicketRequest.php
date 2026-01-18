<?php

namespace App\Http\Requests\Inbound;

use Illuminate\Foundation\Http\FormRequest;

class StoreInboundTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('inbound.create');
    }

    public function rules(): array
    {
        return [
            'contract_id' => ['required', 'exists:contracts,id'],
            'expected_date' => ['required', 'date', 'after_or_equal:today'],
            
            // Validate danh sách hàng hóa nhập
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            
            // Kích thước bắt buộc để tính Slot (Quy đổi)
            'products.*.input_length' => ['required', 'numeric', 'min:0.1'],
            'products.*.input_width' => ['required', 'numeric', 'min:0.1'],
            'products.*.input_height' => ['required', 'numeric', 'min:0.1'],
        ];
    }

    public function attributes()
    {
        return [
            'products.*.input_length' => 'Chiều dài',
            'products.*.input_width' => 'Chiều rộng',
            'products.*.input_height' => 'Chiều cao',
        ];
    }
}