<?php

/**
 * Adds a status to each payment proof, so an admin's accept/reject decision
 * is recorded against the specific proof reviewed, not just inferred from
 * the order's current stage.
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
        Schema::table('payment_proofs', function (Blueprint $table) {
            // I use a string here and enforce values via the PaymentProofStatus enum class.
            $table->string('status')->default('pending')->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
