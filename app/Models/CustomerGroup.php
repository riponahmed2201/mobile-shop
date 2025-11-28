<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;

class CustomerGroup extends Model
{
    protected $fillable = [
        'tenant_id',
        'group_name',
        'description',
        'discount_percentage',
        'min_purchase_amount',
        'min_purchase_count',
        'color',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_group_members')
            ->withTimestamps();
    }

    public function membersCount()
    {
        return $this->customers()->count();
    }
}

