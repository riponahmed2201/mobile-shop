<?php

namespace App\Models\Repair;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\Purchase\Supplier;
use App\Models\User;

class RepairPartCatalog extends Model
{
    protected $table = 'repair_part_catalog';

    protected $fillable = [
        'tenant_id',
        'part_code',
        'part_name',
        'description',
        'category',
        'subcategory',
        'brand',
        'compatible_devices',
        'current_stock',
        'min_stock_level',
        'reorder_level',
        'unit',
        'purchase_price',
        'selling_price',
        'mrp',
        'primary_supplier_id',
        'supplier_part_code',
        'location',
        'bin_location',
        'is_active',
        'is_discontinued',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'compatible_devices' => 'array',
        'current_stock' => 'integer',
        'min_stock_level' => 'integer',
        'reorder_level' => 'integer',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'is_active' => 'boolean',
        'is_discontinued' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function primarySupplier()
    {
        return $this->belongsTo(Supplier::class, 'primary_supplier_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relationship with repair parts used in tickets (through the existing repair_parts table)
    public function repairParts()
    {
        return $this->hasMany(RepairPart::class, 'product_id');
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute()
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->current_stock <= $this->min_stock_level) {
            return 'low_stock';
        } elseif ($this->current_stock <= $this->reorder_level) {
            return 'reorder';
        }
        return 'in_stock';
    }

    /**
     * Get stock status label
     */
    public function getStockStatusLabelAttribute()
    {
        return match($this->stock_status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'reorder' => 'Reorder Soon',
            'in_stock' => 'In Stock',
        };
    }

    /**
     * Get stock status badge class
     */
    public function getStockStatusBadgeClassAttribute()
    {
        return match($this->stock_status) {
            'out_of_stock' => 'bg-danger',
            'low_stock' => 'bg-warning',
            'reorder' => 'bg-info',
            'in_stock' => 'bg-success',
        };
    }

    /**
     * Check if part needs reordering
     */
    public function getNeedsReorderAttribute()
    {
        return $this->current_stock <= $this->reorder_level;
    }

    /**
     * Get total value of stock
     */
    public function getStockValueAttribute()
    {
        return $this->current_stock * ($this->purchase_price ?? 0);
    }

    /**
     * Get profit margin percentage
     */
    public function getProfitMarginAttribute()
    {
        if (!$this->purchase_price || !$this->selling_price) {
            return null;
        }

        return (($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100;
    }

    /**
     * Scope for active parts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_discontinued', false);
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= min_stock_level');
    }

    /**
     * Scope for out of stock items
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    /**
     * Scope for items that need reordering
     */
    public function scopeNeedsReorder($query)
    {
        return $query->whereRaw('current_stock <= reorder_level');
    }

    /**
     * Scope for tenant filtering
     */
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id ?? 1;
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope for category filtering
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Auto-generate part code
     */
    public static function generatePartCode()
    {
        $tenantId = auth()->user()->tenant_id ?? 1;
        $date = now()->format('ymd');

        $latest = self::where('tenant_id', $tenantId)
            ->where('part_code', 'like', "RP{$tenantId}{$date}%")
            ->orderBy('part_code', 'desc')
            ->first();

        $sequence = 1;
        if ($latest) {
            $sequence = intval(substr($latest->part_code, -4)) + 1;
        }

        return sprintf("RP%d%s%04d", $tenantId, $date, $sequence);
    }
}
