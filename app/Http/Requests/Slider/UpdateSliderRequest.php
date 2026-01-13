<?php

namespace App\Http\Requests\Slider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'image_url' => 'sometimes|required|string|max:500',
            'link_type' => 'sometimes|required|in:ad,category,external',
            'link_value' => 'nullable|string|max:500',
            'slider_type' => 'sometimes|required|in:homepage,search_results,category',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.max' => 'The title may not be greater than 200 characters.',
            'image_url.required' => 'The image URL is required.',
            'link_type.required' => 'The link type is required.',
            'link_type.in' => 'The link type must be ad, category, or external.',
            'slider_type.required' => 'The slider type is required.',
            'slider_type.in' => 'The slider type must be homepage, search_results, or category.',
            'ends_at.after' => 'The end date must be after the start date.',
        ];
    }
}
