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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('adjustment_type', ['ADD', 'REMOVE', 'DAMAGED', 'LOST', 'FOUND', 'RETURN'])->default('ADD');
            $table->integer('quantity');
            $table->text('reason')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->foreignId('adjusted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('adjustment_date')->useCurrent();

            // Indexes
            $table->index(['tenant_id', 'adjustment_date'], 'idx_tenant_adjustment_date');
            $table->index(['tenant_id', 'product_id'], 'idx_tenant_adjustment_product');
            $table->index(['tenant_id', 'adjustment_type'], 'idx_tenant_adjustment_type');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
