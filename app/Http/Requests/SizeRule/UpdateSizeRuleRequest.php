    <?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSizeRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rule_name' => ['required', 'string', 'max:50'],
            'max_length' => ['required', 'numeric', 'min:0'],
            'max_width' => ['required', 'numeric', 'min:0'],
            'max_height' => ['required', 'numeric', 'min:0'],
            'max_weight' => ['nullable', 'numeric', 'min:0'],
            'slot_cost' => ['required', 'integer', 'min:1'],
            'priority_level' => ['required', 'integer'],
            'is_active' => ['boolean'],
        ];
    }
}