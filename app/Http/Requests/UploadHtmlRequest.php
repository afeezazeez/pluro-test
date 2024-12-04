<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadHtmlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required','file','mimes:html','max:1024']
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'The HTML file is required.',
            'file.file' => 'The uploaded file must be a valid file.',
            'file.mimes' => 'The file must be of type: .html.',
            'file.max' => 'The uploaded file size must not exceed 1MB.',
        ];
    }
}
