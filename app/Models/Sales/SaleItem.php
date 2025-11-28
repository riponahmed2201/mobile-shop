<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\Product;
use App\Models\Inventory\ProductImei;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'imei_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'total_price',
        'warranty_months',
        'warranty_expiry_date',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'warranty_expiry_date' => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
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
