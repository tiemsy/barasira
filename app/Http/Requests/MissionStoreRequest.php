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
            'service_id.required' => __('missions.validation.service_required'),
            'service_id.exists' => __('missions.validation.service_invalid'),

            'title.required' => __('missions.validation.title_required'),

            'price.numeric' => __('missions.validation.price_numeric'),
            'price.min' => __('missions.validation.price_min'),

            'date_start.date' => __('missions.validation.start_date_invalid'),
            'date_end.date' => __('missions.validation.end_date_invalid'),
            'date_end.after_or_equal' => __('missions.validation.end_date_after_start'),

            'skills.array' => __('missions.validation.skills_array'),
            'skills.max' => __('missions.validation.skills_max'),
            'skills.*.string' => __('missions.validation.skill_string'),
            'skills.*.max' => __('missions.validation.skill_max'),

            'questions.array' => __('missions.validation.questions_array'),
            'questions.max' => __('missions.validation.questions_max'),
            'questions.*.string' => __('missions.validation.question_string'),
            'questions.*.max' => __('missions.validation.question_max'),
        ];
    }
}
