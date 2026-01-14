<?php

namespace App\Http\Requests\Ad;

use App\Enums\AdCondition;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ad = $this->route('ad');
        return $ad && $this->user()->id === $ad->user_id;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:5', 'max:200'],
            'description' => ['sometimes', 'string', 'min:20'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'location' => ['sometimes', 'string', 'max:100'],
            'condition' => ['sometimes', 'string', 'in:' . AdCondition::values()],
        ];
    }

    public function messages(): array
    {
        return [
            'title.min' => 'Title must be at least 5 characters',
            'title.max' => 'Title must not exceed 200 characters',
            'description.min' => 'Description must be at least 20 characters',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price must be at least 0',
            'category_id.exists' => 'Invalid category',
            'condition.in' => 'Invalid condition value',
        ];
    }
}
