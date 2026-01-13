<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Slider\StoreSliderRequest;
use App\Http\Requests\Slider\UpdateSliderRequest;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Slider::query()->with('createdBy:id,name,email');

        if ($request->filled('type')) {
            $query->where('slider_type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $page = max(1, (int) $request->get('page', 1));
        $limit = min(100, max(1, (int) $request->get('limit', 20)));

        $sliders = $query->orderByDesc('display_order')
            ->orderByDesc('created_at')
            ->paginate($limit, ['*'], 'page', $page);

        return $this->success([
            'sliders' => $sliders->items(),
            'page' => $sliders->currentPage(),
            'limit' => $sliders->perPage(),
            'total' => $sliders->total(),
        ]);
    }

    public function store(StoreSliderRequest $request): JsonResponse
    {
        $slider = Slider::create([
            'title' => trim($request->title),
            'description' => $request->description ? trim($request->description) : null,
            'image_url' => trim($request->image_url),
            'link_type' => $request->link_type,
            'link_value' => $request->link_value ? trim($request->link_value) : null,
            'slider_type' => $request->slider_type,
            'display_order' => $request->display_order ?? 0,
            'is_active' => $request->is_active ?? true,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'created_by' => $request->user()->id,
        ]);

        return $this->success([
            'id' => $slider->id,
            'title' => $slider->title,
            'slider_type' => $slider->slider_type,
            'is_active' => $slider->is_active,
        ], 'Slider created successfully', 201);
    }

    public function show(Slider $slider): JsonResponse
    {
        $slider->load('createdBy:id,name,email');

        return $this->success([
            'id' => $slider->id,
            'title' => $slider->title,
            'description' => $slider->description,
            'image_url' => $slider->image_url,
            'link_type' => $slider->link_type,
            'link_value' => $slider->link_value,
            'slider_type' => $slider->slider_type,
            'display_order' => $slider->display_order,
            'is_active' => $slider->is_active,
            'starts_at' => $slider->starts_at,
            'ends_at' => $slider->ends_at,
            'created_by' => [
                'id' => $slider->createdBy->id,
                'name' => $slider->createdBy->name,
                'email' => $slider->createdBy->email,
            ],
            'created_at' => $slider->created_at,
            'updated_at' => $slider->updated_at,
        ]);
    }

    public function update(UpdateSliderRequest $request, Slider $slider): JsonResponse
    {
        $slider->update($request->only([
            'title',
            'description',
            'image_url',
            'link_type',
            'link_value',
            'slider_type',
            'display_order',
            'is_active',
            'starts_at',
            'ends_at',
        ]));

        return $this->success([
            'id' => $slider->id,
            'title' => $slider->title,
            'slider_type' => $slider->slider_type,
            'is_active' => $slider->is_active,
        ], 'Slider updated successfully');
    }

    public function destroy(Slider $slider): JsonResponse
    {
        $slider->delete();

        return $this->success(null, 'Slider deleted successfully');
    }

    public function toggleStatus(Request $request, Slider $slider): JsonResponse
    {
        $slider->update(['is_active' => !$slider->is_active]);

        return $this->success([
            'id' => $slider->id,
            'is_active' => $slider->is_active,
        ], 'Slider status toggled successfully');
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:sliders,id',
        ]);

        $ids = $request->ids;
        $action = $request->action;

        $count = 0;

        switch ($action) {
            case 'activate':
                $count = Slider::whereIn('id', $ids)->update(['is_active' => true]);
                break;
            case 'deactivate':
                $count = Slider::whereIn('id', $ids)->update(['is_active' => false]);
                break;
            case 'delete':
                $count = Slider::whereIn('id', $ids)->delete();
                break;
        }

        return $this->success([
            'count' => $count,
            'action' => $action,
        ], "Bulk action completed successfully. {$count} sliders affected.");
    }
}
