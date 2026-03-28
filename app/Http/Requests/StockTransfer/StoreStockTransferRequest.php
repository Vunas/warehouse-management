<?php

namespace App\Http\Requests\StockTransfer;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_shelf_id' => ['required', 'integer', 'exists:shelves,id'],
            'to_shelf_id'   => ['required', 'integer', 'exists:shelves,id', 'different:from_shelf_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_shelf_id.different' => 'Kệ đến phải khác kệ đi. Không thể luân chuyển hàng trên cùng một kệ.',
        ];
    }
}