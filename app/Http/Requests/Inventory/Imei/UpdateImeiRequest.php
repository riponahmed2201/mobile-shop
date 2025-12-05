<?php

namespace App\Http\Requests\Inventory\Imei;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImeiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = auth()->user()->tenant_id ?? 1;
        $imeiId = $this->route('imei')->id;

        return [
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::exists('products', 'id')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId)
                                ->whereIn('product_type', ['MOBILE', 'ACCESSORY']);
                }),
            ],
            'imei_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('product_imei', 'imei_number')->where(function ($query) use ($tenantId, $imeiId) {
                    return $query->where('tenant_id', $tenantId)
                                ->where('id', '!=', $imeiId);
                }),
            ],
            'serial_number' => 'nullable|string|max:100',
            'status' => 'nullable|in:IN_STOCK,SOLD,DEFECTIVE,RETURNED',
            'purchase_date' => 'nullable|date|before_or_equal:today',
            'sale_date' => 'nullable|date|after_or_equal:purchase_date',
            'warranty_expiry_date' => 'nullable|date|after:purchase_date',
            'sold_to_customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'Selected product is not valid.',
            'imei_number.required' => 'IMEI number is required.',
            'imei_number.unique' => 'This IMEI number already exists in the system.',
            'imei_number.max' => 'IMEI number cannot exceed 50 characters.',
            'serial_number.max' => 'Serial number cannot exceed 100 characters.',
            'status.in' => 'Invalid status selected.',
            'purchase_date.before_or_equal' => 'Purchase date cannot be in the future.',
            'sale_date.after_or_equal' => 'Sale date must be on or after purchase date.',
            'warranty_expiry_date.after' => 'Warranty expiry date must be after purchase date.',
            'sold_to_customer_id.exists' => 'Selected customer is not valid.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }
}
