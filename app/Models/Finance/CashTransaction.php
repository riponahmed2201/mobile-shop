<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\User;

class CashTransaction extends Model
{
    const UPDATED_AT = null; // Cash transactions don't update

    protected $fillable = [
        'tenant_id',
        'transaction_date',
        'transaction_type',
        'reference_type',
        'reference_id',
        'amount',
        'payment_method',
        'description',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the reference model (polymorphic-like relationship)
     */
    public function getReference()
    {
        if (!$this->reference_type || !$this->reference_id) {
            return null;
        }

        $modelMap = [
            'sale' => \App\Models\Sales\Sale::class,
            'purchase' => \App\Models\Purchase\PurchaseOrder::class,
            'expense' => Expense::class,
            'payment_collection' => PaymentCollection::class,
            'supplier_payment' => SupplierPayment::class,
        ];

        if (isset($modelMap[$this->reference_type])) {
            return $modelMap[$this->reference_type]::find($this->reference_id);
        }

        return null;
    }

    /**
     * Scope for filtering by transaction type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }
}
