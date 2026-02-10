<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name'  => ['sometimes', 'string', 'max:100'],
            'email'      => [
                'sometimes',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password'   => ['sometimes', 'nullable', 'string', 'min:6'],
            'phone'      => ['sometimes', 'nullable', 'string', 'max:30'],
            'role'       => ['sometimes', 'in:client,prestataire,admin'],
            'bio'        => ['sometimes', 'nullable', 'string'],
            'avatar_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'rating'     => ['sometimes', 'numeric', 'between:0,5'],
            'verified'   => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Le rôle doit être : client ou prestataire',
        ];
    }
}
