<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\User;

class Expense extends Model
{
    protected $fillable = [
        'tenant_id',
        'expense_category_id',
        'expense_date',
        'amount',
        'payment_method',
        'reference_number',
        'description',
        'receipt_file_url',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Boot method to create cash transaction when expense is created
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($expense) {
            // Create cash transaction for this expense
            CashTransaction::create([
                'tenant_id' => $expense->tenant_id,
                'transaction_date' => $expense->expense_date,
                'transaction_type' => 'EXPENSE',
                'reference_type' => 'expense',
                'reference_id' => $expense->id,
                'amount' => $expense->amount,
                'payment_method' => $expense->payment_method,
                'description' => $expense->description ?? 'Expense: ' . $expense->category->category_name,
                'created_by' => $expense->created_by,
            ]);
        });
    }
}
