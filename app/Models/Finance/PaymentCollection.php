<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\Customer;
use App\Models\Sales\Sale;
use App\Models\User;

class PaymentCollection extends Model
{
    protected $fillable = [
        'tenant_id',
        'customer_id',
        'sale_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
        'collected_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
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
        return $this->belongsTo(Sale::class);
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    /**
     * Boot method to update customer balance and create cash transaction
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
            // Update customer outstanding balance
            $customer = $payment->customer;
            $customer->outstanding_balance -= $payment->amount;
            $customer->save();

            // Update sale due amount if sale is specified
            if ($payment->sale_id) {
                $sale = $payment->sale;
                $sale->due_amount -= $payment->amount;
                $sale->paid_amount += $payment->amount;
                
                // Update payment status
                if ($sale->due_amount <= 0) {
                    $sale->payment_status = 'PAID';
                } elseif ($sale->paid_amount > 0) {
                    $sale->payment_status = 'PARTIAL';
                }
                
                $sale->save();
            }

            // Create cash transaction
            CashTransaction::create([
                'tenant_id' => $payment->tenant_id,
                'transaction_date' => $payment->payment_date,
                'transaction_type' => 'PAYMENT_RECEIVED',
                'reference_type' => 'payment_collection',
                'reference_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'description' => 'Payment received from ' . $customer->full_name,
                'created_by' => $payment->collected_by,
            ]);
        });
    }
}
