<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExhibitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:exhibitors,name',
            'description' => 'required|string|min:50|max:2000',
            'contact_email' => 'required|email:rfc,dns|unique:exhibitors,contact_email',
            'phone' => 'nullable|string|regex:/^[\+]?[0-9\-\(\)\s]+$/',
            'website' => 'nullable|url|max:255',
            'industry' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'booth_preferences' => 'nullable|array',
            'booth_preferences.*' => 'string|in:premium,standard,corner,island',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'An exhibitor with this name already exists.',
            'contact_email.unique' => 'This email address is already registered.',
            'description.min' => 'Please provide a more detailed description (at least 50 characters).',
            'phone.regex' => 'Please provide a valid phone number.',
        ];
    }
}