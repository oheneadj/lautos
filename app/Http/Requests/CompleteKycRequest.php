<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CompleteKycRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^(?:\+233|0)\d{9}$/'],
            'address' => ['required', 'string', 'max:500'],
            'ghana_card_number' => ['required_without:tin_number', 'nullable', 'string', 'regex:/^GHA-\d{9}-[0-9A-Z]$/'],
            'tin_number' => ['required_without:ghana_card_number', 'nullable', 'string', 'regex:/^[CGQVcgqv]\d{9}[A-Za-z0-9]$/'],
            'ghana_card_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'tin_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Phone number must be a valid Ghanaian number (e.g., 0244000000 or +233244000000).',
            'ghana_card_number.regex' => 'Ghana Card must be in the format GHA-123456789-0.',
            'tin_number.regex' => 'TIN must be a valid GRA entity TIN.',
            'ghana_card_number.required_without' => 'Please provide your Ghana Card number or TIN.',
            'tin_number.required_without' => 'Please provide your TIN or Ghana Card number.',
        ];
    }
}
