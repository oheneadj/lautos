<?php

namespace Tests\Feature\Public;

use App\Livewire\Contact\ContactForm;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\ContactEnquiry;
use App\Models\Make;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the shared enquiry form used on the contact page and the car detail page.
 */
class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_visitor_can_submit_a_general_enquiry(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'Kofi Mensah')
            ->set('email', 'kofi@example.com')
            ->set('message', 'I would like to know more about your import process.')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('contact_enquiries', [
            'name'    => 'Kofi Mensah',
            'email'   => 'kofi@example.com',
            'subject' => 'General Enquiry',
        ]);
    }

    #[Test]
    public function submitting_with_invalid_input_shows_validation_errors(): void
    {
        Livewire::test(ContactForm::class)
            ->set('email', 'not-an-email')
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('contact_enquiries', 0);
    }

    #[Test]
    public function an_enquiry_from_a_car_page_defaults_to_the_vehicle_subject_and_tags_the_car(): void
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);

        Livewire::test(ContactForm::class, ['carUuid' => $car->uuid])
            ->assertSet('subject', "This Vehicle's Availability")
            ->set('name', 'Ama Owusu')
            ->set('email', 'ama@example.com')
            ->set('message', 'Is this car still available for purchase?')
            ->call('submit')
            ->assertSet('submitted', true);

        $enquiry = ContactEnquiry::first();

        $this->assertNotNull($enquiry);
        // I check the car's details were folded into the message so admin knows which listing this is about.
        $this->assertStringContainsString((string) $car->year, $enquiry->message);
        $this->assertStringContainsString('Corolla', $enquiry->message);
    }
}
