<?php

namespace App\Models\Repair;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RepairStatusHistory extends Model
{
    protected $fillable = [
        'repair_ticket_id',
        'old_status',
        'new_status',
        'changed_by',
        'notes',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public $timestamps = false;

    public function repairTicket()
    {
        return $this->belongsTo(RepairTicket::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
