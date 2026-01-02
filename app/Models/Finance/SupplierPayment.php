<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenancy\Tenant;
use App\Models\Purchase\Supplier;
use App\Models\Purchase\PurchaseOrder;
use App\Models\User;

class SupplierPayment extends Model
{
    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'purchase_order_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
        'paid_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Boot method to update supplier balance and create cash transaction
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
            // Update supplier outstanding balance
            $supplier = $payment->supplier;
            $supplier->outstanding_balance -= $payment->amount;
            $supplier->save();

            // Update purchase order due amount if PO is specified
            if ($payment->purchase_order_id) {
                $po = $payment->purchaseOrder;
                $po->due_amount -= $payment->amount;
                $po->paid_amount += $payment->amount;
                
                // Update payment status
                if ($po->due_amount <= 0) {
                    $po->payment_status = 'PAID';
                } elseif ($po->paid_amount > 0) {
                    $po->payment_status = 'PARTIAL';
                }
                
                $po->save();
            }

            // Create cash transaction
            CashTransaction::create([
                'tenant_id' => $payment->tenant_id,
                'transaction_date' => $payment->payment_date,
                'transaction_type' => 'PAYMENT_MADE',
                'reference_type' => 'supplier_payment',
                'reference_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'description' => 'Payment made to ' . $supplier->supplier_name,
                'created_by' => $payment->paid_by,
            ]);
        });
    }
}
