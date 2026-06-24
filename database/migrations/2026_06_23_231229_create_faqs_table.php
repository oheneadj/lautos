<?php

/**
 * Creates the faqs table — lets the admin manage the public FAQ page's
 * questions without touching code.
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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('question');
            $table->text('answer');
            // I use this to control display order on the public page —
            // admins reorder by changing the number rather than relying on created_at.
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
