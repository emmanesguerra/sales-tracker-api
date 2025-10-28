<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadCsvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt|max:2048', // Validate file type and size
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'No file was uploaded.',
            'file.file' => 'The uploaded file must be a valid file.',
            'file.mimes' => 'The file must be a CSV or text file.',
            'file.max' => 'The file size must not exceed 2MB.',
        ];
    }
}
