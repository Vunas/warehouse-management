<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy ID user từ employee đang được update để ignore unique check
        $employee = $this->route('employee'); 
        $userId = $employee->user_id;

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
            
            'position' => ['required', 'string'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
            'hired_at' => ['required', 'date'],
            'is_active' => ['boolean'],
        ];
    }
}