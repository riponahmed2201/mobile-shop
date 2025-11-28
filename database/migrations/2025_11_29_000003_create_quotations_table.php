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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('quotation_number', 50);
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->date('quotation_date');
            $table->date('valid_until_date')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['DRAFT', 'SENT', 'ACCEPTED', 'REJECTED', 'EXPIRED', 'CONVERTED'])->default('DRAFT');
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->foreignId('converted_to_sale_id')->nullable()->constrained('sales')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'quotation_number'], 'idx_tenant_quotation');
            $table->index(['tenant_id', 'status'], 'idx_tenant_quotation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
