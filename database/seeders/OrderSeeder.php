<?php

/**
 * Seeds a small, realistic spread of order activity on top of CarSeeder's
 * all-Available cars — a couple of Reserved cars, one Sold car, and a
 * couple of cars with open (PendingPayment) orders for the "Reservations"
 * badge to have something to show.
 *
 * Every status change goes through OrderService, the same code real
 * traffic uses, so Car and Order status can never drift out of sync the
 * way they did when CarFactory used to assign a random status with no
 * backing order at all.
 *
 * @author Ohene Adjei
 */

namespace Database\Seeders;

use App\Enums\CarStatus;
use App\Enums\OrderStatus;
use App\Models\Car;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('OrderSeeder contains mock/scraped data and cannot be run in production.');

            return;
        }

        $service = app(OrderService::class);

        $availableCars = Car::where('status', CarStatus::Available)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        if ($availableCars->count() < 5) {
            return;
        }

        [$reservedCar1, $reservedCar2, $soldCar, $pendingCar1, $pendingCar2] = $availableCars->all();

        // Two Reserved cars — order placed, proof uploaded, payment confirmed.
        $this->confirmAnOrder($service, $reservedCar1);
        $this->confirmAnOrder($service, $reservedCar2);

        // One Sold car — same as above, then advanced all the way through delivery.
        $soldOrder = $this->confirmAnOrder($service, $soldCar);
        $service->advanceStage($soldOrder, OrderStatus::Purchased);
        $service->advanceStage($soldOrder, OrderStatus::InTransitToPort);
        $service->advanceStage($soldOrder, OrderStatus::Shipped, ['estimated_arrival_date' => now()->subWeeks(2)->toDateString()]);
        $service->advanceStage($soldOrder, OrderStatus::ArrivedInGhana);
        $service->advanceStage($soldOrder, OrderStatus::Cleared);
        $service->advanceStage($soldOrder, OrderStatus::Delivered);

        // Two cars with an open order each — car stays Available, but now
        // has a real pending reservation for the catalogue badge to count.
        $service->createOrder(User::factory()->create(), $pendingCar1);
        $service->createOrder(User::factory()->create(), $pendingCar2);
    }

    /**
     * Places an order, simulates the customer uploading payment proof, then
     * confirms it — the same sequence OrderDetail::uploadPaymentProof() and
     * the admin's "Confirm Payment" action drive in production.
     */
    private function confirmAnOrder(OrderService $service, Car $car): Order
    {
        $order = $service->createOrder(User::factory()->create(), $car);

        $order->update(['status' => OrderStatus::PaymentUploaded]);
        $order->paymentProofs()->create([
            'file_path' => 'demo/seed-payment-proof.jpg',
            'note' => 'Seed data — demo payment proof.',
        ]);

        $service->confirmPayment($order);

        $order->refresh();

        return $order;
    }
}
