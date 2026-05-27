<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'candidate';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'linkedin_url' => 'nullable|url|max:255',
            'skills' => 'nullable|array|max:20',
            'skills.*' => 'string|max:50',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'phone.string' => 'The phone number must be a valid string.',
            'phone.max' => 'The phone number must not exceed 20 characters.',
            'linkedin_url.url' => 'The LinkedIn URL must be a valid URL.',
            'linkedin_url.max' => 'The LinkedIn URL must not exceed 255 characters.',
            'skills.array' => 'Skills must be an array.',
            'skills.max' => 'You can add a maximum of 20 skills.',
            'skills.*.string' => 'Each skill must be a string.',
            'skills.*.max' => 'Each skill must not exceed 50 characters.',
            'resume.file' => 'The resume must be a file.',
            'resume.mimes' => 'The resume must be a PDF or Word document (doc, docx).',
            'resume.max' => 'The resume must not exceed 5MB.',
        ];
    }
}
