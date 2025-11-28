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
        Schema::create('emi_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_id')->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('down_payment', 10, 2);
            $table->decimal('installment_amount', 10, 2);
            $table->integer('number_of_installments');
            $table->decimal('interest_rate', 5, 2)->default(0.00);
            $table->date('start_date');
            $table->integer('paid_installments')->default(0);
            $table->decimal('remaining_amount', 12, 2);
            $table->enum('status', ['ACTIVE', 'COMPLETED', 'DEFAULTED', 'CANCELLED'])->default('ACTIVE');
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'status'], 'idx_tenant_emi_status');
            $table->index(['tenant_id', 'customer_id'], 'idx_tenant_emi_customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emi_plans');
    }
};
