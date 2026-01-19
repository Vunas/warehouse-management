<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Contract;

class UpdateContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contractId = $this->route('contract')->id;

        return [
            'customer_id'     => ['required', 'exists:customers,id'],
            'contract_code'   => [
                'required',
                'string',
                'max:50',
                'unique:contracts,contract_code,' . $contractId
            ],
            'start_date'      => ['required', 'date'],
            'end_date'        => ['required', 'date', 'after:start_date'],
            'penalty_markup'  => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
