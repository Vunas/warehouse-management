<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy ID của category đang được update từ URL route
        $categoryId = $this->route('category')->id ?? $this->route('category');

        return [
            'name' => [
                'required', 
                'string', 
                'max:150', 
                Rule::unique('categories')->ignore($categoryId)
            ],
        ];
    }
}