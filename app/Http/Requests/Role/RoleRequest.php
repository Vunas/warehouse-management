<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Phân quyền sẽ được check ở Policy
    }

    public function rules(): array
    {
        $roleId = $this->route('role') ? $this->route('role')->id : null;

        return [
            // Tên role phải là duy nhất
            'name' => [
                'required', 
                'string', 
                'max:100', 
                Rule::unique('roles', 'name')->ignore($roleId)
            ],
            // Mảng các quyền (chứa tên quyền)
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên vai trò.',
            'name.unique' => 'Tên vai trò này đã tồn tại.',
            'permissions.*.exists' => 'Quyền không hợp lệ.',
        ];
    }
}