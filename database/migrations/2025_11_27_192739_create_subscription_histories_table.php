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
        Schema::create('subscription_histories', function (Blueprint $table) {

            $table->id();

            // Foreign Keys
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            $table->foreignId('subscription_plan_id')->constrained('subscription_plans');

            // Subscription timeline
            $table->date('start_date');
            $table->date('end_date');

            // Payment details
            $table->decimal('amount_paid', 10, 2);

            // Use string instead of enum (better Laravel practice)
            $table->string('payment_method', 20)->default('CASH'); // CASH, BKASH, NAGAD, BANK, CARD, OTHER

            $table->string('payment_status', 20)->default('PENDING'); // PENDING, COMPLETED, FAILED, REFUNDED

            $table->string('transaction_id', 100)->nullable();
            $table->string('invoice_number', 50)->nullable();

            // Laravel timestamps + soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Indexes for faster reporting
            $table->index('payment_method');
            $table->index('payment_status');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_histories');
    }
};
