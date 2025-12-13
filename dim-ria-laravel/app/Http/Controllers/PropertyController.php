<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::where('status', 'active')->paginate(12);
        return view('properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('properties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'rooms' => 'required|integer|min:1',
            'area' => 'required|numeric|min:0',
            'type' => 'required|in:apartment,house,commercial',
        ]);

        Property::create($validated);

        return redirect()->route('properties.index')
            ->with('success', 'Нерухомість успішно додана!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        return view('properties.show', compact('property'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        return view('properties.edit', compact('property'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'rooms' => 'required|integer|min:1',
            'area' => 'required|numeric|min:0',
            'type' => 'required|in:apartment,house,commercial',
            'status' => 'required|in:active,sold,rented',
        ]);

        $property->update($validated);

        return redirect()->route('properties.show', $property)
            ->with('success', 'Нерухомість успішно оновлена!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', 'Нерухомість успішно видалена!');
    }
}

