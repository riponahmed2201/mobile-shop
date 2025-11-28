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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('return_number', 50);
            $table->foreignId('sale_id')->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->date('return_date');
            $table->text('return_reason');
            $table->enum('return_type', ['REFUND', 'EXCHANGE', 'STORE_CREDIT']);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('refund_amount', 12, 2)->default(0.00);
            $table->decimal('restocking_fee', 10, 2)->default(0.00);
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'COMPLETED'])->default('PENDING');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('approval_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'return_number'], 'idx_tenant_return');
            $table->index(['tenant_id', 'status'], 'idx_tenant_return_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
