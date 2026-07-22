<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerSponsorshipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:150'],
            'contact_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'website_url' => ['nullable', 'url:http,https', 'max:255'],
            'category' => ['required', Rule::in(config('partner_sponsorship.categories'))],
            'other_category' => ['nullable', 'required_if:category,other', 'string', 'max:100'],
            'plan' => ['required', Rule::in(array_keys(config('partner_sponsorship.plans')))],
            'message' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ];
    }
}
