<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingFilmController extends Controller
{
    public function index(Request $request)
    {
        $query = Rating::with(['film', 'user']);

        $selectedFilm = null;
        $averageRating = null;

        if ($request->filled('film_id')) {
            $query->where('film_id', $request->film_id);
            $selectedFilm = Film::find($request->film_id);
            $averageRating = Rating::where('film_id', $request->film_id)->avg('ball');
        }

        $ratings = $query->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $films = Film::orderBy('name')->get();

        return view('admin.ratings.index', compact('ratings', 'films', 'selectedFilm', 'averageRating'));
    }

    public function destroy(Rating $rating)
    {
        $rating->delete();

        return redirect()->back()
            ->with('success', 'Оценка успешно удалена.');
    }
}
