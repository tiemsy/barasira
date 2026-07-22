<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'website_url' => ['nullable', 'url:http,https', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:150'],
            'contact_position' => ['nullable', 'string', 'max:150'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'is_published' => ['required', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'promotion_id' => [
                'nullable',
                'integer',
                Rule::exists('partner_promotions', 'id')->where(
                    'partner_id', $this->route('partner')?->id ?? 0
                ),
            ],
            'promotion_amount' => ['nullable', 'required_with:promotion_starts_at,promotion_ends_at', 'numeric', 'min:1', 'max:999999999999'],
            'promotion_starts_at' => ['nullable', 'required_with:promotion_amount,promotion_ends_at', 'date'],
            'promotion_ends_at' => ['nullable', 'required_with:promotion_amount,promotion_starts_at', 'date', 'after:promotion_starts_at'],
        ];
    }
}
