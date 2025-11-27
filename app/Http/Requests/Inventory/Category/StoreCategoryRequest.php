<?php

namespace App\Http\Requests\Inventory\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_name' => 'required|string|max:100',
            'category_type' => 'required|in:MOBILE,ACCESSORY,PARTS,OTHER',
            'parent_category_id' => 'nullable|exists:product_categories,id',
        ];
    }
}
