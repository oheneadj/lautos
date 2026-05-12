<?php

/**
 * Adds an optional car_trim_id FK to cars — trims are not mandatory since not all
 * listings will have a known trim level.
 *
 * @author Ohene Adjei
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->foreignId('car_trim_id')->nullable()->after('car_model_id')->constrained()->nullOnDelete();
            $table->index('car_trim_id');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['car_trim_id']);
            $table->dropColumn('car_trim_id');
        });
    }
};
