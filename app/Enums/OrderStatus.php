<?php

/**
 * Defines the 9-stage order pipeline from placement through to delivery,
 * plus the terminal Cancelled state for orders that lose the race to
 * another buyer's confirmed payment.
 *
 * Stages must always advance in sequence — no skipping allowed. Cancelled
 * is a side-branch, not a pipeline stage — it never appears in next()/pipeline().
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
    case Cancelled         = 'cancelled';

    /**
     * The 9 sequential stages, in order. I keep this separate from
     * self::cases() so Cancelled never gets treated as "the next stage"
     * after Delivered, and never shows up as a step in the customer's
     * shipment timeline.
     *
     * @return self[]
     */
    private const SEQUENCE = [
        self::PendingPayment,
        self::PaymentUploaded,
        self::PaymentConfirmed,
        self::Purchased,
        self::InTransitToPort,
        self::Shipped,
        self::ArrivedInGhana,
        self::Cleared,
        self::Delivered,
    ];

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
            self::Cancelled        => 'Cancelled',
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
            self::Cancelled        => 'danger',
        };
    }

    /**
     * Returns the next stage in the pipeline.
     * I return null when the order is already at the final stage, or when
     * called on Cancelled (it has no next stage — it's terminal).
     */
    public function next(): ?self
    {
        $currentIndex = array_search($this, self::SEQUENCE, true);

        if ($currentIndex === false || $currentIndex === array_key_last(self::SEQUENCE)) {
            return null;
        }

        return self::SEQUENCE[$currentIndex + 1];
    }

    /**
     * Returns the 9 sequential stages as an ordered array — used to render
     * the tracking timeline. Cancelled is deliberately excluded since it's
     * a side-branch, not a step a customer progresses through.
     *
     * @return self[]
     */
    public static function pipeline(): array
    {
        return self::SEQUENCE;
    }

    /** Returns true if this stage requires admin action (payment review). */
    public function requiresAdminAction(): bool
    {
        return $this === self::PaymentUploaded;
    }
}
