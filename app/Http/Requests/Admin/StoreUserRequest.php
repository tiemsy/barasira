<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in($this->user()->isSuperAdmin()
                ? ['client', 'prestataire', 'admin', 'superadmin']
                : ['client', 'prestataire', 'admin'])],
            'bio' => ['nullable', 'string', 'max:2000'],
            'hourly_rate' => ['nullable', 'required_if:role,prestataire', 'numeric', 'min:0', 'max:99999999.99'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'verified' => ['required', 'boolean'],
        ];
    }
}
