<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $userId)
    {
        $user = User::find($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ((int) $request->user()->id !== (int) $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'film_id' => ['required', 'exists:films,id'],
            'message' => ['required', 'string', 'min:4', 'max:1024'],
        ]);

        $review = Review::create([
            'film_id' => $validated['film_id'],
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_approved' => 0,
        ]);

        $review->load('film');

        return response()->json([
            'id' => $review->id,
            'film' => [
                'id' => $review->film->id,
                'name' => $review->film->name,
            ],
            'message' => $review->message,
            'is_approved' => (int) $review->is_approved,
            'created_at' => $review->created_at?->toISOString(),
        ], 201);
    }

    public function index($userId)
    {
        $user = User::find($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $reviews = $user->reviews()
            ->with('film')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'reviews' => $reviews->map(fn (Review $review) => [
                'id' => $review->id,
                'film' => $review->film ? [
                    'id' => $review->film->id,
                    'name' => $review->film->name,
                ] : null,
                'message' => $review->message,
                'is_approved' => (int) $review->is_approved,
                'created_at' => $review->created_at?->toISOString(),
            ])->values(),
        ], 200);
    }

    public function destroy(Request $request, $userId, $id)
    {
        $user = User::find($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ((int) $request->user()->id !== (int) $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $review = $user->reviews()->find($id);

        if (! $review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        $review->delete();

        return response()->noContent();
    }
}
