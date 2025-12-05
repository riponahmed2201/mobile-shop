<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\User;

class StockTransfer extends Model
{
    protected $fillable = [
        'tenant_id',
        'from_location',
        'to_location',
        'transfer_date',
        'status',
        'notes',
        'transferred_by',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'PENDING' => 'Pending',
            'IN_TRANSIT' => 'In Transit',
            'COMPLETED' => 'Completed',
            'CANCELLED' => 'Cancelled',
            default => $this->status
        };
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'PENDING' => 'bg-warning',
            'IN_TRANSIT' => 'bg-info',
            'COMPLETED' => 'bg-success',
            'CANCELLED' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Get total quantity of all items
     */
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get total received quantity
     */
    public function getTotalReceivedQuantityAttribute()
    {
        return $this->items->sum('received_quantity');
    }

    /**
     * Check if transfer is fully received
     */
    public function getIsFullyReceivedAttribute()
    {
        return $this->total_quantity > 0 && $this->total_quantity === $this->total_received_quantity;
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
