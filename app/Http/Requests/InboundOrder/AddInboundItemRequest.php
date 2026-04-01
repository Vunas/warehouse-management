<?php

namespace App\Http\Requests\InboundOrder;

use Illuminate\Foundation\Http\FormRequest;

class AddInboundItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'price'      => 'required|numeric|min:0',
            'batch_id'   => 'nullable|exists:product_batches,id',
        ];
    }
}