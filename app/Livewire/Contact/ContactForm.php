<?php

declare(strict_types=1);

/**
 * Handles the public enquiry form — shared by the contact page and the car detail page.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Contact;

use App\Events\ContactEnquirySubmitted;
use App\Http\Requests\ContactFormRequest;
use App\Models\Car;
use App\Models\ContactEnquiry;
use Illuminate\Support\Facades\RateLimiter;
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

    public function submit(): void
    {
        // This form has no auth to key a per-account limit off, so I key by
        // IP — CLAUDE.md names this form specifically as needing a throttle,
        // and unlimited public submissions can spam the DB and admin inbox.
        $throttleKey = 'contact-form:'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, maxAttempts: 3)) {
            $this->addError('message', 'Too many submissions. Please try again in a few minutes.');

            return;
        }

        $this->phone = preg_replace('/\s+/', '', $this->phone ?? '');

        $request = new ContactFormRequest;
        $this->validate($request->rules(), $request->messages());

        RateLimiter::hit($throttleKey, decaySeconds: 600);

        // I prefix the message with the car's details so admin knows which listing this is about.
        $message = $this->message;
        if ($this->carUuid && $car = Car::with(['make', 'carModel'])->where('uuid', $this->carUuid)->first()) {
            $message = "Regarding: {$car->year} {$car->make->name} {$car->carModel->name}\n\n{$message}";
        }

        $enquiry = ContactEnquiry::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $message,
        ]);

        ContactEnquirySubmitted::dispatch($enquiry);

        $this->reset(['name', 'email', 'phone', 'message']);
        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.contact.contact-form');
    }
}
