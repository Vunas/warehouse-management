<?php

namespace App\Http\Requests\Outbound;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutboundOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'warehouse_id'       => 'required|exists:warehouses,id',
            'type'               => 'required|in:sales,internal,adjustment',
            'order_id'           => 'required_if:type,sales|nullable|exists:orders,id',
            'reason'             => 'nullable|string|max:255',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.location_id'=> 'required|exists:locations,id',
            'items.*.batch_id'   => 'nullable|exists:product_batches,id',
            'items.*.quantity'   => 'required|numeric|min:1',
        ];
    }
}