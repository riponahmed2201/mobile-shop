<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_code',
        'shop_name',
        'owner_name',
        'owner_email',
        'owner_phone',
        'business_license',
        'shop_address',
        'city',
        'state',
        'country',
        'postal_code',
        'shop_logo',
        'timezone',
        'currency',
        'subscription_plan_id',
        'subscription_status',
        'subscription_start_date',
        'subscription_end_date',
        'trial_ends_at',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'trial_ends_at' => 'date',
    ];

    // Users in this shop/tenant
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Subscription Plan
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    // Created By
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Updated By
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Example for products
    // public function products()
    // {
    //     return $this->hasMany(Product::class);
    // }

    // Orders / Sales
    // public function sales()
    // {
    //     return $this->hasMany(Sale::class);
    // }

    // Purchases
    // public function purchases()
    // {
    //     return $this->hasMany(Purchase::class);
    // }

    // Repairs/Service center
    // public function repairs()
    // {
    //     return $this->hasMany(Repair::class);
    // }

    public function getLogoUrlAttribute()
    {
        return $this->shop_logo ? asset('storage/tenants/' . $this->shop_logo) : asset('images/default-shop.png');
    }
}