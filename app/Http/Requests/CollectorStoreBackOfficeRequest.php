<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CollectorStoreBackOfficeRequest extends FormRequest
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
            'name' => 'string',
            'phone' => 'string',
            'identification' => 'required|numeric|unique:users,identification',
            'type_identification' => 'required|numeric',
            'email' => 'required|string|unique:users,email',
            'images.*' => 'required|min:1',
        ];
    }
}
