<?php

namespace App\Livewire\Contact;

use App\Models\ContactEnquiry;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $message = '';
    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'message' => 'required|string|min:10|max:2000',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        ContactEnquiry::create([
            'name'    => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'message' => $this->message,
        ]);

        $this->reset(['name', 'email', 'phone', 'message']);
        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.contact.contact-form');
    }
}
