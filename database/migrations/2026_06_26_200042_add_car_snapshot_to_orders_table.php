<?php

/**
 * Snapshots a car's identity (year/make/model/thumbnail) onto each order at
 * creation time, the same way price is already snapshotted — so an order's
 * display never depends on the car row surviving, or staying unedited,
 * after the sale. Backfills existing orders from their current car
 * relation immediately after adding the columns, since this is the best
 * information available and only ever runs once.
 *
 * @author Ohene Adjei
 */

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedSmallInteger('car_year')->nullable()->after('car_id');
            $table->string('car_make_name')->nullable()->after('car_year');
            $table->string('car_model_name')->nullable()->after('car_make_name');
            $table->string('car_thumbnail_path')->nullable()->after('car_model_name');
        });

        // I include trashed cars here — a soft-deleted car is exactly the
        // case this backfill needs to capture before it's too late.
        Order::withTrashed()
            ->with(['car' => fn ($query) => $query->withTrashed()->with(['make', 'carModel', 'images'])])
            ->whereNull('car_year')
            ->each(function (Order $order) {
                $car = $order->car;

                if (! $car) {
                    return;
                }

                $order->forceFill([
                    'car_year' => $car->year,
                    'car_make_name' => $car->make?->name,
                    'car_model_name' => $car->carModel?->name,
                    'car_thumbnail_path' => $car->images->first()?->path,
                ])->save();
            });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['car_year', 'car_make_name', 'car_model_name', 'car_thumbnail_path']);
        });
    }
};
