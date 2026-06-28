<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'ghana_card_number' => ['required_without:tin_number', 'nullable', 'string', 'regex:/^GHA-\d{9}-[0-9A-Z]$/'],
            'tin_number' => ['required_without:ghana_card_number', 'nullable', 'string', 'regex:/^[CGQVcgqv]\d{9}[A-Za-z0-9]$/'],
            'ghana_card_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'tin_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'ghana_card_number.regex' => 'Ghana Card must be in the format GHA-123456789-0.',
            'tin_number.regex' => 'TIN must be a valid GRA entity TIN.',
            'ghana_card_number.required_without' => 'Please provide your Ghana Card number or TIN.',
            'tin_number.required_without' => 'Please provide your TIN or Ghana Card number.',
        ];
    }
}
