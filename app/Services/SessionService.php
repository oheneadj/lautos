<?php

/**
 * Manages a customer's active sessions (database session driver) — used by
 * the "Active Sessions" list in Security settings and automatically on
 * password reset, so both share one code path.
 *
 * @author Ohene Adjei
 */

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class SessionService
{
    /**
     * Deletes every session row for this user except the one given —
     * deleting a database-driver session row invalidates it immediately,
     * no extra middleware needed.
     */
    public function deleteOtherSessions(User $user, ?string $exceptSessionId): void
    {
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->when($exceptSessionId, fn ($query) => $query->where('id', '!=', $exceptSessionId))
            ->delete();
    }
}
