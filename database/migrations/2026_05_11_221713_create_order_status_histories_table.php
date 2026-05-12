<?php

/**
 * Creates the order_status_histories table — an append-only audit log of every
 * status transition an order passes through.
 *
 * @author Ohene Adjei
 */

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
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->index('order_id');

            $table->string('status');

            // Nullable because system-driven transitions (e.g. scheduled jobs) have no admin actor.
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();

            // I only store created_at — history rows are never updated.
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
