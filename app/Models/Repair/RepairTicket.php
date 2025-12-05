<?php

namespace App\Models\Repair;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\Customer;
use App\Models\User;

class RepairTicket extends Model
{
    protected $fillable = [
        'tenant_id',
        'ticket_number',
        'customer_id',
        'device_brand',
        'device_model',
        'imei_number',
        'problem_description',
        'estimated_cost',
        'final_cost',
        'advance_payment',
        'status',
        'priority',
        'assigned_to',
        'received_date',
        'estimated_delivery_date',
        'actual_delivery_date',
        'warranty_repair',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'final_cost' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'received_date' => 'datetime',
        'estimated_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'warranty_repair' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parts()
    {
        return $this->hasMany(RepairPart::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(RepairStatusHistory::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(\App\Models\CustomerFeedback::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'RECEIVED' => 'Received',
            'DIAGNOSED' => 'Diagnosed',
            'IN_PROGRESS' => 'In Progress',
            'PARTS_PENDING' => 'Parts Pending',
            'READY' => 'Ready for Delivery',
            'DELIVERED' => 'Delivered',
            'CANCELLED' => 'Cancelled',
            default => $this->status
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'RECEIVED' => 'bg-secondary',
            'DIAGNOSED' => 'bg-info',
            'IN_PROGRESS' => 'bg-warning',
            'PARTS_PENDING' => 'bg-danger',
            'READY' => 'bg-success',
            'DELIVERED' => 'bg-primary',
            'CANCELLED' => 'bg-dark',
            default => 'bg-secondary'
        };
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute()
    {
        return match($this->priority) {
            'LOW' => 'Low',
            'NORMAL' => 'Normal',
            'HIGH' => 'High',
            'URGENT' => 'Urgent',
            default => $this->priority
        };
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClassAttribute()
    {
        return match($this->priority) {
            'LOW' => 'bg-secondary',
            'NORMAL' => 'bg-info',
            'HIGH' => 'bg-warning',
            'URGENT' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Get total parts cost
     */
    public function getTotalPartsCostAttribute()
    {
        return $this->parts->sum('total_price');
    }

    /**
     * Get outstanding balance
     */
    public function getOutstandingBalanceAttribute()
    {
        return ($this->final_cost ?? $this->estimated_cost ?? 0) - $this->advance_payment;
    }

    /**
     * Check if ticket can be edited
     */
    public function getCanEditAttribute()
    {
        return in_array($this->status, ['RECEIVED', 'DIAGNOSED']);
    }

    /**
     * Check if ticket can be assigned
     */
    public function getCanAssignAttribute()
    {
        return !in_array($this->status, ['DELIVERED', 'CANCELLED']);
    }

    /**
     * Check if ticket can be marked as ready
     */
    public function getCanMarkReadyAttribute()
    {
        return in_array($this->status, ['DIAGNOSED', 'IN_PROGRESS']);
    }

    /**
     * Check if ticket can be delivered
     */
    public function getCanDeliverAttribute()
    {
        return $this->status === 'READY';
    }

    /**
     * Get days since received
     */
    public function getDaysSinceReceivedAttribute()
    {
        return $this->received_date->diffInDays(now());
    }

    /**
     * Get device info
     */
    public function getDeviceInfoAttribute()
    {
        $info = [];
        if ($this->device_brand) $info[] = $this->device_brand;
        if ($this->device_model) $info[] = $this->device_model;
        return implode(' ', $info);
    }

    /**
     * Scope for filtering by tenant
     */
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id ?? 1;
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for filtering by assigned technician
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }
}
