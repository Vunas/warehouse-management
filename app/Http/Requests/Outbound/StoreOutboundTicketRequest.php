<?php

namespace App\Http\Requests\Outbound;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutboundTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('outbound.create');
    }

    public function rules(): array
    {
        return [
            'contract_id' => ['required', 'exists:contracts,id'],
            'requested_date' => ['required', 'date', 'after_or_equal:today'],
            
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}