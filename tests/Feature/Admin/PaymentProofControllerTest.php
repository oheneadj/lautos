<?php

namespace Tests\Feature\Admin;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\User;
use Database\Seeders\ShieldPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests that payment proofs are only reachable through a signed admin URL
 * and are served from the private disk — the admin Filament view previously
 * built a broken public-disk URL for files that only exist privately.
 */
class PaymentProofControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $this->seed(ShieldPermissionsSeeder::class);

        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($admin);

        return $admin;
    }

    private function makeProof(string $path): PaymentProof
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
        $order = Order::factory()->create(['car_id' => $car->id]);

        return PaymentProof::create(['order_id' => $order->id, 'file_path' => $path]);
    }

    #[Test]
    public function an_admin_can_view_a_payment_proof_via_a_signed_url(): void
    {
        Storage::fake('private');
        $this->actingAsAdmin();

        $proof = $this->makeProof('payment-proofs/'.uniqid().'/receipt.jpg');
        Storage::disk('private')->put($proof->file_path, 'fake-image-bytes');

        $url = URL::signedRoute('admin.payment-proofs.show', ['proof' => $proof]);

        $this->get($url)->assertOk();
    }

    #[Test]
    public function the_proof_is_not_reachable_without_a_valid_signature(): void
    {
        Storage::fake('private');
        $this->actingAsAdmin();

        $proof = $this->makeProof('payment-proofs/'.uniqid().'/receipt.jpg');
        Storage::disk('private')->put($proof->file_path, 'fake-image-bytes');

        $this->get(route('admin.payment-proofs.show', ['proof' => $proof]))->assertForbidden();
    }

    #[Test]
    public function a_non_admin_cannot_view_a_payment_proof_even_with_a_valid_signature(): void
    {
        Storage::fake('private');

        $proof = $this->makeProof('payment-proofs/'.uniqid().'/receipt.jpg');
        Storage::disk('private')->put($proof->file_path, 'fake-image-bytes');

        $customer = User::factory()->create();
        $this->actingAs($customer);

        $url = URL::signedRoute('admin.payment-proofs.show', ['proof' => $proof]);

        $this->get($url)->assertForbidden();
    }

    #[Test]
    public function a_guest_cannot_view_a_payment_proof_even_with_a_valid_signature(): void
    {
        Storage::fake('private');

        $proof = $this->makeProof('payment-proofs/'.uniqid().'/receipt.jpg');
        Storage::disk('private')->put($proof->file_path, 'fake-image-bytes');

        $url = URL::signedRoute('admin.payment-proofs.show', ['proof' => $proof]);

        $this->get($url)->assertRedirect(route('login'));
    }

    #[Test]
    public function it_returns_404_when_the_proof_file_does_not_exist_on_disk(): void
    {
        Storage::fake('private');
        $this->actingAsAdmin();

        $proof = $this->makeProof('payment-proofs/'.uniqid().'/missing.jpg');

        $url = URL::signedRoute('admin.payment-proofs.show', ['proof' => $proof]);

        $this->get($url)->assertNotFound();
    }
}
