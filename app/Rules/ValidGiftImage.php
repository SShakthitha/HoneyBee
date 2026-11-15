<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ValidGiftImage implements ValidationRule
{
    /**
     * Validate the uploaded file's detected image content, not its filename.
     * SVG is intentionally excluded because it can contain active content.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('The :attribute upload is invalid.');

            return;
        }

        $imageInfo = @getimagesize($value->getRealPath());
        $mimeType = $imageInfo['mime'] ?? null;

        if (! in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            $fail('The :attribute must contain a valid JPEG, PNG, or WebP image.');
        }
    }
}
