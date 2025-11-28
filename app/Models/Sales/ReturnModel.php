<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\Customer;
use App\Models\User;

class ReturnModel extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'tenant_id',
        'return_number',
        'sale_id',
        'customer_id',
        'return_date',
        'return_reason',
        'return_type',
        'total_amount',
        'refund_amount',
        'restocking_fee',
        'status',
        'approved_by',
        'approval_notes',
        'processed_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'restocking_fee' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }
}
