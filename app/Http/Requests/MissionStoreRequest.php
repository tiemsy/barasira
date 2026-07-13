<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MissionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'service_id' => [
                'required',
                'integer',
                'exists:services,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'address' => [
                'required',
                'string',
                'max:255',
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'date_start' => [
                'required',
                'date',
            ],

            'date_end' => [
                'nullable',
                'date',
                'after_or_equal:date_start',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Le service est obligatoire.',
            'service_id.exists' => 'Le service sélectionné est invalide.',
            'title.required' => 'Le titre de la mission est obligatoire.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix ne peut pas être négatif.',
            'date_start.date' => 'La date de début est invalide.',
            'date_end.date' => 'La date de fin est invalide.',
            'date_end.after_or_equal' => 'La date de fin doit être égale ou postérieure à la date de début.',
        ];
    }
}
