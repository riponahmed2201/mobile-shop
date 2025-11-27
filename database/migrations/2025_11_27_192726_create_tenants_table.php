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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            // Unique Shop Identifier
            $table->string('tenant_code', 50)->unique();

            // Shop/Business Info
            $table->string('shop_name', 200);
            $table->string('owner_name', 100);
            $table->string('owner_email', 100)->unique()->nullable();
            $table->string('owner_phone', 20)->unique();

            $table->string('business_license', 100)->nullable();
            $table->text('shop_address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->default('Bangladesh');
            $table->string('postal_code', 20)->nullable();

            $table->string('shop_logo')->nullable(); // store only filename

            // Localization
            $table->string('timezone', 50)->default('Asia/Dhaka');
            $table->string('currency', 10)->default('BDT');

            // Subscription
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->enum('subscription_status', ['TRIAL', 'ACTIVE', 'SUSPENDED', 'EXPIRED', 'CANCELLED'])->default('TRIAL');

            $table->date('subscription_start_date')->nullable();
            $table->date('subscription_end_date')->nullable();
            $table->date('trial_ends_at')->nullable();

            // Status Flags
            $table->boolean('is_active')->default(true);

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Key
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
