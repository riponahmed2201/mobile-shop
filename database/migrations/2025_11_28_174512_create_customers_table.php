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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('customer_code', 50)->nullable();
            $table->string('full_name', 200);
            $table->string('mobile_primary', 20);
            $table->string('mobile_alternative', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('customer_type', ['NEW', 'REGULAR', 'VIP', 'WHOLESALE'])->default('NEW');
            $table->decimal('total_purchases', 12, 2)->default(0.00);
            $table->integer('total_repairs')->default(0);
            $table->integer('loyalty_points')->default(0);
            $table->decimal('credit_limit', 10, 2)->default(0.00);
            $table->decimal('outstanding_balance', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_purchase_date')->nullable();
            $table->timestamp('last_contact_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['tenant_id', 'mobile_primary'], 'idx_tenant_mobile');
            $table->index(['tenant_id', 'customer_type'], 'idx_customer_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
