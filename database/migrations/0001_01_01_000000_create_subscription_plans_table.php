<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('plan_name', 100);

            // Plan type (string is more flexible than enum)
            $table->string('plan_type', 30); // FREE, BASIC, PRO, ENTERPRISE

            $table->decimal('price', 10, 2);
            $table->string('billing_cycle', 20); // MONTHLY, QUARTERLY, YEARLY

            // Limits
            $table->integer('max_users')->default(1);
            $table->integer('max_customers')->nullable(); // NULL = unlimited
            $table->integer('max_products')->nullable();
            $table->integer('max_sms_monthly')->default(0);

            // JSON Features
            $table->json('features')->nullable();

            // Additional Recommended Fields
            $table->integer('trial_days')->default(7);
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('plan_type');
            $table->index('billing_cycle');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
