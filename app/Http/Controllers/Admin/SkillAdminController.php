<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class SkillAdminController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCategoryId = $request->query('category_id');

        $categories = Category::withCount('skills')->orderBy('name')->get();

        $skillsQuery = Skill::with('category')->latest();

        if (!empty($selectedCategoryId) && $selectedCategoryId !== 'all') {
            $skillsQuery->where('category_id', $selectedCategoryId);
        }

        $skills = $skillsQuery->get();

        return view('admin.skills.index', compact('skills', 'categories', 'selectedCategoryId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;

        Skill::create($validated);

        return back()->with('success', "Skill '{$validated['name']}' added successfully under selected category!");
    }

    public function update(Request $request, Skill $skill): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $skill->update($validated);

        return back()->with('success', "Skill '{$skill->name}' updated successfully!");
    }

    public function toggleStatus(Skill $skill): RedirectResponse
    {
        $skill->update(['is_active' => !$skill->is_active]);

        $statusLabel = $skill->is_active ? 'ACTIVATED' : 'DEACTIVATED';
        return back()->with('success', "Skill '{$skill->name}' has been {$statusLabel}.");
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        $skillName = $skill->name;
        $skill->delete();

        return back()->with('success', "Skill '{$skillName}' removed successfully.");
    }

    public function getByCategory($categoryId): JsonResponse
    {
        $category = Category::find($categoryId);
        if (!$category) {
            $category = Category::where('slug', $categoryId)->orWhere('name', $categoryId)->first();
        }

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
                'skills' => []
            ], 404);
        }

        $skills = Skill::where('category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'category_id', 'name', 'slug', 'description']);

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'skills' => $skills
        ]);
    }
}
