<?php

/**
 * @author Ohene Adjei
 */

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class KycVerified
{
    use Dispatchable;

    public function __construct(public User $customer) {}
}
