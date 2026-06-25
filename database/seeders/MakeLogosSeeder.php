<?php

/**
 * Imports car brand logos (sourced from the filippofilip95/car-logos-dataset
 * GitHub project, bundled into assets/make-logos/ so this works on any
 * environment, including production) matched to our Make rows by slug. I only
 * ever fill in makes that don't already have an icon_path, so a manually
 * -uploaded logo is never overwritten by this.
 *
 * @author Ohene Adjei
 */

namespace Database\Seeders;

use App\Models\Make;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MakeLogosSeeder extends Seeder
{
    public function run(): void
    {
        // These are the dataset's thumb/ versions (~385x256, transparent PNG)
        // — already the right size for our small make-icon usages (admin
        // previews, the homepage brand grid), so nothing to re-optimize.
        $assetsPath = __DIR__.'/assets/make-logos';

        if (! is_dir($assetsPath)) {
            $this->command?->warn("Make logo assets not found at {$assetsPath} — skipping.");

            return;
        }

        Make::whereNull('icon_path')->get()->each(function (Make $make) use ($assetsPath) {
            $logoPath = "{$assetsPath}/{$make->slug}.png";

            if (! file_exists($logoPath)) {
                return;
            }

            $storedPath = 'make-icons/'.Str::ulid().'.png';
            Storage::disk('public')->put($storedPath, file_get_contents($logoPath));

            $make->update(['icon_path' => $storedPath]);
        });
    }
}
