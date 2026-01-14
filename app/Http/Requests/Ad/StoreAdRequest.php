<?php

namespace App\Http\Requests\Ad;

use App\Enums\AdCondition;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'description' => ['required', 'string', 'min:20'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'location' => ['required', 'string', 'max:100'],
            'condition' => ['nullable', 'string', 'in:' . AdCondition::values()],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required',
            'title.min' => 'Title must be at least 5 characters',
            'title.max' => 'Title must not exceed 200 characters',
            'description.required' => 'Description is required',
            'description.min' => 'Description must be at least 20 characters',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price must be at least 0',
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Invalid category',
            'location.required' => 'Location is required',
            'condition.in' => 'Invalid condition value',
        ];
    }
}
