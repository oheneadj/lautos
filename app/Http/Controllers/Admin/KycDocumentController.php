<?php

/**
 * @author Ohene Adjei
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a customer's KYC document to an admin. Only reachable via a
 * signed, short-lived URL — the raw storage path is never exposed.
 */
class KycDocumentController extends Controller
{
    public function show(User $user, string $type): StreamedResponse|Response
    {
        // A valid signature only proves the URL wasn't tampered with — it
        // doesn't prove the holder is allowed to use it. Without this, any
        // logged-in customer who gets hold of a leaked admin link (browser
        // history, a screenshot, a support ticket) could pull someone else's
        // KYC documents. I use the same is_admin + role check canAccessPanel()
        // uses, since that's what actually gates admin access in this app.
        $admin = Auth::user();
        abort_unless($admin->is_admin && $admin->roles()->exists(), 403);

        $path = match ($type) {
            'ghana_card' => $user->ghana_card_path,
            'tin' => $user->tin_path,
            default => abort(404),
        };

        if (empty($path) || ! Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return Storage::disk('private')->response($path);
    }
}
