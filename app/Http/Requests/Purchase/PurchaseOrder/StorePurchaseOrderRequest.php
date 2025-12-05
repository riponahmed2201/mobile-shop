<?php

namespace App\Http\Requests\Purchase\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
                Rule::exists('suppliers', 'id')->where(function ($query) {
                    return $query->where('tenant_id', auth()->user()->tenant_id ?? 1)
                                ->where('is_active', true);
                }),
            ],
            'po_date' => 'required|date|before_or_equal:today',
            'expected_delivery_date' => 'nullable|date|after_or_equal:po_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => [
                'required',
                'exists:products,id',
                Rule::exists('products', 'id')->where(function ($query) {
                    return $query->where('tenant_id', auth()->user()->tenant_id ?? 1);
                }),
            ],
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Please select a supplier.',
            'supplier_id.exists' => 'Selected supplier is not valid.',
            'po_date.required' => 'PO date is required.',
            'po_date.before_or_equal' => 'PO date cannot be in the future.',
            'expected_delivery_date.after_or_equal' => 'Expected delivery date must be on or after PO date.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'items.required' => 'At least one item must be added to the purchase order.',
            'items.min' => 'At least one item must be added to the purchase order.',
            'items.*.product_id.required' => 'Please select a product.',
            'items.*.product_id.exists' => 'Selected product is not valid.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.integer' => 'Quantity must be a valid number.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.unit_price.required' => 'Unit price is required for each item.',
            'items.*.unit_price.numeric' => 'Unit price must be a valid number.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            // Check for duplicate products
            $productIds = array_column($items, 'product_id');
            if (count($productIds) !== count(array_unique($productIds))) {
                $validator->errors()->add('items', 'Duplicate products are not allowed in the same purchase order.');
            }
        });
    }
}
