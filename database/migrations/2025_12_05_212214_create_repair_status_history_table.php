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
        Schema::create('repair_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('repair_ticket_id');
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('changed_at')->useCurrent();

            $table->foreign('repair_ticket_id')->references('id')->on('repair_tickets')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');

            $table->index('changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_status_history');
    }
};
