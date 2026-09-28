<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class CategoryAdminController extends Controller
{
    public function index(): View
    {
        $categories = Category::latest()->get();

        // Get count of registered staff per category
        $categoryCounts = [];
        foreach ($categories as $cat) {
            $categoryCounts[$cat->name] = Professional::where('category', $cat->name)
                ->orWhere('category', $cat->slug)
                ->count();
        }

        return view('admin.categories.index', compact('categories', 'categoryCounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'min_rate' => ['nullable', 'numeric', 'min:0'],
            'max_rate' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['min_rate'] = $validated['min_rate'] ?? 0;
        $validated['max_rate'] = $validated['max_rate'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;

        Category::create($validated);

        return back()->with('success', 'Professional staff category created successfully!');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'min_rate' => ['nullable', 'numeric', 'min:0'],
            'max_rate' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['min_rate'] = $validated['min_rate'] ?? 0;
        $validated['max_rate'] = $validated['max_rate'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return back()->with('success', 'Category updated successfully!');
    }

    public function toggleStatus(Category $category): RedirectResponse
    {
        $category->update(['is_active' => !$category->is_active]);

        $statusLabel = $category->is_active ? 'ACTIVATED' : 'DEACTIVATED';
        return back()->with('success', "Category '{$category->name}' has been {$statusLabel}.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        $catName = $category->name;
        $category->delete();

        return back()->with('success', "Category '{$catName}' deleted successfully.");
    }
}
