<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Makes\Pages\EditMake;
use App\Filament\Resources\Makes\RelationManagers\CarModelsRelationManager;
use App\Models\CarModel;
use App\Models\CarTrim;
use App\Models\Make;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests managing a make's models and trims from its admin edit page (US-09
 * follow-up) — previously these could only be created on the fly from the
 * car form, with no way to rename or remove them afterwards.
 */
class MakeModelsAndTrimsTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $this->seed(\Database\Seeders\ShieldPermissionsSeeder::class);

        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($user);

        return $user;
    }

    #[Test]
    public function guest_cannot_access_the_make_edit_page(): void
    {
        $make = Make::factory()->create();

        $this->get(EditMake::getUrl(['record' => $make]))->assertRedirect('/admin/login');
    }

    #[Test]
    public function admin_can_view_a_makes_existing_models_on_its_edit_page(): void
    {
        $this->actingAsAdmin();

        $make = Make::factory()->create();
        $model = CarModel::factory()->for($make)->create(['name' => 'Corolla']);

        Livewire::test(CarModelsRelationManager::class, [
            'ownerRecord' => $make,
            'pageClass' => EditMake::class,
        ])->assertCanSeeTableRecords([$model]);
    }

    #[Test]
    public function admin_can_add_a_model_to_a_make(): void
    {
        $this->actingAsAdmin();

        $make = Make::factory()->create();

        Livewire::test(CarModelsRelationManager::class, [
            'ownerRecord' => $make,
            'pageClass' => EditMake::class,
        ])
            ->callTableAction('create', data: ['name' => 'Sportage'])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('car_models', ['make_id' => $make->id, 'name' => 'Sportage']);
    }

    #[Test]
    public function admin_can_rename_a_model(): void
    {
        $this->actingAsAdmin();

        $make = Make::factory()->create();
        $model = CarModel::factory()->for($make)->create(['name' => 'Corola']);

        Livewire::test(CarModelsRelationManager::class, [
            'ownerRecord' => $make,
            'pageClass' => EditMake::class,
        ])
            ->callTableAction('edit', $model, data: ['name' => 'Corolla'])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('car_models', ['id' => $model->id, 'name' => 'Corolla']);
    }

    #[Test]
    public function admin_can_delete_a_model(): void
    {
        $this->actingAsAdmin();

        $make = Make::factory()->create();
        $model = CarModel::factory()->for($make)->create();

        Livewire::test(CarModelsRelationManager::class, [
            'ownerRecord' => $make,
            'pageClass' => EditMake::class,
        ])->callTableAction('delete', $model);

        $this->assertDatabaseMissing('car_models', ['id' => $model->id]);
    }

    #[Test]
    public function the_manage_trims_action_is_available_on_each_model_row(): void
    {
        $this->actingAsAdmin();

        $make = Make::factory()->create();
        $model = CarModel::factory()->for($make)->create();
        CarTrim::factory()->for($model, 'carModel')->create(['name' => 'LE']);

        Livewire::test(CarModelsRelationManager::class, [
            'ownerRecord' => $make,
            'pageClass' => EditMake::class,
        ])->assertTableActionExists('manageTrims', record: $model);
    }
}
