<?php

/**
 * Adds a uuid to ticket_messages so attachments can be served through a
 * signed route keyed on the message, not its raw integer id.
 *
 * @author Ohene Adjei
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id')->unique();
        });

        $this->backfillExistingMessages();

        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    /**
     * Generates a uuid for any messages that existed before this column was added.
     */
    private function backfillExistingMessages(): void
    {
        $messages = DB::table('ticket_messages')->whereNull('uuid')->get(['id']);

        foreach ($messages as $message) {
            DB::table('ticket_messages')->where('id', $message->id)->update(['uuid' => (string) Str::uuid()]);
        }
    }

    public function down(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
