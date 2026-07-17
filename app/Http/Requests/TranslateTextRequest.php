<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslateTextRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'max:10000'],
            'source_locale' => ['required', 'string', 'max:10'],
            'target_locale' => ['required', 'string', 'different:source_locale', 'max:10'],
            'context' => ['sometimes', 'array'],
        ];
    }
}
