<?php

namespace App\Http\Requests\Inventory\StockTransfer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_location' => 'required|string|max:100',
            'to_location' => 'required|string|max:100|different:from_location',
            'transfer_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => [
                'required',
                'exists:products,id',
                Rule::exists('products', 'id')->where(function ($query) {
                    $query->where('tenant_id', auth()->user()->tenant_id ?? 1);
                }),
            ],
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'from_location.required' => 'Please specify the source location.',
            'from_location.max' => 'Source location cannot exceed 100 characters.',
            'to_location.required' => 'Please specify the destination location.',
            'to_location.max' => 'Destination location cannot exceed 100 characters.',
            'to_location.different' => 'Source and destination locations must be different.',
            'transfer_date.required' => 'Transfer date is required.',
            'transfer_date.before_or_equal' => 'Transfer date cannot be in the future.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'items.required' => 'At least one item must be added to the transfer.',
            'items.min' => 'At least one item must be added to the transfer.',
            'items.*.product_id.required' => 'Please select a product.',
            'items.*.product_id.exists' => 'Selected product is not valid.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.integer' => 'Quantity must be a valid number.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
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
                $validator->errors()->add('items', 'Duplicate products are not allowed in the same transfer.');
            }

            // Validate stock availability
            foreach ($items as $index => $item) {
                if (isset($item['product_id']) && isset($item['quantity'])) {
                    $product = \App\Models\Inventory\Product::find($item['product_id']);
                    if ($product && $product->current_stock < $item['quantity']) {
                        $validator->errors()->add(
                            "items.{$index}.quantity",
                            "Insufficient stock for {$product->product_name}. Available: {$product->current_stock}, Requested: {$item['quantity']}"
                        );
                    }
                }
            }
        });
    }
}
