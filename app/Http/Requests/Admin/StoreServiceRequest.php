<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', 'prestataire')],
            'service_category_id' => ['required', 'exists:service_categories,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'municipality_id' => [
                'nullable',
                Rule::exists('municipalities', 'id')->where(fn ($query) => $query->where('city_id', $this->integer('city_id'))),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:5000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'price_min' => ['required', 'integer', 'min:0', 'max:99999999'],
            'price_max' => ['required', 'integer', 'gte:price_min', 'max:99999999'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
