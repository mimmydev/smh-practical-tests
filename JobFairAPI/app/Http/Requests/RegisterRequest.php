<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|regex:/^[\pL\s\-\'\.]+$/u',
            'email' => 'required|email:rfc,dns|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'phone' => 'nullable|string|regex:/^[\+]?[0-9\-\(\)\s]+$/',
            'profession' => 'nullable|string|max:100',
            'experience_level' => 'nullable|string|in:entry,junior,mid,senior,lead,executive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your full name.',
            'name.regex' => 'Name may only contain letters, spaces, hyphens, apostrophes, and periods.',
            'email.required' => 'Please provide your email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered. Please use a different email or try logging in.',
            'password.required' => 'Please provide a password.',
            'password.confirmed' => 'Password confirmation does not match.',
            'phone.regex' => 'Please provide a valid phone number format.',
            'experience_level.in' => 'Please select a valid experience level.',
        ];
    }
}
