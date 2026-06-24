<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the admin FAQ resource — FAQs used to be hardcoded directly in
 * faqs.blade.php; this is the admin UI that replaces that.
 */
class FaqManagementTest extends TestCase
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
    public function guest_cannot_access_faq_management(): void
    {
        $this->get('/admin/faqs')->assertRedirect('/admin/login');
    }

    #[Test]
    public function admin_can_create_an_faq(): void
    {
        $this->actingAsAdmin();

        Livewire::test(ListFaqs::class)
            ->callAction('create', data: [
                'question' => 'Can I track my shipment?',
                'answer' => 'Yes, your dashboard shows live shipment status.',
                'sort_order' => 10,
            ]);

        $this->assertDatabaseHas('faqs', [
            'question' => 'Can I track my shipment?',
        ]);
    }

    #[Test]
    public function admin_can_see_and_edit_an_faq(): void
    {
        $this->actingAsAdmin();

        $faq = Faq::create(['question' => 'Old question?', 'answer' => 'Old answer.', 'sort_order' => 0]);

        Livewire::test(ListFaqs::class)
            ->assertCanSeeTableRecords([$faq])
            ->callTableAction('edit', $faq, data: [
                'question' => 'New question?',
                'answer' => 'New answer.',
                'sort_order' => 0,
            ]);

        $this->assertSame('New question?', $faq->refresh()->question);
    }

    #[Test]
    public function admin_can_delete_an_faq(): void
    {
        $this->actingAsAdmin();

        $faq = Faq::create(['question' => 'Temp?', 'answer' => 'Temp answer.', 'sort_order' => 0]);

        Livewire::test(ListFaqs::class)
            ->callTableAction('delete', $faq);

        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }
}
