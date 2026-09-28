<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategorySkillApiController extends Controller
{
    /**
     * Get list of active categories with their associated active skills
     */
    public function categoriesWithSkills(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->with(['skills' => function ($query) {
                $query->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Get skills for a specific category ID or slug
     */
    public function skillsByCategory(string $identifier): JsonResponse
    {
        $category = Category::where('is_active', true)
            ->where(function ($query) use ($identifier) {
                if (is_numeric($identifier)) {
                    $query->where('id', $identifier);
                } else {
                    $query->where('slug', $identifier)->orWhere('name', $identifier);
                }
            })
            ->first();

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        $skills = Skill::where('category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'category' => $category->only(['id', 'name', 'slug', 'icon']),
            'skills' => $skills
        ]);
    }

    /**
     * Admin Endpoint: Create skill via API
     */
    public function storeSkill(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $skill = Skill::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Skill created successfully',
            'data' => $skill->load('category')
        ], 201);
    }
}
