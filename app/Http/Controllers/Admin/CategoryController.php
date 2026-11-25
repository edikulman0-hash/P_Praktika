<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::orderBy('name')->get();

        return view('admin.categories.form', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Жанр успешно добавлен.');
    }

    public function edit(Category $category)
    {
        $parents = Category::where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.form', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'parent_id' => ['nullable', 'exists:categories,id', 'not_in:' . $category->id],
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Жанр успешно обновлён.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return back()->with('error', 'Нельзя удалить жанр, у которого есть подкатегории.');
        }

        if ($category->films()->exists()) {
            return back()->with('error', 'Нельзя удалить жанр, к которому привязаны фильмы.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Жанр успешно удалён.');
    }
}
