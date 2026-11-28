<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;

class RatingController extends Controller
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
            'ball' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $exists = Rating::where('user_id', $user->id)
            ->where('film_id', $validated['film_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Score exist',
            ], 401);
        }

        $rating = Rating::create([
            'film_id' => $validated['film_id'],
            'user_id' => $user->id,
            'ball' => $validated['ball'],
        ]);

        $rating->load('film');

        return response()->json([
            'id' => $rating->id,
            'film' => [
                'id' => $rating->film->id,
                'name' => $rating->film->name,
            ],
            'score' => (int) $rating->ball,
            'created_at' => $rating->created_at?->toISOString(),
        ], 201);
    }

    public function index($userId)
    {
        $user = User::find($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $ratings = $user->ratings()
            ->with('film')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'ratings' => $ratings->map(fn (Rating $rating) => [
                'id' => $rating->id,
                'film' => $rating->film ? [
                    'id' => $rating->film->id,
                    'name' => $rating->film->name,
                ] : null,
                'score' => (int) $rating->ball,
                'created_at' => $rating->created_at?->toISOString(),
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

        $rating = $user->ratings()->find($id);

        if (! $rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        $rating->delete();

        return response()->noContent();
    }
}
