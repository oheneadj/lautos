<?php

/**
 * Adds a unique slug to makes so the catalogue can filter and link by make
 * using a readable URL value instead of the raw integer id.
 *
 * @author Ohene Adjei
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('makes', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name')->unique();
        });

        foreach (DB::table('makes')->get() as $make) {
            DB::table('makes')->where('id', $make->id)->update(['slug' => Str::slug($make->name)]);
        }
    }

    public function down(): void
    {
        Schema::table('makes', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
