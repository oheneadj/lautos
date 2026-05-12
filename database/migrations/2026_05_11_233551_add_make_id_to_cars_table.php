<?php

/**
 * Replaces the free-text make column on cars with a foreign key to the makes table.
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
            $table->foreignId('make_id')->nullable()->after('uuid')->constrained()->nullOnDelete();
            $table->index('make_id');
            // I drop the old composite index before removing the make column — SQLite errors otherwise.
            $table->dropIndex('cars_make_model_index');
            $table->dropColumn('make');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['make_id']);
            $table->dropColumn('make_id');
            $table->string('make')->after('uuid');
        });
    }
};
