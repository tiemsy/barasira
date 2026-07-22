<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['client', 'admin', 'superadmin'], true);
    }

    public function rules(): array
    {
        return [
            'mission_id' => ['required', 'integer', 'exists:missions,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
