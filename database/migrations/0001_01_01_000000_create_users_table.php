<?php

/**
 * Creates the core user authentication tables plus all KYC fields required
 * for Ghana customs clearance.
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // I use uuid for all external references — the integer id never leaves the database.
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // KYC fields — ghana_card_number and tin_number are encrypted at rest.
            $table->string('ghana_card_number')->nullable(); // cast to encrypted in model
            $table->string('tin_number')->nullable();        // cast to encrypted in model
            $table->string('ghana_card_path')->nullable();   // private S3 path
            $table->string('tin_path')->nullable();          // private S3 path
            $table->string('kyc_status')->default('pending'); // enforced by KycStatus enum
            $table->text('kyc_notes')->nullable();            // admin feedback on resubmission

            // I store whether the user is an admin — customers are non-admin by default.
            $table->boolean('is_admin')->default(false)->index();

            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
