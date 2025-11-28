<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\Customer;

class ProductImei extends Model
{
    protected $table = 'product_imei';

    protected $fillable = [
        'tenant_id',
        'product_id',
        'imei_number',
        'serial_number',
        'status',
        'purchase_date',
        'sale_date',
        'sold_to_customer_id',
        'warranty_expiry_date',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'sale_date' => 'date',
        'warranty_expiry_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'sold_to_customer_id');
    }
}
