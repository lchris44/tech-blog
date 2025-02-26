<?php

namespace App\Http\Requests\Api\v1;

use App\Rules\ValidTag;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostRequest extends FormRequest
{
    /**
     * Define the validation rules for the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // Validate the 'tags' field
            'tags' => [
                'nullable',          // The field is optional
                'array',            // Must be an array
                new ValidTag,        // Use the custom rule to validate tags
            ],
            // Validate each item in the 'tags' array
            'tags.*' => [
                'string',           // Each tag must be a string
            ],
            // Validate the 'page' field
            'page' => [
                'nullable',         // The field is optional
                'integer',          // Must be an integer
                'min:1',            // Must be at least 1
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     * Convert comma-separated tags into an array.
     */
    protected function prepareForValidation()
    {
        // Check if the 'tags' field is present in the request
        if ($this->has('tags')) {
            // Convert comma-separated tags into an array
            $this->merge([
                'tags' => explode(',', $this->tags),
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        // Throw an HTTP response exception with validation errors
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(), // Include validation error messages
        ], 422)); // HTTP status code 422: Unprocessable Entity
    }
}
