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
        Schema::create('product_imei', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('imei_number', 50)->unique();
            $table->string('serial_number', 100)->nullable();
            $table->enum('status', ['IN_STOCK', 'SOLD', 'DEFECTIVE', 'RETURNED'])->default('IN_STOCK');
            $table->date('purchase_date')->nullable();
            $table->date('sale_date')->nullable();
            $table->foreignId('sold_to_customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->date('warranty_expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'status'], 'idx_tenant_imei_status');
            $table->index(['tenant_id', 'product_id'], 'idx_tenant_imei_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_imei');
    }
};
