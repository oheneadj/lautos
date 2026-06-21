<?php

/**
 * Adds a unique slug to cars so the public detail page can use a readable
 * URL instead of the raw uuid.
 *
 * @author Ohene Adjei
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('uuid')->unique();
        });

        $this->backfillExistingCars();
    }

    /**
     * Generates a slug for any cars that existed before this column was added.
     */
    private function backfillExistingCars(): void
    {
        $cars = DB::table('cars')
            ->join('makes', 'makes.id', '=', 'cars.make_id')
            ->join('car_models', 'car_models.id', '=', 'cars.car_model_id')
            ->select('cars.id', 'cars.year', 'makes.name as make_name', 'car_models.name as model_name')
            ->get();

        $usedSlugs = [];

        foreach ($cars as $car) {
            $base = Str::slug("{$car->year}-{$car->make_name}-{$car->model_name}");
            $slug = $base;
            $suffix = 2;

            while (in_array($slug, $usedSlugs, true)) {
                $slug = "{$base}-{$suffix}";
                $suffix++;
            }

            $usedSlugs[] = $slug;

            DB::table('cars')->where('id', $car->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
