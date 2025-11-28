<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sale_id' => 'required|exists:sales,id',
            'customer_id' => 'required|exists:customers,id',
            'return_date' => 'required|date',
            'return_reason' => 'required|string',
            'return_type' => 'required|in:REFUND,EXCHANGE,STORE_CREDIT',
            'refund_amount' => 'nullable|numeric|min:0',
            'restocking_fee' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
        ];
    }
}
