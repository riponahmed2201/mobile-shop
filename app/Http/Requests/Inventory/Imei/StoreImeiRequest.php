<?php

namespace App\Http\Requests\Inventory\Imei;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreImeiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = auth()->user()->tenant_id ?? 1;

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
                Rule::unique('product_imei', 'imei_number')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
            'serial_number' => 'nullable|string|max:100',
            'status' => 'nullable|in:IN_STOCK,SOLD,DEFECTIVE,RETURNED',
            'purchase_date' => 'nullable|date|before_or_equal:today',
            'warranty_expiry_date' => 'nullable|date|after:purchase_date',
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
            'warranty_expiry_date.after' => 'Warranty expiry date must be after purchase date.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }
}
