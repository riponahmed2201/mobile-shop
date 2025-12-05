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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('from_location', 100);
            $table->string('to_location', 100);
            $table->date('transfer_date');
            $table->enum('status', ['PENDING', 'IN_TRANSIT', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->text('notes')->nullable();
            $table->foreignId('transferred_by')->nullable()->constrained('users')->onDelete('set null');

            // Indexes
            $table->index(['tenant_id', 'transfer_date'], 'idx_tenant_transfer_date');
            $table->index(['tenant_id', 'status'], 'idx_tenant_transfer_status');
            $table->index(['tenant_id', 'from_location'], 'idx_tenant_from_location');
            $table->index(['tenant_id', 'to_location'], 'idx_tenant_to_location');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
