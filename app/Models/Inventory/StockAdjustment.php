<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\User;

class StockAdjustment extends Model
{
    protected $fillable = [
        'tenant_id',
        'product_id',
        'adjustment_type',
        'quantity',
        'reason',
        'reference_number',
        'adjusted_by',
        'adjustment_date',
    ];

    protected $casts = [
        'adjustment_date' => 'datetime',
        'quantity' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function adjustedBy()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    /**
     * Get the adjustment type label
     */
    public function getAdjustmentTypeLabelAttribute()
    {
        return match($this->adjustment_type) {
            'ADD' => 'Stock Added',
            'REMOVE' => 'Stock Removed',
            'DAMAGED' => 'Damaged',
            'LOST' => 'Lost',
            'FOUND' => 'Found',
            'RETURN' => 'Returned',
            default => $this->adjustment_type
        };
    }

    /**
     * Get the quantity with sign based on adjustment type
     */
    public function getSignedQuantityAttribute()
    {
        return match($this->adjustment_type) {
            'ADD', 'FOUND', 'RETURN' => '+' . $this->quantity,
            'REMOVE', 'DAMAGED', 'LOST' => '-' . $this->quantity,
            default => $this->quantity
        };
    }

    /**
     * Scope for filtering by tenant
     */
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id ?? 1;
        return $query->where('tenant_id', $tenantId);
    }
}
