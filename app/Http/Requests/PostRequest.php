<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.it' => ['required', 'string'],
            'content' => ['required', 'array'],
            'content.en' => ['required', 'string'],
            'content.it' => ['required', 'string'],
            'short_description' => ['required', 'array'],
            'short_description.en' => ['required', 'string'],
            'short_description.it' => ['required', 'string'],
            'tags' => ['required', 'array'],
            'tags.*.id' => ['required', 'exists:tags,id'],
        ];
    }
}
