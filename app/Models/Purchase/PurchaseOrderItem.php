<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\Product;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'received_quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'received_quantity' => 'integer',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get remaining quantity to receive
     */
    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->received_quantity;
    }

    /**
     * Check if item is fully received
     */
    public function getIsFullyReceivedAttribute()
    {
        return $this->received_quantity >= $this->quantity;
    }

    /**
     * Get received percentage
     */
    public function getReceivedPercentageAttribute()
    {
        if ($this->quantity == 0) return 0;
        return round(($this->received_quantity / $this->quantity) * 100, 1);
    }
}
