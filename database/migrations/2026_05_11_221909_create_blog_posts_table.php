<?php

/**
 * Creates the blog_posts table — news and import-guide content authored by admins.
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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            // I use uuid for all external references — integer id never leaves the database.
            $table->uuid('uuid')->unique();

            $table->foreignId('blog_category_id')->nullable()->constrained()->nullOnDelete();
            $table->index('blog_category_id');

            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();

            $table->string('title');
            // I use slug for SEO-friendly public URLs.
            $table->string('slug')->unique();
            $table->string('excerpt')->nullable();
            $table->longText('body');
            $table->string('cover_image_path')->nullable();

            // I use a string column and enforce values via the BlogStatus enum.
            $table->string('status')->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
