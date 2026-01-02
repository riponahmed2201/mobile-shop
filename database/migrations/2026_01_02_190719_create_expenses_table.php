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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('expense_category_id')->constrained()->onDelete('cascade');
            $table->date('expense_date');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['CASH', 'CARD', 'BANK', 'BKASH', 'NAGAD', 'OTHER'])->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('receipt_file_url', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'expense_date'], 'idx_tenant_expense_date');
            $table->index(['tenant_id', 'expense_category_id'], 'idx_tenant_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
