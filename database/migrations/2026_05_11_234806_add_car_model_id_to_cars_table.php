<?php

/**
 * Replaces the free-text model column on cars with a FK to car_models.
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
            $table->foreignId('car_model_id')->nullable()->after('make_id')->constrained()->nullOnDelete();
            $table->index('car_model_id');
            $table->dropColumn('model');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['car_model_id']);
            $table->dropColumn('car_model_id');
            $table->string('model')->after('make_id');
        });
    }
};
