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
            'phone' => 'required|string|regex:/^[\+]?[0-9\-\(\)\s]+$/',
            'website' => 'nullable|url|max:255',
            'industry' => 'required|string|max:100|in:technology,finance,healthcare,education,manufacturing,retail,consulting,media,government,non_profit,other',
            'address' => 'required|string|max:500',
            'booth_preferences' => 'nullable|array',
            'booth_preferences.*' => 'string|in:premium,standard,corner,island',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your company name.',
            'name.unique' => 'An exhibitor with this company name already exists.',
            'description.required' => 'Please provide a company description.',
            'description.min' => 'Please provide a more detailed description (at least 50 characters).',
            'description.max' => 'Company description is too long. Please keep it under 2000 characters.',
            'contact_email.required' => 'Please provide a contact email address.',
            'contact_email.email' => 'Please provide a valid email address.',
            'contact_email.unique' => 'This email address is already registered for another exhibitor.',
            'phone.required' => 'Please provide a contact phone number.',
            'phone.regex' => 'Please provide a valid phone number format.',
            'website.url' => 'Please provide a valid website URL.',
            'industry.required' => 'Please select your industry.',
            'industry.in' => 'Please select a valid industry from the list.',
            'address.required' => 'Please provide your company address.',
            'booth_preferences.*.in' => 'Please select valid booth preferences.',
        ];
    }
}