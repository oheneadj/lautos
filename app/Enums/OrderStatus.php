<?php

/**
 * Defines the 9-stage order pipeline from placement through to delivery.
 *
 * Stages must always advance in sequence — no skipping allowed.
 *
 * @author Ohene Adjei
 */

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment    = 'pending_payment';
    case PaymentUploaded   = 'payment_uploaded';
    case PaymentConfirmed  = 'payment_confirmed';
    case Purchased         = 'purchased';
    case InTransitToPort   = 'in_transit_to_port';
    case Shipped           = 'shipped';
    case ArrivedInGhana    = 'arrived_in_ghana';
    case Cleared           = 'cleared';
    case Delivered         = 'delivered';

    /** Returns a human-readable label for display. */
    public function label(): string
    {
        return match($this) {
            self::PendingPayment   => 'Pending Payment',
            self::PaymentUploaded  => 'Payment Uploaded',
            self::PaymentConfirmed => 'Payment Confirmed',
            self::Purchased        => 'Purchased',
            self::InTransitToPort  => 'In Transit to Port',
            self::Shipped          => 'Shipped',
            self::ArrivedInGhana   => 'Arrived in Ghana',
            self::Cleared          => 'Cleared',
            self::Delivered        => 'Delivered',
        };
    }

    /** Returns the Filament colour for the status badge. */
    public function colour(): string
    {
        return match($this) {
            self::PendingPayment   => 'warning',
            self::PaymentUploaded  => 'info',
            self::PaymentConfirmed => 'success',
            self::Purchased        => 'success',
            self::InTransitToPort  => 'info',
            self::Shipped          => 'info',
            self::ArrivedInGhana   => 'warning',
            self::Cleared          => 'success',
            self::Delivered        => 'success',
        };
    }

    /**
     * Returns the next stage in the pipeline.
     * I return null when the order is already at the final stage.
     */
    public function next(): ?self
    {
        $stages = self::cases();
        $currentIndex = array_search($this, $stages);

        if ($currentIndex === false || $currentIndex === array_key_last($stages)) {
            return null;
        }

        return $stages[$currentIndex + 1];
    }

    /**
     * Returns all stages as an ordered array — used to render the tracking timeline.
     *
     * @return self[]
     */
    public static function pipeline(): array
    {
        return self::cases();
    }

    /** Returns true if this stage requires admin action (payment review). */
    public function requiresAdminAction(): bool
    {
        return $this === self::PaymentUploaded;
    }
}
