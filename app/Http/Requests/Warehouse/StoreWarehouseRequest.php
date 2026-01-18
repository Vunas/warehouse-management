<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('warehouse.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:warehouses,name'],
            'type_id' => ['required', 'exists:warehouse_types,id'],
            
            // Logic tạo nhanh Block
            'total_blocks' => ['required', 'integer', 'min:1', 'max:50'],
            'slots_per_block' => ['required', 'integer', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'type_id.exists' => 'Loại kho không hợp lệ.',
            'total_blocks.max' => 'Số lượng kệ/lô tối đa cho phép tạo nhanh là 50.',
        ];
    }
}