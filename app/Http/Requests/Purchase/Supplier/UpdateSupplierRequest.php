<?php

namespace App\Http\Requests\Purchase\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier')->id;

        return [
            'supplier_name' => 'required|string|max:200',
            'contact_person' => 'nullable|string|max:100',
            'mobile' => [
                'required',
                'string',
                'max:20',
                Rule::unique('suppliers', 'mobile')->where(function ($query) use ($supplierId) {
                    return $query->where('tenant_id', auth()->user()->tenant_id ?? 1)
                                ->where('id', '!=', $supplierId);
                }),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('suppliers', 'email')->where(function ($query) use ($supplierId) {
                    return $query->where('tenant_id', auth()->user()->tenant_id ?? 1)
                                ->where('id', '!=', $supplierId);
                }),
            ],
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'payment_terms' => 'nullable|string|max:200',
            'credit_limit' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_name.required' => 'Supplier name is required.',
            'supplier_name.max' => 'Supplier name cannot exceed 200 characters.',
            'contact_person.max' => 'Contact person name cannot exceed 100 characters.',
            'mobile.required' => 'Mobile number is required.',
            'mobile.max' => 'Mobile number cannot exceed 20 characters.',
            'mobile.unique' => 'This mobile number is already registered.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email cannot exceed 100 characters.',
            'email.unique' => 'This email is already registered.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'city.max' => 'City cannot exceed 100 characters.',
            'country.max' => 'Country cannot exceed 100 characters.',
            'payment_terms.max' => 'Payment terms cannot exceed 200 characters.',
            'credit_limit.numeric' => 'Credit limit must be a valid number.',
            'credit_limit.min' => 'Credit limit cannot be negative.',
        ];
    }
}
