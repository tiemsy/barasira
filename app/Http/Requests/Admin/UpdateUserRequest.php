<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');

        return ($this->user()?->isAdmin() ?? false)
            && (! $target?->isSuperAdmin() || $this->user()->isSuperAdmin());
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in($this->user()->isSuperAdmin()
                ? ['client', 'prestataire', 'admin', 'superadmin']
                : ['client', 'prestataire', 'admin'])],
            'bio' => ['nullable', 'string', 'max:2000'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'verified' => ['required', 'boolean'],
        ];
    }
}
