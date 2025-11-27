<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;

class Brand extends Model
{
    protected $fillable = [
        'tenant_id',
        'brand_name',
        'brand_logo_url',
        'is_active'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
