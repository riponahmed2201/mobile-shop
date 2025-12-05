<?php

namespace App\Http\Requests\Inventory\StockAdjustment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = auth()->user()->tenant_id ?? 1;
        $adjustmentId = $this->route('stock_adjustment')->id;

        return [
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::exists('products', 'id')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
            'adjustment_type' => [
                'required',
                'in:ADD,REMOVE,DAMAGED,LOST,FOUND,RETURN'
            ],
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:1000',
            'reference_number' => 'nullable|string|max:100',
            'adjustment_date' => 'nullable|date|before_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'Selected product is not valid.',
            'adjustment_type.required' => 'Please select an adjustment type.',
            'adjustment_type.in' => 'Invalid adjustment type selected.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a valid number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'reason.max' => 'Reason cannot exceed 1000 characters.',
            'reference_number.max' => 'Reference number cannot exceed 100 characters.',
            'adjustment_date.before_or_equal' => 'Adjustment date cannot be in the future.',
        ];
    }
}
