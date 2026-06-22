<?php

/**
 * @author Ohene Adjei
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
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
        $path = match ($type) {
            'ghana_card' => $user->ghana_card_path,
            'tin' => $user->tin_path,
            default => abort(404),
        };

        if (empty($path) || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }
}
