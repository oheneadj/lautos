<?php

namespace Tests\Feature\Admin;

use App\Enums\KycStatus;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\Customers\Pages\ViewCustomer;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the admin customer list and KYC review workflows (US-15 / US-16).
 */
class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $this->seed(\Database\Seeders\ShieldPermissionsSeeder::class);

        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($admin);

        return $admin;
    }

    #[Test]
    public function guest_cannot_access_customer_management(): void
    {
        $this->get('/admin/customers')->assertRedirect('/admin/login');
    }

    #[Test]
    public function the_customer_list_excludes_admin_accounts(): void
    {
        $this->actingAsAdmin();

        $customer = User::factory()->create(['is_admin' => false]);
        $otherAdmin = User::factory()->create(['is_admin' => true]);

        Livewire::test(ListCustomers::class)
            ->assertCanSeeTableRecords([$customer])
            ->assertCanNotSeeTableRecords([$otherAdmin]);
    }

    #[Test]
    public function it_searches_customers_by_name_email_and_phone(): void
    {
        $this->actingAsAdmin();

        $match = User::factory()->create(['name' => 'Ama Owusu', 'phone' => '0551234567']);
        $other = User::factory()->create(['name' => 'Kojo Asante', 'phone' => '0559999999']);

        Livewire::test(ListCustomers::class)
            ->searchTable('Ama Owusu')
            ->assertCanSeeTableRecords([$match])
            ->assertCanNotSeeTableRecords([$other]);
    }

    #[Test]
    public function it_filters_customers_by_kyc_status(): void
    {
        $this->actingAsAdmin();

        $verified = User::factory()->create(['kyc_status' => KycStatus::Verified]);
        $pending = User::factory()->create(['kyc_status' => KycStatus::Pending]);

        Livewire::test(ListCustomers::class)
            ->filterTable('kyc_status', KycStatus::Verified->value)
            ->assertCanSeeTableRecords([$verified])
            ->assertCanNotSeeTableRecords([$pending]);
    }

    #[Test]
    public function the_table_shows_each_customers_order_count(): void
    {
        $this->actingAsAdmin();

        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);

        $customer = User::factory()->create();

        for ($i = 0; $i < 2; $i++) {
            $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
            Order::factory()->create(['user_id' => $customer->id, 'car_id' => $car->id]);
        }

        Livewire::test(ListCustomers::class)
            ->assertTableColumnStateSet('orders_count', 2, $customer);
    }

    #[Test]
    public function admin_can_verify_a_customers_kyc(): void
    {
        $this->actingAsAdmin();

        $customer = User::factory()->create(['kyc_status' => KycStatus::Pending]);

        Livewire::test(ViewCustomer::class, ['record' => $customer->uuid])
            ->callAction('verifyKyc');

        $this->assertSame(KycStatus::Verified, $customer->refresh()->kyc_status);
    }

    #[Test]
    public function admin_can_request_kyc_resubmission_with_a_reason(): void
    {
        Event::fake([\App\Events\KycResubmissionRequested::class]);
        $this->actingAsAdmin();

        $customer = User::factory()->create(['kyc_status' => KycStatus::Pending]);

        Livewire::test(ViewCustomer::class, ['record' => $customer->uuid])
            ->callAction('requestResubmission', data: ['reason' => 'Ghana Card photo is blurry.']);

        $customer->refresh();
        $this->assertSame(KycStatus::NeedsResubmission, $customer->kyc_status);
        $this->assertSame('Ghana Card photo is blurry.', $customer->kyc_notes);
    }

    #[Test]
    public function kyc_status_tabs_filter_the_table_to_just_that_status(): void
    {
        $this->actingAsAdmin();

        $verified = User::factory()->create(['kyc_status' => KycStatus::Verified]);
        $pending = User::factory()->create(['kyc_status' => KycStatus::Pending]);

        Livewire::test(ListCustomers::class)
            ->set('activeTab', KycStatus::Verified->value)
            ->assertCanSeeTableRecords([$verified])
            ->assertCanNotSeeTableRecords([$pending]);
    }
}
