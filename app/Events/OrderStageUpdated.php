<?php

/**
 * @author Ohene Adjei
 */

namespace App\Events;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;

class OrderStageUpdated
{
    use Dispatchable;

    public function __construct(public Order $order, public OrderStatus $previousStatus)
    {
    }
}
