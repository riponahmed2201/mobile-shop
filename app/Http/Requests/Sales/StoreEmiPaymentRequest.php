<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmiPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'installment_id' => 'required|exists:emi_installments,id',
            'paid_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:CASH,CARD,BKASH,NAGAD,BANK',
            'notes' => 'nullable|string',
        ];
    }
}
