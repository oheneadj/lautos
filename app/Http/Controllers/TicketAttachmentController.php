<?php

/**
 * @author Ohene Adjei
 */

namespace App\Http\Controllers;

use App\Models\TicketMessage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a support ticket message's attachment. Unlike KycDocumentController
 * and PaymentProofController (admin-only), a ticket is a two-way conversation,
 * so either the ticket's own customer or any admin may view it — only
 * reachable via a signed, short-lived URL either way.
 */
class TicketAttachmentController extends Controller
{
    public function show(TicketMessage $message): StreamedResponse|Response
    {
        $user = Auth::user();
        $isAdmin = $user->is_admin && $user->roles()->exists();

        abort_unless($isAdmin || $message->ticket->user_id === $user->id, 403);

        if (empty($message->attachment_path) || ! Storage::disk('private')->exists($message->attachment_path)) {
            abort(404);
        }

        return Storage::disk('private')->response($message->attachment_path);
    }
}
