<?php

/**
 * Creates the orders table — links a customer to a car through the 9-stage pipeline.
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // I use uuid for all external references — integer id never leaves the database.
            $table->uuid('uuid')->unique();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->index('user_id');

            // A car can only have one active order at a time; uniqueness is enforced in the service layer.
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->index('car_id');

            // I use a string column and enforce values via the OrderStatus enum.
            $table->string('status')->default('pending_payment')->index();

            // I snapshot prices at order time so price edits on the car don't affect existing orders.
            $table->unsignedInteger('price_usd_cents');
            $table->unsignedInteger('shipping_cost_usd_cents');

            // Admin-set logistics dates — nullable until the relevant pipeline stage is reached.
            $table->date('estimated_arrival_date')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('vessel_name')->nullable();

            // I keep delivered_at separate from status so fulfilment reports can query it directly.
            $table->timestamp('delivered_at')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
