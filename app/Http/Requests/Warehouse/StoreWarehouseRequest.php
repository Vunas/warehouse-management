<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'warehouse_code' => ['required', 'string', 'max:20', 'unique:warehouses,warehouse_code'],
            'type_id' => ['required', 'exists:warehouse_types,id'],
            'address' => ['nullable', 'string'],
            
            'total_blocks' => ['required', 'integer', 'min:1'],
            'slots_per_block' => ['required', 'integer', 'min:1'],
        ];
    }
}