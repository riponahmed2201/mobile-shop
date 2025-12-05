<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    protected $fillable = [
        'stock_transfer_id',
        'product_id',
        'quantity',
        'received_quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'received_quantity' => 'integer',
    ];

    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class);
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
