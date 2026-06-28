<?php

declare(strict_types=1);

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

use App\Http\Requests\CompleteKycRequest;
use Illuminate\Support\Facades\Auth;
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
        // I clean the inputs before validation to ensure a smooth user experience
        $this->phone = preg_replace('/\s+/', '', $this->phone);
        $this->ghana_card_number = strtoupper(trim($this->ghana_card_number));
        $this->tin_number = strtoupper(trim($this->tin_number));

        $request = new CompleteKycRequest;
        $this->validate($request->rules(), $request->messages());

        $user = Auth::user();

        $data = [
            'phone' => $this->phone,
            'address' => $this->address,
            'ghana_card_number' => $this->ghana_card_number ?: null,
            'tin_number' => $this->tin_number ?: null,
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
