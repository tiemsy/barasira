<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MissionUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('date_end') && ! $this->has('date_start')) {
            $this->merge([
                'date_start' => $this->route('mission')?->date_start?->toDateTimeString(),
            ]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('mission')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['sometimes', 'integer', 'exists:services,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string', 'max:50'],
            'address' => ['sometimes', 'string', 'max:255'],
            'skills' => ['sometimes', 'nullable', 'array', 'max:10'],
            'skills.*' => ['string', 'max:100'],
            'questions' => ['sometimes', 'nullable', 'array', 'max:5'],
            'questions.*' => ['string', 'max:500'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'date_start' => ['sometimes', 'date'],
            'date_end' => ['sometimes', 'nullable', 'date', 'after_or_equal:date_start'],
            'status' => [
                'sometimes',
                Rule::in(['pending', 'in_progress', 'completed', 'cancelled']),
            ],
        ];
    }
}
