<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'phone'      => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6'],
            'password_confirmed' => 'Les mots de passe ne correspondent pas.',
            'role'       => ['required', Rule::in(['client', 'prestataire', 'admin', 'superadmin'])],
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Le rôle doit être : client, prestataire, admin ou superadmin.',
            'first_name' => 'Le champs prénom est obligatoire',
            'last_name' => 'Le champs nom de famille est obligatoire'
        ];
    }
}
