<?php
namespace App\Http\Controllers;

use App\Models\Property\PropertyCategory;
use Illuminate\Http\Request;

class PropertyCategoryController extends Controller
{
    public function index()
    {
        $categories = PropertyCategory::withCount('properties')->orderBy('order')->get();
        return view('property-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('property-categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:50',
            'order'       => 'required|integer|min:0',
            'is_active'   => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        PropertyCategory::create($data);
        return redirect()->route('property-categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(PropertyCategory $propertyCategory)
    {
        return view('property-categories.edit', ['category' => $propertyCategory]);
    }

    public function update(Request $request, PropertyCategory $propertyCategory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:50',
            'order'       => 'required|integer|min:0',
            'is_active'   => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        $propertyCategory->update($data);
        return redirect()->route('property-categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(PropertyCategory $propertyCategory)
    {
        $propertyCategory->delete();
        return redirect()->route('property-categories.index')->with('success', 'Categoría eliminada.');
    }
}