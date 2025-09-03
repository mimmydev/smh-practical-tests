<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|regex:/^[\pL\s\-\'\.]+$/u',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => 'nullable|string|regex:/^[\+]?[0-9\-\(\)\s]+$/',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:10|max:2000',
            'category' => 'required|string|in:general,exhibitor,visitor,technical',
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Name may only contain letters, spaces, hyphens, apostrophes, and periods.',
            'message.min' => 'Please provide more details in your message (at least 10 characters).',
        ];
    }
}