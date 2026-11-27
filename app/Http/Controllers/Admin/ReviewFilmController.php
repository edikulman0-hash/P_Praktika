<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewFilmController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['film', 'user']);

        $selectedFilm = null;
        if ($request->filled('film_id')) {
            $query->where('film_id', $request->film_id);
            $selectedFilm = Film::find($request->film_id);
        }

        if ($request->get('status') === 'pending') {
            $query->where('is_approved', 0);
        } elseif ($request->get('status') === 'approved') {
            $query->where('is_approved', 1);
        }

        $reviews = $query->orderBy('is_approved', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $films = Film::orderBy('name')->get();

        return view('admin.reviews.index', compact('reviews', 'films', 'selectedFilm'));
    }

    public function approved(Review $review)
    {
        $review->update(['is_approved' => 1]);

        return redirect()->back()
            ->with('success', 'Отзыв успешно одобрен.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->back()
            ->with('success', 'Отзыв успешно удалён.');
    }
}
