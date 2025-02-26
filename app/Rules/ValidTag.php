<?php

namespace App\Rules;

use App\Models\Tag;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTag implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Ensure the value is an array
        if (! is_array($value)) {
            $fail(__('The tags must be an array.'));

            return;
        }

        // Check each tag in the array
        foreach ($value as $tag) {
            $exists = Tag::where('name->en', $tag)
                ->orWhere('name->it', $tag)
                ->exists();

            if (! $exists) {
                $fail(__('The selected tag ":tag" is invalid.', ['tag' => $tag]));

                return;
            }
        }
    }
}
