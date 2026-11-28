<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Film;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    public function index(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $size = max(1, (int) $request->query('size', 10));
        $sortBy = in_array($request->query('sortBy'), ['name', 'year', 'rating'], true)
            ? $request->query('sortBy')
            : 'name';
        $sortDir = in_array(strtolower((string) $request->query('sortDir')), ['asc', 'desc'], true)
            ? strtolower((string) $request->query('sortDir'))
            : 'asc';
        $categoryId = (int) $request->query('category', 0);
        $countryId = (int) $request->query('country', 0);
        $search = $request->query('search');

        $query = Film::with(['country', 'categories'])
            ->withAvg('ratings', 'ball')
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', 1)]);

        if ($categoryId > 0) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId));
        }

        if ($countryId > 0) {
            $query->where('country_id', $countryId);
        }

        if ($search !== null && $search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($sortBy === 'year') {
            $query->orderBy('year_of_issue', $sortDir);
        } elseif ($sortBy === 'rating') {
            $query->orderBy('ratings_avg_ball', $sortDir);
        } else {
            $query->orderBy('name', $sortDir);
        }

        $total = (clone $query)->count();
        $films = $query->skip(($page - 1) * $size)->take($size)->get();

        return response()->json([
            'page' => $page,
            'size' => $films->count(),
            'total' => $total,
            'films' => $films->map(fn (Film $film) => $this->formatFilm($film))->values(),
        ]);
    }

    public function show($id)
    {
        $film = Film::with(['country', 'categories'])
            ->withAvg('ratings', 'ball')
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', 1)])
            ->find($id);

        if (! $film) {
            return response()->json(['message' => 'Film not found'], 404);
        }

        return response()->json($this->formatFilm($film));
    }

    public function reviews($filmId)
    {
        $film = Film::find($filmId);

        if (! $film) {
            return response()->json(['message' => 'Film not found'], 404);
        }

        $reviews = $film->reviews()
            ->with('user')
            ->where('is_approved', 1)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'reviews' => $reviews->map(fn ($review) => [
                'id' => $review->id,
                'user' => [
                    'id' => $review->user?->id,
                    'fio' => $review->user?->fio,
                ],
                'message' => $review->message,
                'created_at' => $review->created_at?->toISOString(),
            ])->values(),
        ]);
    }

    private function formatFilm(Film $film): array
    {
        return [
            'id' => $film->id,
            'name' => $film->name,
            'duration' => (int) $film->duration,
            'year_of_issue' => (int) $film->year_of_issue,
            'age' => (int) $film->age,
            'link_img' => $film->link_img,
            'link_kinopoisk' => $film->link_kinopoisk,
            'link_video' => $film->link_video,
            'created_at' => $film->created_at?->toISOString(),
            'country' => $film->country ? [
                'id' => $film->country->id,
                'name' => $film->country->name,
            ] : null,
            'categories' => $film->categories->map(fn ($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
            ])->values(),
            'ratingAvg' => $film->ratings_avg_ball !== null ? round((float) $film->ratings_avg_ball, 1) : 0,
            'reviewCount' => (int) $film->reviews_count,
        ];
    }
}
