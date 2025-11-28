<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\User;

class CustomerFeedback extends Model
{
    protected $table = 'customer_feedback';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'sale_id',
        'repair_ticket_id',
        'feedback_type',
        'rating',
        'feedback_text',
        'is_public',
        'response_text',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_public' => 'boolean',
        'responded_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        // Sale model will be created later
        // return $this->belongsTo(Sale::class);
        return null;
    }

    public function repairTicket()
    {
        // RepairTicket model will be created later
        // return $this->belongsTo(RepairTicket::class);
        return null;
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}

