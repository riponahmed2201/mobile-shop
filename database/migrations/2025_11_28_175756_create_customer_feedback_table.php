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
        Schema::create('customer_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            // sale_id and repair_ticket_id will be added as foreign keys later when sales and repair_tickets tables are created
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('repair_ticket_id')->nullable();
            $table->enum('feedback_type', ['SALE', 'REPAIR', 'SERVICE', 'GENERAL']);
            $table->tinyInteger('rating'); // Using tinyInteger for 1-5 rating
            $table->text('feedback_text')->nullable();
            $table->boolean('is_public')->default(false);
            $table->text('response_text')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_feedback');
    }
};
