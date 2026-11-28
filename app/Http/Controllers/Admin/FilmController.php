<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Country;
use App\Models\Film;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    public function index(Request $request)
    {
        $query = Film::with(['country', 'categories']);

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $films = $query->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $countries = Country::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.films.index', compact('films', 'countries', 'categories'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.films.form', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'country_id' => ['required', 'exists:countries,id'],
            'duration' => ['required', 'integer', 'min:1', 'max:65535'],
            'year_of_issue' => ['required', 'integer', 'min:1901', 'max:'.(date('Y') + 5)],
            'age' => ['required', 'integer', 'min:0', 'max:255'],
            'link_img' => ['nullable', 'string', 'max:255'],
            'link_kinopoisk' => ['nullable', 'string', 'max:255'],
            'link_video' => ['required', 'string', 'max:255'],
        ]);

        Film::create($validated);

        return redirect()->route('admin.films.index')
            ->with('success', 'Фильм успешно добавлен.');
    }

    public function edit(Film $film)
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.films.form', compact('film', 'countries'));
    }

    public function update(Request $request, Film $film)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'country_id' => ['required', 'exists:countries,id'],
            'duration' => ['required', 'integer', 'min:1', 'max:65535'],
            'year_of_issue' => ['required', 'integer', 'min:1901', 'max:'.(date('Y') + 5)],
            'age' => ['required', 'integer', 'min:0', 'max:255'],
            'link_img' => ['nullable', 'string', 'max:255'],
            'link_kinopoisk' => ['nullable', 'string', 'max:255'],
            'link_video' => ['required', 'string', 'max:255'],
        ]);

        $film->update($validated);

        return redirect()->route('admin.films.index')
            ->with('success', 'Фильм успешно обновлён.');
    }

    public function destroy(Film $film)
    {
        $film->delete();

        return redirect()->route('admin.films.index')
            ->with('success', 'Фильм успешно удалён.');
    }
}
