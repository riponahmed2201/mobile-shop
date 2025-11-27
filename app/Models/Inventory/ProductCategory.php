<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;

class ProductCategory extends Model
{
    protected $fillable = [
        'tenant_id',
        'category_name',
        'category_type',
        'parent_category_id',
        'is_active'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_category_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_category_id');
    }
}
