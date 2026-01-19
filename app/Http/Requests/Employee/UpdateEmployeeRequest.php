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
        $employee = $this->route('employee'); 
        $userId = $employee ? $employee->user_id : null;

        return [
  
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],

            'position' => ['required', 'string'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
            'hired_at' => ['required', 'date'],
            'is_active' => ['boolean'],
        ];
    }
}