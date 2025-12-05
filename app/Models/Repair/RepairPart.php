<?php

namespace App\Models\Repair;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\Product;

class RepairPart extends Model
{
    protected $fillable = [
        'repair_ticket_id',
        'product_id',
        'part_name',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function repairTicket()
    {
        return $this->belongsTo(RepairTicket::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
