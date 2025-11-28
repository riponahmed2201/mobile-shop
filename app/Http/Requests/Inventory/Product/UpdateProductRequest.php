<?php

namespace App\Http\Requests\Inventory\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'product_code' => 'nullable|string|max:50',
            'brand_id' => 'nullable|exists:brands,id',
            'product_type' => 'nullable|in:MOBILE,ACCESSORY,PARTS',
            'model_name' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'storage' => 'nullable|string|max:50',
            'ram' => 'nullable|string|max:50',
            'specifications' => 'nullable|string',
            'mrp' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'warranty_period' => 'nullable|integer|min:0',
            'warranty_type' => 'nullable|string|max:100',
            'current_stock' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:20|in:PCS,BOX,SET,PAIR,KG,LITER',
            'barcode' => 'nullable|string|max:100',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ];
    }
}
