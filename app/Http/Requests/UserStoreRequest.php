<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:150', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'role'       => ['required', 'in:client,prestataire,admin'],
            'bio'        => ['nullable', 'string'],
            'avatar_url' => ['nullable', 'url', 'max:255'],
            'rating'     => ['nullable', 'numeric', 'between:0,5'],
            'verified'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Le rôle doit être : client, prestataire ou admin.',
        ];
    }
}
