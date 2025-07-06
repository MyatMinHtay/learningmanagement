<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;

class CourseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = CourseCategory::withCount('courses')
                ->ordered()
                ->paginate(10);
            
            return view('admin.categories.index', compact('categories'));
        } catch (Exception $e) {
            Log::error('Error in categories index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load categories. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('admin.categories.create');
        } catch (Exception $e) {
            Log::error('Error in category create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load category creation form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:course_categories,name'],
                'color' => ['required', 'string', 'regex:/^#[0-9A-F]{6}$/i'],
                'sort_order' => ['integer', 'min:0'],
            ]);

            $validated['sort_order'] = $validated['sort_order'] ?? 0;

            CourseCategory::create($validated);

            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully.');
        } catch (Exception $e) {
            Log::error('Error in category store: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create category. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseCategory $category)
    {
        try {
            $category->load(['courses' => function ($query) {
                $query->latest()->take(10);
            }]);
            
            return view('admin.categories.show', compact('category'));
        } catch (Exception $e) {
            Log::error('Error in category show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load category details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseCategory $category)
    {
        try {
            return view('admin.categories.edit', compact('category'));
        } catch (Exception $e) {
            Log::error('Error in category edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load category edit form. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseCategory $category)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', Rule::unique('course_categories', 'name')->ignore($category->id)],
                'color' => ['required', 'string', 'regex:/^#[0-9A-F]{6}$/i'],
                'sort_order' => ['integer', 'min:0'],
            ]);

            $validated['sort_order'] = $validated['sort_order'] ?? $category->sort_order;

            $category->update($validated);

            return redirect()->route('categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in category update: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update category. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseCategory $category)
    {
        try {
            // Check if category has courses
            if ($category->courses()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete category that has courses assigned to it.');
            }

            $category->delete();

            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in category destroy: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete category. Please try again.');
        }
    }

    /**
     * Get active categories for dropdown/select
     */
    public function getActiveCategories()
    {
        try {
            $categories = CourseCategory::ordered()->get();
            return response()->json($categories);
        } catch (Exception $e) {
            Log::error('Error in getActiveCategories: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to load categories'], 500);
        }
    }
} 