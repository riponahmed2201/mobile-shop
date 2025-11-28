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
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('group_name', 100);
            $table->text('description')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('min_purchase_amount', 10, 2)->nullable();
            $table->integer('min_purchase_count')->nullable();
            $table->string('color', 20)->nullable(); // For UI display
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'group_name'], 'unique_group_tenant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_groups');
    }
};
