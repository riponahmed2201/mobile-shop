<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;

class Product extends Model
{
    protected $fillable = [
        'tenant_id',
        'product_code',
        'product_name',
        'brand_id',
        'category_id',
        'product_type',
        'model_name',
        'color',
        'storage',
        'ram',
        'specifications',
        'purchase_price',
        'selling_price',
        'mrp',
        'wholesale_price',
        'warranty_period',
        'warranty_type',
        'current_stock',
        'min_stock_level',
        'reorder_level',
        'unit',
        'barcode',
        'product_image_url',
        'is_active'
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_active' => 'boolean',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function imeiRecords()
    {
        return $this->hasMany(ProductImei::class, 'product_id');
    }
}
