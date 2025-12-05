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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('supplier_name', 200);
            $table->string('contact_person', 100)->nullable();
            $table->string('mobile', 20);
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('Bangladesh');
            $table->string('payment_terms', 200)->nullable();
            $table->decimal('credit_limit', 12, 2)->default(0.00);
            $table->decimal('outstanding_balance', 12, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'supplier_name'], 'idx_tenant_supplier_name');
            $table->index(['tenant_id', 'is_active'], 'idx_tenant_supplier_active');
            $table->index(['tenant_id', 'outstanding_balance'], 'idx_tenant_supplier_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
