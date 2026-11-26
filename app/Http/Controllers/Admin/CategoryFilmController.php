<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Film;
use Illuminate\Http\Request;

class CategoryFilmController extends Controller
{
    public function index(Film $film)
    {
        $categories = $film->categories()->with('parent')->orderBy('name')->get();

        return view('admin.films.categories.index', compact('film', 'categories'));
    }

    public function create(Film $film)
    {
        $attachedIds = $film->categories()->pluck('categories.id');
        $availableCategories = Category::whereNotIn('id', $attachedIds)
            ->orderBy('name')
            ->get();

        return view('admin.films.categories.create', compact('film', 'availableCategories'));
    }

    public function store(Request $request, Film $film)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        if (! $film->categories()->where('categories.id', $validated['category_id'])->exists()) {
            $film->categories()->attach($validated['category_id']);
        }

        return redirect()->route('admin.films.categories.index', $film)
            ->with('success', 'Жанр успешно привязан к фильму.');
    }

    public function destroy(Film $film, Category $category)
    {
        $film->categories()->detach($category->id);

        return redirect()->route('admin.films.categories.index', $film)
            ->with('success', 'Жанр удалён у фильма.');
    }
}
