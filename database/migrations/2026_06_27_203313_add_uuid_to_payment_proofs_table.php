<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // I add this so the new signed payment-proof viewing route never has
        // to expose the integer id in the URL (CLAUDE.md §13) — this table
        // was missed when that rule was first applied everywhere else.
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        DB::table('payment_proofs')->whereNull('uuid')->orderBy('id')->each(function ($proof) {
            DB::table('payment_proofs')->where('id', $proof->id)->update(['uuid' => (string) Str::uuid()]);
        });

        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
