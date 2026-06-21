<?php

/**
 * @author Ohene Adjei
 */

namespace App\Console\Commands;

use App\Enums\CarStatus;
use App\Models\Car;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('cars:archive-sold')]
#[Description('Soft-delete cars that have been Sold for more than 7 days so they drop off the catalogue.')]
class ArchiveSoldCars extends Command
{
    public function handle(): void
    {
        $cars = Car::where('status', CarStatus::Sold)
            ->where('sold_at', '<=', now()->subDays(7))
            ->get();

        $cars->each(fn (Car $car) => $car->delete());

        $this->info("Archived {$cars->count()} sold car(s).");
    }
}
