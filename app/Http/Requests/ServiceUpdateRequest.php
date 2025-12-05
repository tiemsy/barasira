<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:service_categories,id'],
            'name'        => ['sometimes', 'string', 'max:150'],
            'description' => ['sometimes', 'string'],
            'icon'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'price_min'   => ['sometimes', 'numeric', 'min:0'],
            'price_max'   => ['sometimes', 'numeric', 'gte:price_min'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}
