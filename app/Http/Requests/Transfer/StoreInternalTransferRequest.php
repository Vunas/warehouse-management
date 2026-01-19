<?php

namespace App\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;

class StoreInternalTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_block_id' => ['required', 'exists:storage_blocks,id'],
            // Kho đích phải khác kho nguồn
            'to_block_id' => ['required', 'exists:storage_blocks,id', 'different:from_block_id'],
            'trigger_reason' => ['nullable', 'string'],
            
            // Danh sách các Item ID trong Inventory muốn chuyển
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_block_id.different' => 'Kho đích phải khác kho nguồn.',
        ];
    }
}