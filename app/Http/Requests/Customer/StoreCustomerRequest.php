<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string|max:255', 


            'company_name' => 'required|string|max:255',
            'tax_code' => 'required|string|max:50|unique:customers,tax_code',
            'billing_phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ];
    }
}
