<?php

/**
 * Creates the makes table — the canonical list of car manufacturers with their brand icons.
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
        Schema::create('makes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon_path')->nullable(); // public disk path for the brand logo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('makes');
    }
};
