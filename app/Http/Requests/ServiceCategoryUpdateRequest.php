<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceCategoryUpdateRequest extends FormRequest
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
            'name'        => ['sometimes', 'string', 'max:150', Rule::unique('service_categories')->ignore($categoryId)],
            'description' => ['sometimes', 'nullable', 'string'],
            'icon'        => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
