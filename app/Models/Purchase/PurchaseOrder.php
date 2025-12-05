<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\User;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'po_number',
        'po_date',
        'expected_delivery_date',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'order_status',
        'invoice_file_url',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'po_date' => 'date',
        'expected_delivery_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->order_status) {
            'DRAFT' => 'Draft',
            'CONFIRMED' => 'Confirmed',
            'RECEIVED' => 'Received',
            'CANCELLED' => 'Cancelled',
            default => $this->order_status
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->order_status) {
            'DRAFT' => 'bg-secondary',
            'CONFIRMED' => 'bg-info',
            'RECEIVED' => 'bg-success',
            'CANCELLED' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute()
    {
        return match($this->payment_status) {
            'PENDING' => 'Pending',
            'PARTIAL' => 'Partial',
            'PAID' => 'Paid',
            default => $this->payment_status
        };
    }

    /**
     * Get payment status badge class
     */
    public function getPaymentStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'PENDING' => 'bg-warning',
            'PARTIAL' => 'bg-info',
            'PAID' => 'bg-success',
            default => 'bg-secondary'
        };
    }

    /**
     * Calculate total quantity of all items
     */
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Calculate total received quantity
     */
    public function getTotalReceivedQuantityAttribute()
    {
        return $this->items->sum('received_quantity');
    }

    /**
     * Check if order is fully received
     */
    public function getIsFullyReceivedAttribute()
    {
        return $this->total_quantity > 0 && $this->total_quantity === $this->total_received_quantity;
    }

    /**
     * Get remaining quantity to receive
     */
    public function getRemainingQuantityAttribute()
    {
        return $this->total_quantity - $this->total_received_quantity;
    }

    /**
     * Check if order can be edited
     */
    public function getCanEditAttribute()
    {
        return in_array($this->order_status, ['DRAFT']);
    }

    /**
     * Check if order can be received
     */
    public function getCanReceiveAttribute()
    {
        return in_array($this->order_status, ['CONFIRMED', 'DRAFT']);
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
