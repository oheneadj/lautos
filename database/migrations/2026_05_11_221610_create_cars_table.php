<?php

/**
 * Creates the cars table — the core inventory for Livingston Autos.
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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            // I use uuid for all external references — integer id never leaves the database.
            $table->uuid('uuid')->unique();

            $table->string('make');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->string('engine_capacity');          // e.g. "1800cc"
            $table->string('transmission');             // Automatic / Manual
            $table->string('fuel_type');                // Petrol / Diesel / Hybrid
            $table->unsignedInteger('mileage');         // km
            $table->string('colour');
            $table->string('country_of_origin');        // Korea / Japan

            // I store prices in USD cents to avoid floating point precision issues.
            $table->unsignedInteger('price_usd_cents');
            $table->unsignedInteger('shipping_cost_usd_cents');

            // JSON array of feature strings e.g. ["Sunroof", "Reverse Camera"]
            $table->json('special_features')->nullable();

            // I use a string column and enforce values via the CarStatus enum.
            $table->string('status')->default('available')->index();

            // I keep sold_at separate from status so the 7-day archive window is queryable.
            $table->timestamp('sold_at')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();

            // I index the columns most commonly used in catalogue filters and sorts.
            $table->index(['make', 'model']);
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
