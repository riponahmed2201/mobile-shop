<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'tenant_id',
        'customer_id',
        'transaction_type',
        'points',
        'reference_type',
        'reference_id',
        'description',
        'expiry_date',
    ];

    protected $casts = [
        'points' => 'integer',
        'expiry_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

