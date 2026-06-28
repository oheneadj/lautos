<?php

/**
 * @author Ohene Adjei
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a customer's payment proof to an admin. Mirrors KycDocumentController
 * — only reachable via a signed, short-lived URL, the raw storage path is
 * never exposed. Proofs are stored on the private disk, so the Filament
 * infolist can't just build a Storage::disk('public')->url() for them.
 */
class PaymentProofController extends Controller
{
    public function show(PaymentProof $proof): StreamedResponse|Response
    {
        $admin = Auth::user();
        abort_unless($admin->is_admin && $admin->roles()->exists(), 403);

        if (empty($proof->file_path) || ! Storage::disk('private')->exists($proof->file_path)) {
            abort(404);
        }

        return Storage::disk('private')->response($proof->file_path);
    }
}
