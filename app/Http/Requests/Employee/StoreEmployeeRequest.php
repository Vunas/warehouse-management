<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {

        return true;
    }

    public function rules(): array
    {
        return [

            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'full_name' => ['required', 'string', 'max:255'],


            'position' => ['required', 'string'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'role_ids' => ['required', 'array'],
            'role_ids.*' => ['exists:roles,id'],
            'hired_at' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique' => 'Tên đăng nhập đã tồn tại.',
            'warehouse_id.exists' => 'Kho được chọn không hợp lệ.',
        ];
    }
}
