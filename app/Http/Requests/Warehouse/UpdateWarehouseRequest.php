<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $warehouseId = $this->route('warehouse')->id ?? $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'warehouse_code' => [
                'required', 'string', 'max:20',
                Rule::unique('warehouses', 'warehouse_code')->ignore($warehouseId)
            ],
            'type_id' => ['required', 'exists:warehouse_types,id'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,maintenance,locked'],
        ];
    }
}