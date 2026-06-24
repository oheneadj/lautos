<?php

/**
 * Defines the body style/category a car listing falls under — drives both
 * the admin's body type field and the public catalogue's category filter
 * and homepage "Trending categories" tabs.
 *
 * @author Ohene Adjei
 */

namespace App\Enums;

enum CarBodyType: string
{
    case Sedan = 'sedan';
    case Suv = 'suv';
    case Hatchback = 'hatchback';
    case PickupTruck = 'pickup_truck';
    case VanMinivan = 'van_minivan';
    case Coupe = 'coupe';
    case Convertible = 'convertible';

    /** Returns a human-readable label for display. */
    public function label(): string
    {
        return match ($this) {
            self::Sedan => 'Sedan',
            self::Suv => 'SUV',
            self::Hatchback => 'Hatchback',
            self::PickupTruck => 'Pickup Truck',
            self::VanMinivan => 'Van / Minivan',
            self::Coupe => 'Coupe',
            self::Convertible => 'Convertible',
        };
    }
}
