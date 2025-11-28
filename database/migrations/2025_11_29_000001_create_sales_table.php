<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number', 50)->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('sale_date')->useCurrent();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('due_amount', 12, 2)->default(0.00);
            $table->enum('payment_method', ['CASH', 'CARD', 'BKASH', 'NAGAD', 'BANK', 'EMI', 'MIXED'])->nullable();
            $table->enum('payment_status', ['PAID', 'PARTIAL', 'UNPAID'])->default('PAID');
            $table->enum('sale_type', ['RETAIL', 'WHOLESALE', 'EMI'])->default('RETAIL');
            $table->enum('sale_status', ['COMPLETED', 'CANCELLED', 'RETURNED'])->default('COMPLETED');
            $table->text('notes')->nullable();
            $table->foreignId('sold_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'invoice_number'], 'idx_tenant_invoice');
            $table->index(['tenant_id', 'sale_date'], 'idx_tenant_sale_date');
            $table->index(['tenant_id', 'payment_status'], 'idx_tenant_payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
