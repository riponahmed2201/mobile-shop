<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\Customer;
use App\Models\User;

class Sale extends Model
{
    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'customer_id',
        'sale_date',
        'subtotal',
        'discount_amount',
        'discount_percentage',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_method',
        'payment_status',
        'sale_type',
        'sale_status',
        'notes',
        'sold_by',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function soldBy()
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function emiPlan()
    {
        return $this->hasOne(EmiPlan::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnModel::class);
    }

    /**
     * Get total quantity from all items
     */
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }
}
