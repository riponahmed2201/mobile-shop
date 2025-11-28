<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class EmiInstallment extends Model
{
    protected $fillable = [
        'emi_plan_id',
        'installment_number',
        'due_date',
        'amount',
        'paid_amount',
        'payment_date',
        'payment_method',
        'status',
        'late_fee',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function emiPlan()
    {
        return $this->belongsTo(EmiPlan::class);
    }
}
