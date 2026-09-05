<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category]);
    }

    public function store(CategoryRequest $request)
    {
        Category::create([
            'name' => $request->validated()['name'],
            'slug' => \Illuminate\Support\Str::slug($request->validated()['name']),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category added.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update([
            'name' => $request->validated()['name'],
            'slug' => \Illuminate\Support\Str::slug($request->validated()['name']),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete "'.$category->name.'" — it still has products assigned. Reassign or delete those products first.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
