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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('product_code', 50)->nullable();
            $table->string('product_name', 200);
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->foreignId('category_id')->constrained('product_categories');
            $table->enum('product_type', ['MOBILE', 'ACCESSORY', 'PARTS'])->default('MOBILE');
            $table->string('model_name', 100)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('storage', 50)->nullable();
            $table->string('ram', 50)->nullable();
            $table->json('specifications')->nullable();
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->integer('warranty_period')->default(0); // in months
            $table->string('warranty_type', 100)->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_level')->default(5);
            $table->integer('reorder_level')->default(10);
            $table->string('unit', 20)->default('PCS');
            $table->string('barcode', 100)->nullable();
            $table->string('product_image_url', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
