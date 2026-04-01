<?php

namespace App\Http\Requests\StockTransfer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBulkTransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'items'                  => 'required|array',
            'items.*.inventory_id'   => 'required|exists:inventory,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.to_location_id' => 'nullable|exists:locations,id'
        ];
    }
}