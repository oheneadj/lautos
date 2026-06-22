<?php

/**
 * Step 2 of customer registration — collects KYC information.
 *
 * I keep this as a standalone page rather than a multi-step modal because
 * Fortify handles Step 1 (name/email/password) and redirects here. The
 * user can skip this and come back later from their profile page.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Complete Your KYC')]
#[Layout('layouts.auth')]
class CompleteKyc extends Component
{
    use WithFileUploads;

    public string $phone = '';
    public string $address = '';
    public string $ghana_card_number = '';
    public string $tin_number = '';

    // I use nullable TemporaryUploadedFile here — Livewire handles the type via WithFileUploads.
    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf|max:5120')]
    public $ghana_card_file = null;

    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf|max:5120')]
    public $tin_file = null;

    public function mount(): void
    {
        $user = Auth::user();

        // Pre-fill any data the user may have already entered.
        $this->phone = $user->phone ?? '';
        $this->address = $user->address ?? '';
        $this->ghana_card_number = $user->ghana_card_number ?? '';
        $this->tin_number = $user->tin_number ?? '';
    }

    public function submit(): void
    {
        $this->validate([
            'phone'              => ['required', 'string', 'max:20'],
            'address'            => ['required', 'string', 'max:500'],
            'ghana_card_number'  => ['required_without:tin_number', 'nullable', 'string', 'max:50'],
            'tin_number'         => ['required_without:ghana_card_number', 'nullable', 'string', 'max:50'],
            'ghana_card_file'    => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'tin_file'           => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'ghana_card_number.required_without' => 'Please provide your Ghana Card number or TIN.',
            'tin_number.required_without'         => 'Please provide your TIN or Ghana Card number.',
        ]);

        $user = Auth::user();

        $data = [
            'phone'             => $this->phone,
            'address'           => $this->address,
            'ghana_card_number' => $this->ghana_card_number ?: null,
            'tin_number'        => $this->tin_number ?: null,
        ];

        // I store KYC docs on the private disk — never public.
        if ($this->ghana_card_file) {
            $data['ghana_card_path'] = $this->ghana_card_file->store(
                "kyc/{$user->uuid}",
                'private'
            );
        }

        if ($this->tin_file) {
            $data['tin_path'] = $this->tin_file->store(
                "kyc/{$user->uuid}",
                'private'
            );
        }

        $user->update($data);

        $this->redirectRoute('dashboard.index', navigate: true);
    }

    public function skip(): void
    {
        $this->redirectRoute('dashboard.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.complete-kyc');
    }
}
