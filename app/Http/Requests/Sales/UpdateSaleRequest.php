<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'sale_date' => 'required|date',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:CASH,CARD,BKASH,NAGAD,BANK,EMI,MIXED',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1', // Usually updates might not allow changing items easily if stock is affected, but keeping consistent with controller logic
        ];
    }
}
