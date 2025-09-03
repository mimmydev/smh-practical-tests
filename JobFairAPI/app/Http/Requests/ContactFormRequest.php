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
            'name.required' => 'Please provide your name.',
            'name.regex' => 'Name may only contain letters, spaces, hyphens, apostrophes, and periods.',
            'email.required' => 'Please provide your email address.',
            'email.email' => 'Please provide a valid email address.',
            'subject.required' => 'Please provide a subject for your message.',
            'message.required' => 'Please provide your message.',
            'message.min' => 'Please provide more details in your message (at least 10 characters).',
            'message.max' => 'Your message is too long. Please keep it under 2000 characters.',
            'category.required' => 'Please select a category for your inquiry.',
            'category.in' => 'Please select a valid category.',
            'phone.regex' => 'Please provide a valid phone number format.',
        ];
    }
}