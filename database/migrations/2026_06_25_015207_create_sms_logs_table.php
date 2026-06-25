<?php

/**
 * Creates sms_logs — one row per GiantSMS API call (OTP codes and queued
 * notification sends alike), so a failed delivery can be traced without
 * digging through the application log.
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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('phone');

            // I keep the message body even though it can be re-derived elsewhere —
            // the whole point of this table is to see exactly what we sent.
            $table->text('message');

            // I use a string here and enforce values via the SmsLogStatus enum class.
            $table->string('status');

            // Free-text label for what triggered the send (e.g. "otp", a
            // notification class name) — purely for filtering in the admin list.
            $table->string('context')->nullable();

            $table->unsignedSmallInteger('http_status')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index('phone');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
