<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'tenant_id',
        'category_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get total expenses for this category
     */
    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }
}
