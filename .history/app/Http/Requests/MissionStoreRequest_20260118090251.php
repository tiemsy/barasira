<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MissionStoreRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'description' => 'required|string',
            'address' => 'required|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'date_start' => 'required|dateTime',
            'date_end' => 'required|dateTime',
        ];
    }
}
