<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\Product;
use App\Models\Inventory\ProductImei;

class ReturnItem extends Model
{
    protected $fillable = [
        'return_id',
        'sale_item_id',
        'product_id',
        'imei_id',
        'quantity',
        'unit_price',
        'total_amount',
        'condition_notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function return()
    {
        return $this->belongsTo(ReturnModel::class, 'return_id');
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function imei()
    {
        return $this->belongsTo(ProductImei::class, 'imei_id');
    }
}
