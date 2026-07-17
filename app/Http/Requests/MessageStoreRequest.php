<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageStoreRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('message')) {
            $this->merge([
                'message' => trim((string) $this->input('message')),
            ]);
        }
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'receiver_id' => ['required', 'integer', 'different:sender_id', 'exists:users,id'],
            'mission_id' => ['nullable', 'integer', 'exists:missions,id'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }
}
