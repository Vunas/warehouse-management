<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customer = $this->route('customer');
        $customerId = $customer ? $customer->id : null;
        $userId = $customer ? $customer->user_id : null;

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'is_active' => ['boolean'],

            'company_name' => ['required', 'string', 'max:255'],
            'tax_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('customers', 'tax_code')->ignore($customerId)
            ],
            'billing_phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ];
    }
}
