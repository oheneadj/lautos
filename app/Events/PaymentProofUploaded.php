<?php

/**
 * @author Ohene Adjei
 */

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;

class PaymentProofUploaded
{
    use Dispatchable;

    public function __construct(public Order $order)
    {
    }
}
