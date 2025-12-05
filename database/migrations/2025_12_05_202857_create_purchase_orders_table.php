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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('po_number', 50);
            $table->date('po_date');
            $table->date('expected_delivery_date')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('due_amount', 12, 2)->default(0.00);
            $table->enum('payment_status', ['PENDING', 'PARTIAL', 'PAID'])->default('PENDING');
            $table->enum('order_status', ['DRAFT', 'CONFIRMED', 'RECEIVED', 'CANCELLED'])->default('DRAFT');
            $table->string('invoice_file_url', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'po_number'], 'idx_tenant_po_number');
            $table->index(['tenant_id', 'supplier_id'], 'idx_tenant_po_supplier');
            $table->index(['tenant_id', 'order_status'], 'idx_tenant_po_status');
            $table->index(['tenant_id', 'payment_status'], 'idx_tenant_po_payment');
            $table->index(['tenant_id', 'po_date'], 'idx_tenant_po_date');
            $table->unique(['tenant_id', 'po_number'], 'unique_tenant_po_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
