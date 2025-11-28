<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')->id ?? null;
        $tenantId = auth()->check() ? auth()->user()->tenant_id : 1;

        return [
            'full_name' => 'required|string|max:200',
            'mobile_primary' => 'required|string|max:20|unique:customers,mobile_primary,' . $customerId . ',id,tenant_id,' . $tenantId,
            'mobile_alternative' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'customer_type' => 'nullable|in:NEW,REGULAR,VIP,WHOLESALE',
            'total_purchases' => 'nullable|numeric|min:0',
            'total_repairs' => 'nullable|integer|min:0',
            'loyalty_points' => 'nullable|integer|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'outstanding_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string|max:50',
        ];
    }
}
