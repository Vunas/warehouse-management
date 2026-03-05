<?php

namespace App\Http\Requests\Inbound;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInboundTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // Cho phép đổi hợp đồng, nhưng phải kiểm tra hợp đồng đó tồn tại
            'contract_id' => ['required', 'exists:contracts,contract_id'], // Chú ý: ERD dùng contract_id làm PK
            'expected_date' => ['required', 'date', 'after_or_equal:today'],
            
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,product_id'], // ERD: product_id
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            
            // Các thông số kích thước nhập (quan trọng cho Calculated Slots sau này)
            'products.*.input_length' => ['required', 'numeric', 'min:0.1'],
            'products.*.input_width' => ['required', 'numeric', 'min:0.1'],
            'products.*.input_height' => ['required', 'numeric', 'min:0.1'],
        ];
    }
}