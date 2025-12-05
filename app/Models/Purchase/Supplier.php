<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;

class Supplier extends Model
{
    protected $fillable = [
        'tenant_id',
        'supplier_name',
        'contact_person',
        'mobile',
        'email',
        'address',
        'city',
        'country',
        'payment_terms',
        'credit_limit',
        'outstanding_balance',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get total purchase amount from this supplier
     */
    public function getTotalPurchasesAttribute()
    {
        return $this->purchaseOrders()
            ->where('order_status', '!=', 'CANCELLED')
            ->sum('total_amount');
    }

    /**
     * Get pending purchase orders count
     */
    public function getPendingOrdersCountAttribute()
    {
        return $this->purchaseOrders()
            ->whereIn('order_status', ['DRAFT', 'CONFIRMED'])
            ->count();
    }

    /**
     * Get available credit
     */
    public function getAvailableCreditAttribute()
    {
        return $this->credit_limit - $this->outstanding_balance;
    }

    /**
     * Scope for filtering by tenant
     */
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id ?? 1;
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope for active suppliers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
