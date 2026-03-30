<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'username'  => ['required', 'string', 'max:100', 'unique:users,username'],
            'email'     => ['required', 'email', 'max:150', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:6'],
            'full_name' => ['required', 'string', 'max:150'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'role_name' => ['required', 'string', 'exists:roles,name'], 
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique' => 'Tên đăng nhập này đã tồn tại.',
            'email.unique'    => 'Email này đã được sử dụng.',
            'role_name.exists'=> 'Vai trò không hợp lệ.',
        ];
    }
}