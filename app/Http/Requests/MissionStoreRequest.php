<?php

namespace App\Http\Requests;

use App\Models\Mission;
use Illuminate\Foundation\Http\FormRequest;

class MissionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Mission::class) ?? false;
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

            'city' => [
                'required',
                'string',
                'max:50',
            ],

            'address' => [
                'required',
                'string',
                'max:255',
            ],

            'skills' => [
                'nullable',
                'array',
                'max:10',
            ],

            'skills.*' => [
                'string',
                'max:100',
            ],

            'questions' => [
                'nullable',
                'array',
                'max:5',
            ],

            'questions.*' => [
                'string',
                'max:500',
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
                'after_or_equal:today',
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

            'skills.array' => 'Les compétences doivent être fournies sous forme de liste.',
            'skills.max' => 'Une mission ne peut contenir plus de 10 compétences.',
            'skills.*.string' => 'Chaque compétence doit être une chaîne de caractères.',
            'skills.*.max' => 'Une compétence ne peut pas dépasser 100 caractères.',

            'questions.array' => 'Les questions doivent être fournies sous forme de liste.',
            'questions.max' => 'Une mission ne peut contenir plus de 5 questions.',
            'questions.*.string' => 'Chaque question doit être une chaîne de caractères.',
            'questions.*.max' => 'Une question ne peut pas dépasser 500 caractères.',
        ];
    }
}
