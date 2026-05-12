<?php

/**
 * Creates the payment_proofs table — stores customer-uploaded payment evidence
 * for an order. Multiple uploads are allowed per order (e.g. resubmissions).
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
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->index('order_id');

            // Private S3 path — never exposed directly; served via signed URL.
            $table->string('file_path');

            // Optional message from the customer explaining the payment (e.g. split payments).
            $table->string('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
    }
};
