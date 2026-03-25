<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'username'  => ['required', 'string', 'max:100', Rule::unique('users')->ignore($userId)],
            'email'     => ['required', 'email', 'max:150', Rule::unique('users')->ignore($userId)],
            'password'  => ['nullable', 'string', 'min:6'], 
            'full_name' => ['required', 'string', 'max:150'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'role_name' => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
