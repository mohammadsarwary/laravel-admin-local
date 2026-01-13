<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Slider::query()->active();

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        $sliders = $query->ordered()->get()->map(function ($slider) {
            return [
                'id' => $slider->id,
                'title' => $slider->title,
                'description' => $slider->description,
                'image_url' => $slider->image_url,
                'link_type' => $slider->link_type,
                'link_value' => $slider->link_value,
                'slider_type' => $slider->slider_type,
                'created_at' => $slider->created_at,
            ];
        });

        return $this->success($sliders);
    }

    public function show(Slider $slider): JsonResponse
    {
        if (!$slider->isActive()) {
            return $this->notFound('Slider not found or inactive');
        }

        return $this->success([
            'id' => $slider->id,
            'title' => $slider->title,
            'description' => $slider->description,
            'image_url' => $slider->image_url,
            'link_type' => $slider->link_type,
            'link_value' => $slider->link_value,
            'slider_type' => $slider->slider_type,
            'created_at' => $slider->created_at,
            'updated_at' => $slider->updated_at,
        ]);
    }
}
