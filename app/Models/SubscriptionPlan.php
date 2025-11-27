<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'plan_name',
        'plan_type',
        'price',
        'billing_cycle',
        'max_users',
        'max_customers',
        'max_products',
        'max_sms_monthly',
        'features',
        'trial_days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    // A plan can belong to many tenants
    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    // Check if feature is enabled
    public function hasFeature($featureKey)
    {
        return isset($this->features[$featureKey]) && $this->features[$featureKey] === true;
    }

    // Human readable cycle
    public function getBillingCycleLabelAttribute()
    {
        return ucfirst(strtolower($this->billing_cycle));
    }

    //     {
    //     "inventory": true,
    //     "sales": true,
    //     "purchase": true,
    //     "repair": true,
    //     "sms": false,
    //     "barcode": true,
    //     "multi_branch": false,
    //     "accounts": false
    // }

    //     if ($tenant->plan->hasFeature('repair')) {
    //     // allow access to repair module
    // }


}
