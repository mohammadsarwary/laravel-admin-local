<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\AdminLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->get('page', 1));
        $limit = min(100, max(1, (int) $request->get('limit', 20)));
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Category::query()
            ->with(['parent:id,name'])
            ->withCount('ads');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $isActive = $status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        $total = $query->count();

        $categories = $query->orderBy('display_order')
            ->orderBy('name')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get()
            ->map(fn($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'parent_id' => $category->parent_id,
                'parent_name' => $category->parent?->name,
                'display_order' => $category->display_order,
                'is_active' => $category->is_active,
                'ads_count' => $category->ads_count,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            ]);

        return $this->success([
            'categories' => $categories,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon,
            'parent_id' => $request->parent_id,
            'display_order' => $request->display_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AdminLog::log($request->user(), 'create_category', $category->id, 'category', 'Category created: ' . $category->name);

        return $this->success($category, 'Category created successfully');
    }

    public function show(Category $category): JsonResponse
    {
        $category->load(['parent:id,name', 'children:id,name,parent_id']);
        $category->loadCount('ads');

        return $this->success([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'icon' => $category->icon,
            'parent_id' => $category->parent_id,
            'parent_name' => $category->parent?->name,
            'display_order' => $category->display_order,
            'is_active' => $category->is_active,
            'ads_count' => $category->ads_count,
            'children' => $category->children,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ]);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->parent_id == $category->id) {
            return $this->error('A category cannot be its own parent', 422);
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon,
            'parent_id' => $request->parent_id,
            'display_order' => $request->display_order ?? $category->display_order,
            'is_active' => $request->boolean('is_active', $category->is_active),
        ]);

        AdminLog::log($request->user(), 'update_category', $category->id, 'category', 'Category updated: ' . $category->name);

        return $this->success($category, 'Category updated successfully');
    }

    public function destroy(Request $request, Category $category): JsonResponse
    {
        $categoryName = $category->name;
        $adsCount = $category->ads()->count();

        if ($adsCount > 0) {
            return $this->error("Cannot delete category with {$adsCount} active ads. Please reassign or delete the ads first.", 422);
        }

        if ($category->children()->count() > 0) {
            return $this->error('Cannot delete category with subcategories. Please delete or reassign subcategories first.', 422);
        }

        AdminLog::log($request->user(), 'delete_category', $category->id, 'category', 'Category deleted: ' . $categoryName);

        $category->delete();

        return $this->success(null, 'Category deleted successfully');
    }

    public function toggleStatus(Request $request, Category $category): JsonResponse
    {
        $category->is_active = !$category->is_active;
        $category->save();

        $status = $category->is_active ? 'activated' : 'deactivated';
        AdminLog::log($request->user(), 'toggle_category_status', $category->id, 'category', "Category {$status}: " . $category->name);

        return $this->success([
            'is_active' => $category->is_active,
        ], "Category {$status} successfully");
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'category_ids' => 'required|array',
            'category_ids.*' => 'integer|exists:categories,id',
        ]);

        $action = $request->action;
        $categoryIds = $request->category_ids;

        switch ($action) {
            case 'activate':
                Category::whereIn('id', $categoryIds)->update(['is_active' => true]);
                break;
            case 'deactivate':
                Category::whereIn('id', $categoryIds)->update(['is_active' => false]);
                break;
            case 'delete':
                $categories = Category::whereIn('id', $categoryIds)->get();
                foreach ($categories as $category) {
                    if ($category->ads()->count() === 0 && $category->children()->count() === 0) {
                        $category->delete();
                    }
                }
                break;
        }

        AdminLog::log(
            $request->user(),
            "bulk_{$action}",
            null,
            'category',
            "Bulk action on " . count($categoryIds) . " categories"
        );

        return $this->success(null, count($categoryIds) . ' categories updated successfully');
    }

    public function export()
    {
        $categories = Category::with(['parent:id,name'])
            ->withCount('ads')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $headers = ['ID', 'Name', 'Slug', 'Icon', 'Parent Category', 'Display Order', 'Active', 'Ads Count', 'Created At'];
        
        $callback = function() use ($categories, $headers) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, $headers);
            
            foreach ($categories as $category) {
                fputcsv($file, [
                    $category->id,
                    $category->name,
                    $category->slug,
                    $category->icon,
                    $category->parent?->name ?? 'N/A',
                    $category->display_order,
                    $category->is_active ? 'Yes' : 'No',
                    $category->ads_count,
                    $category->created_at,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="categories_export_' . date('Y-m-d') . '.csv"',
        ]);
    }
}
