<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\User;

class Customer extends Model
{
    protected $fillable = [
        'tenant_id',
        'customer_code',
        'full_name',
        'mobile_primary',
        'mobile_alternative',
        'email',
        'address',
        'city',
        'postal_code',
        'date_of_birth',
        'customer_type',
        'total_purchases',
        'total_repairs',
        'loyalty_points',
        'credit_limit',
        'outstanding_balance',
        'notes',
        'is_active',
        'last_purchase_date',
        'last_contact_date',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'total_purchases' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'last_purchase_date' => 'datetime',
        'last_contact_date' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tags()
    {
        return $this->hasMany(CustomerTag::class);
    }

    public function groups()
    {
        return $this->belongsToMany(CustomerGroup::class, 'customer_group_members')
            ->withTimestamps();
    }
}

