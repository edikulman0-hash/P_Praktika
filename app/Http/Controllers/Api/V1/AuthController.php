<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validated = $request->validate([
            'fio' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email', 'min:4', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'max:65536'],
            'birthday' => ['required', 'date'],
            'gender_id' => ['required', 'exists:genders,id'],
        ]);

        $user = User::create($validated);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'id' => $user->id,
            'fio' => $user->fio,
        ], 201);
    }

    public function signin(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'min:4', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'max:65536'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Wrong email or password',
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'id' => $user->id,
            'fio' => $user->fio,
        ], 200);
    }

    public function signout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
