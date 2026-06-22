<?php

/**
 * @author Ohene Adjei
 */

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class KycResubmissionRequested
{
    use Dispatchable;

    public function __construct(public User $customer, public string $reason)
    {
    }
}
