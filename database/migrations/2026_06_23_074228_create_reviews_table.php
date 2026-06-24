<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // One review per order — this is what makes it a "verified
            // buyer" review and what the post-delivery prompt links back to.
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');
            $table->string('title');
            $table->text('body');

            // I use a string here and enforce values via the ReviewStatus enum class.
            $table->string('status')->default('pending');

            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
