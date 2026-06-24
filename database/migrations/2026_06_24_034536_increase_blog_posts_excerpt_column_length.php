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
        // I widen this past the default 255 so a 290-character auto-generated
        // excerpt never gets silently truncated by the database.
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('excerpt', 290)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('excerpt')->nullable()->change();
        });
    }
};
