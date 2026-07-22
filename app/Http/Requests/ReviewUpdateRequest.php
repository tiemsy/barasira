<?php

namespace App\Http\Requests;

use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;

class ReviewUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $review = $this->route('review');

        return in_array($this->user()?->role, ['client', 'admin', 'superadmin'], true)
            && $review instanceof Review
            && $review->reviewer_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
