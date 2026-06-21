<?php

/**
 * Handles the public enquiry form — shared by the contact page and the car detail page.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Contact;

use App\Models\Car;
use App\Models\ContactEnquiry;
use Livewire\Component;

class ContactForm extends Component
{
    /** The subjects a visitor can pick from in the form. */
    public const SUBJECTS = [
        'General Enquiry',
        "This Vehicle's Availability",
        'Shipping & Import Process',
        'Other',
    ];

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $subject = 'General Enquiry';
    public string $message = '';
    public bool $submitted = false;

    /** Set when the form is embedded on a car detail page, so we know which car the enquiry is about. */
    public ?string $carUuid = null;

    /**
     * Pre-fills the subject when the form arrives with a car already in context.
     */
    public function mount(?string $carUuid = null): void
    {
        $this->carUuid = $carUuid;

        if ($carUuid) {
            $this->subject = "This Vehicle's Availability";
        }
    }

    protected function rules(): array
    {
        return [
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|min:10|max:2000',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        // I prefix the message with the car's details so admin knows which listing this is about.
        $message = $this->message;
        if ($this->carUuid && $car = Car::with(['make', 'carModel'])->where('uuid', $this->carUuid)->first()) {
            $message = "Regarding: {$car->year} {$car->make->name} {$car->carModel->name}\n\n{$message}";
        }

        ContactEnquiry::create([
            'name'    => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'subject' => $this->subject,
            'message' => $message,
        ]);

        $this->reset(['name', 'email', 'phone', 'message']);
        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.contact.contact-form');
    }
}
