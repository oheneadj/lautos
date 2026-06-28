<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfilePhoneInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.phone' => ['required', 'string', 'regex:/^(?:\+233|0)\d{9}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'data.phone.regex' => 'Phone number must be a valid Ghanaian number (e.g., 0244000000 or +233244000000).',
        ];
    }
}
