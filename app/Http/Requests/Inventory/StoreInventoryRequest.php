<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id'  => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'batch_id'    => 'nullable|exists:product_batches,id',
            'quantity'    => 'required|integer|min:0',
        ];
    }
}
