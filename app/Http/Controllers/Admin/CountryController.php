<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('id', 'desc')->paginate(10);

        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.countries.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:countries,name'],
        ]);

        Country::create($validated);

        return redirect()->route('admin.countries.index')
            ->with('success', 'Страна успешно добавлена.');
    }

    public function edit(Country $country)
    {
        return view('admin.countries.form', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:countries,name,' . $country->id],
        ]);

        $country->update($validated);

        return redirect()->route('admin.countries.index')
            ->with('success', 'Страна успешно обновлена.');
    }

    public function destroy(Country $country)
    {
        if ($country->films()->exists()) {
            return back()->with('error', 'Нельзя удалить страну, к которой привязаны фильмы.');
        }

        $country->delete();

        return redirect()->route('admin.countries.index')
            ->with('success', 'Страна успешно удалена.');
    }
}
