<?php

namespace App\Http\Requests\Repair\RepairTicket;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRepairTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $ticketId = $this->route('repair_ticket')?->id ?? $this->route('repairTicket')?->id;

        return [
            'customer_id' => 'required|exists:customers,id',
            'device_brand' => 'nullable|string|max:100',
            'device_model' => 'nullable|string|max:100',
            'imei_number' => 'nullable|string|max:50|unique:repair_tickets,imei_number,' . $ticketId,
            'problem_description' => 'required|string|max:1000',
            'estimated_cost' => 'nullable|numeric|min:0|max:999999.99',
            'final_cost' => 'nullable|numeric|min:0|max:999999.99',
            'advance_payment' => 'nullable|numeric|min:0|max:999999.99',
            'priority' => 'required|in:LOW,NORMAL,HIGH,URGENT',
            'estimated_delivery_date' => 'nullable|date',
            'warranty_repair' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists' => 'Selected customer does not exist.',
            'problem_description.required' => 'Problem description is required.',
            'imei_number.unique' => 'This IMEI number is already registered for another repair.',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'customer',
            'device_brand' => 'device brand',
            'device_model' => 'device model',
            'imei_number' => 'IMEI number',
            'problem_description' => 'problem description',
            'estimated_cost' => 'estimated cost',
            'final_cost' => 'final cost',
            'advance_payment' => 'advance payment',
            'estimated_delivery_date' => 'estimated delivery date',
            'warranty_repair' => 'warranty repair',
        ];
    }
}
