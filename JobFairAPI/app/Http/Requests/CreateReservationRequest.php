<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CreateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'job_id' => 'required|exists:jobs,id',
            'session_type' => 'required|string|in:job_matching,career_talk',
            'session_time' => 'required|date|after:now',
            'user_message' => 'nullable|string|max:1000',
            'additional_info' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'job_id.required' => 'Please select a job.',
            'job_id.exists' => 'Selected job does not exist.',
            'session_type.required' => 'Please select a session type.',
            'session_type.in' => 'Please select a valid session type.',
            'session_time.required' => 'Please select a session time.',
            'session_time.date' => 'Please provide a valid date and time.',
            'session_time.after' => 'Session time must be in the future.',
            'user_message.max' => 'Message is too long. Please keep it under 1000 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $sessionTime = Carbon::parse($this->session_time);
            
            // Check if session is during business hours (9 AM - 6 PM)
            if ($sessionTime->hour < 9 || $sessionTime->hour >= 18) {
                $validator->errors()->add('session_time', 'Session must be scheduled between 9 AM and 6 PM.');
            }
            
            // Check if session is on a weekday
            if ($sessionTime->isWeekend()) {
                $validator->errors()->add('session_time', 'Sessions can only be scheduled on weekdays.');
            }
            
            // Check if session is not more than 30 days in the future
            if ($sessionTime->diffInDays(now()) > 30) {
                $validator->errors()->add('session_time', 'Sessions can only be scheduled up to 30 days in advance.');
            }
        });
    }
}
