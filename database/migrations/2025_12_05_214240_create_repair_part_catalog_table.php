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
        Schema::create('repair_part_catalog', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');

            // Basic Information
            $table->string('part_code', 50)->unique();
            $table->string('part_name', 200);
            $table->text('description')->nullable();

            // Categorization
            $table->string('category', 100)->nullable();
            $table->string('subcategory', 100)->nullable();
            $table->string('brand', 100)->nullable();

            // Compatibility
            $table->json('compatible_devices')->nullable(); // Array of device models/brands

            // Inventory Management
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_level')->default(5);
            $table->integer('reorder_level')->default(10);
            $table->string('unit', 20)->default('pcs');

            // Pricing
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->decimal('mrp', 10, 2)->nullable();

            // Supplier Information
            $table->unsignedBigInteger('primary_supplier_id')->nullable();
            $table->string('supplier_part_code', 100)->nullable();

            // Location & Storage
            $table->string('location', 100)->nullable(); // Warehouse location
            $table->string('bin_location', 50)->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_discontinued')->default(false);

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('primary_supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'category']);
            $table->index(['tenant_id', 'current_stock']);
            $table->index('part_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_part_catalog');
    }
};
