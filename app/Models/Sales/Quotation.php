<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\Customer;
use App\Models\User;

class Quotation extends Model
{
    protected $fillable = [
        'tenant_id',
        'quotation_number',
        'customer_id',
        'quotation_date',
        'valid_until_date',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'status',
        'notes',
        'terms_conditions',
        'converted_to_sale_id',
        'created_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function convertedSale()
    {
        return $this->belongsTo(Sale::class, 'converted_to_sale_id');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
