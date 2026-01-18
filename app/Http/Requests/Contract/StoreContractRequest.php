<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('contract.create');
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'contract_code' => ['required', 'string', 'max:50', 'unique:contracts,contract_code'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'penalty_markup' => ['nullable', 'numeric', 'min:0', 'max:100'],
            
            // Validate danh sách các Block (Lô) thuê
            'blocks' => ['required', 'array', 'min:1'],
            'blocks.*.id' => ['required', 'exists:storage_blocks,id'],
            'blocks.*.price' => ['required', 'numeric', 'min:0'],
            'blocks.*.slots_committed' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'blocks.required' => 'Phải chọn ít nhất một lô kho để thuê.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ];
    }
}