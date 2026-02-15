<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'email_notifications' => ['boolean'],
            'exam_notifications' => ['boolean'],
            'result_notifications' => ['boolean'],
            'system_notifications' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The full name field is required.',
            'email.required' => 'The email address field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'phone.regex' => 'Please enter a valid phone number.',
            'facebook_url.url' => 'Please enter a valid Facebook URL.',
            'twitter_url.url' => 'Please enter a valid Twitter/X URL.',
            'linkedin_url.url' => 'Please enter a valid LinkedIn URL.',
            'instagram_url.url' => 'Please enter a valid Instagram URL.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Clean up phone number (remove spaces, dashes, parentheses)
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^\d\+]/', '', $this->phone),
            ]);
        }
        
        // Ensure notification fields are booleans
        $this->merge([
            'email_notifications' => $this->boolean('email_notifications'),
            'exam_notifications' => $this->boolean('exam_notifications'),
            'result_notifications' => $this->boolean('result_notifications'),
            'system_notifications' => $this->boolean('system_notifications'),
        ]);
    }
}