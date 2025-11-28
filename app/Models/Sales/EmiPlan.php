<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\Customer;

class EmiPlan extends Model
{
    protected $fillable = [
        'tenant_id',
        'sale_id',
        'customer_id',
        'total_amount',
        'down_payment',
        'installment_amount',
        'number_of_installments',
        'interest_rate',
        'start_date',
        'paid_installments',
        'remaining_amount',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'start_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function installments()
    {
        return $this->hasMany(EmiInstallment::class);
    }
}
