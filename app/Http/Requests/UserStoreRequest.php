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
        $googleProfile = $this->session()->get('google_registration');
        $isGoogleRegistration = is_array($googleProfile)
            && isset($googleProfile['google_id'], $googleProfile['email'])
            && hash_equals((string) $googleProfile['email'], (string) $this->input('email'));

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [$isGoogleRegistration ? 'nullable' : 'required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', Rule::in(['client', 'prestataire'])],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Le rôle doit être client ou prestataire.',
            'first_name' => 'Le prénom est obligatoire',
            'last_name' => 'Le nom de famille est obligatoire',
            'email' => 'Le champs email est obligatoire',
        ];
    }
}
