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
        Schema::create('repair_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('ticket_number', 50)->unique();
            $table->unsignedBigInteger('customer_id');
            $table->string('device_brand', 100)->nullable();
            $table->string('device_model', 100)->nullable();
            $table->string('imei_number', 50)->nullable()->unique();
            $table->text('problem_description');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('final_cost', 10, 2)->nullable();
            $table->decimal('advance_payment', 10, 2)->default(0.00);
            $table->enum('status', ['RECEIVED', 'DIAGNOSED', 'IN_PROGRESS', 'PARTS_PENDING', 'READY', 'DELIVERED', 'CANCELLED'])->default('RECEIVED');
            $table->enum('priority', ['LOW', 'NORMAL', 'HIGH', 'URGENT'])->default('NORMAL');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('received_date')->useCurrent();
            $table->date('estimated_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->boolean('warranty_repair')->default(false);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'assigned_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_tickets');
    }
};
