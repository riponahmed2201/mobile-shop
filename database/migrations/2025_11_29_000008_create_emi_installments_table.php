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
        Schema::create('emi_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emi_plan_id')->constrained()->onDelete('cascade');
            $table->integer('installment_number');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->date('payment_date')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->enum('status', ['PENDING', 'PAID', 'OVERDUE', 'WAIVED'])->default('PENDING');
            $table->decimal('late_fee', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['emi_plan_id', 'status'], 'idx_emi_installment_status');
            $table->index(['emi_plan_id', 'due_date'], 'idx_emi_installment_due');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emi_installments');
    }
};
