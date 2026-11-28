<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::with('gender')
            ->withCount(['reviews', 'ratings'])
            ->find($id);

        if (! $user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'id' => $user->id,
            'fio' => $user->fio,
            'email' => $user->email,
            'birthday' => $user->birthday?->format('Y-m-d'),
            'gender' => $user->gender ? [
                'id' => $user->gender->id,
                'name' => $user->gender->name,
            ] : null,
            'reviewCount' => (int) $user->reviews_count,
            'ratingCount' => (int) $user->ratings_count,
        ], 200);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'fio' => ['required', 'string', 'min:2', 'max:150'],
            'email' => [
                'required',
                'email',
                'min:4',
                'max:50',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'birthday' => ['required', 'date'],
            'gender_id' => ['required', 'exists:genders,id'],
        ]);

        $user->update($validated);

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->delete();

        return response()->noContent();
    }
}
