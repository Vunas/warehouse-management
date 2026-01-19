<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreSizeRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rule_name' => ['required', 'string', 'max:50'],
            'max_length' => ['required', 'numeric', 'min:0'],
            'max_width' => ['required', 'numeric', 'min:0'],
            'max_height' => ['required', 'numeric', 'min:0'],
            'max_weight' => ['nullable', 'numeric', 'min:0'],
            'slot_cost' => ['required', 'integer', 'min:1'], // Số slot quy đổi
            'priority_level' => ['required', 'integer'], // Độ ưu tiên
            'is_active' => ['boolean'],
        ];
    }
}