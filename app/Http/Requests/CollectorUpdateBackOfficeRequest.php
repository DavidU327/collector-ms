<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CollectorUpdateBackOfficeRequest extends FormRequest
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
            'name' => 'sometimes|string',
            'phone' => 'sometimes|string',
            'identification' => 'sometimes|numeric|unique:users,identification',
            'type_identification' => 'sometimes|numeric',
            'email' => 'sometimes|string|email|unique:users,email',
            'images.*' => 'sometimes|min:1',
            'identification_document.*' => 'sometimes|file|mimes:pdf|max:2048',
            'driving_license_document.*' => 'sometimes|file|mimes:pdf|max:2048',
        ];
    }
}
