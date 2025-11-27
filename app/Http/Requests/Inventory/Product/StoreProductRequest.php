<?php

namespace App\Http\Requests\Inventory\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:200',
            'category_id' => 'required|exists:product_categories,id',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'product_code' => 'nullable|string|max:50',
            'brand_id' => 'nullable|exists:brands,id',
            'product_type' => 'nullable|in:MOBILE,ACCESSORY,PARTS',
            'model_name' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'storage' => 'nullable|string|max:50',
            'ram' => 'nullable|string|max:50',
            'current_stock' => 'nullable|integer',
        ];
    }
}
