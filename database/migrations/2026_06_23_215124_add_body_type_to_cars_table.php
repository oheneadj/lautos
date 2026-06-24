<?php

/**
 * Adds body_type to cars and backfills existing rows using the same
 * model-name -> category mapping that used to live as a hardcoded list in
 * the homepage's "Trending categories" section — that section had no real
 * field to query against, so it matched on car model names directly. This
 * migration retires that hack by doing the same match once, here, instead.
 *
 * @author Ohene Adjei
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // I use a string here and enforce values via the CarBodyType enum class.
        Schema::table('cars', function (Blueprint $table) {
            $table->string('body_type')->nullable()->after('country_of_origin');
            $table->index('body_type');
        });

        $modelNamesByBodyType = [
            'suv' => ['RAV4', 'CR-V', 'Tucson', 'Explorer', 'Highlander', 'Santa Fe', 'Sportage', 'Sorento', 'Escape', 'CX-5', 'Q5', 'X5', 'XC60'],
            'sedan' => ['Camry', 'Accord', 'Sonata', 'Civic', 'Corolla', 'Elantra', 'Optima', 'Fusion', 'Mazda3', 'Mazda6', 'C-Class', '3 Series', 'A4'],
            'pickup_truck' => ['Tacoma', 'F-150', 'Hilux', 'Ranger', 'Navara', 'L200', 'Colorado', 'Silverado', 'D-Max'],
            'hatchback' => ['Fit', 'Yaris', 'Golf', 'Polo', 'Focus', 'Fiesta', 'Swift', 'Rio', 'Mazda2', 'i20'],
        ];

        foreach ($modelNamesByBodyType as $bodyType => $modelNames) {
            DB::table('cars')
                ->whereIn('car_model_id', function ($query) use ($modelNames) {
                    $query->select('id')->from('car_models')->whereIn('name', $modelNames);
                })
                ->update(['body_type' => $bodyType]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('body_type');
        });
    }
};
