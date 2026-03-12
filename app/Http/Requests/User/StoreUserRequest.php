<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Trả về true nếu mọi user đều có quyền, hoặc thêm logic check permission ở đây
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'username'  => 'required|unique:users',
            'password'  => 'required|min:6',
            'full_name' => 'required|string',
            'email'     => 'required|email|unique:users',
            'phone'     => 'nullable|string',
        ];
    }

    /**
     * Custom messages (Tùy chọn)
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Tên đăng nhập không được để trống.',
            'username.unique'   => 'Tên đăng nhập đã tồn tại.',
            'email.required'    => 'Email không được để trống.',
            'email.email'       => 'Email không đúng định dạng.',
            'email.unique'      => 'Email đã được sử dụng.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ];
    }
}