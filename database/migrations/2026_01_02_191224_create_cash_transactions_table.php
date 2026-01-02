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
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->timestamp('transaction_date')->useCurrent();
            $table->enum('transaction_type', [
                'SALE', 
                'PURCHASE', 
                'EXPENSE', 
                'PAYMENT_RECEIVED', 
                'PAYMENT_MADE', 
                'OPENING_BALANCE', 
                'ADJUSTMENT'
            ]);
            $table->string('reference_type', 50)->nullable(); // 'sale', 'purchase', 'expense', etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of the related record
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['CASH', 'CARD', 'BKASH', 'NAGAD', 'BANK', 'OTHER'])->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index(['tenant_id', 'transaction_date'], 'idx_tenant_transaction_date');
            $table->index(['tenant_id', 'transaction_type'], 'idx_tenant_transaction_type');
            $table->index(['reference_type', 'reference_id'], 'idx_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
