<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::withCount('films')
            ->orderBy('id')
            ->get();

        return response()->json([
            'countries' => $countries->map(fn (Country $country) => [
                'id' => $country->id,
                'name' => $country->name,
                'filmCount' => (int) $country->films_count,
            ])->values(),
        ]);
    }
}
