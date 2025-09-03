<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add proper authorization logic later
    }

    public function rules(): array
    {
        return [
            'exhibitor_id' => 'required|exists:exhibitors,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:100|max:5000',
            'requirements' => 'required|string|min:50|max:3000',
            'salary_range' => 'nullable|string|max:100',
            'job_type' => 'required|string|in:full_time,part_time,contract,internship',
            'location' => 'required|string|max:255',
            'skills_required' => 'nullable|array',
            'skills_required.*' => 'string|max:50',
            'available_slots' => 'required|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'exhibitor_id.required' => 'Exhibitor is required.',
            'exhibitor_id.exists' => 'Selected exhibitor does not exist.',
            'title.required' => 'Please provide a job title.',
            'description.required' => 'Please provide a job description.',
            'description.min' => 'Job description should be at least 100 characters long.',
            'requirements.required' => 'Please provide job requirements.',
            'requirements.min' => 'Job requirements should be at least 50 characters long.',
            'job_type.required' => 'Please select a job type.',
            'job_type.in' => 'Please select a valid job type.',
            'location.required' => 'Please provide a job location.',
            'available_slots.required' => 'Please specify available interview slots.',
            'available_slots.min' => 'Must have at least 1 available slot.',
            'available_slots.max' => 'Cannot exceed 100 available slots.',
        ];
    }
}

