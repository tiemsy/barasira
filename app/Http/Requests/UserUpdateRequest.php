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
        $authenticatedUser = $this->user();
        $updatedUser = $this->route('user');

        return $authenticatedUser !== null
            && $updatedUser !== null
            && ($authenticatedUser->is($updatedUser) || $authenticatedUser->isAdmin());
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
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'hourly_rate' => [
                Rule::requiredIf(fn () => $this->route('user')?->role === 'prestataire'),
                'nullable', 'numeric', 'min:0', 'max:99999999.99',
            ],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_avatar' => ['sometimes', 'boolean'],
        ];
    }

}
