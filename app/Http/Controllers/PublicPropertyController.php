<?php

namespace App\Http\Controllers;

use App\Models\Property\Property;
use App\Services\PropertyViewService;

class PublicPropertyController extends Controller
{
    public function __construct(
        protected PropertyViewService $viewService,
    ) {}

    public function index(\Illuminate\Http\Request $request)
    {
        $query = Property::active()
            ->with(['category', 'primaryImage'])
            ->latest();

        if ($request->filled('category')) {
            $query->where('property_category_id', $request->category);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('address', 'ILIKE', "%{$s}%")
                  ->orWhere('city', 'ILIKE', "%{$s}%")
                  ->orWhere('zip_code', 'ILIKE', "%{$s}%");
            });
        }

        $properties = $query->paginate(12)->withQueryString();

        $categories = \App\Models\Property\PropertyCategory::active()
            ->withCount('properties')
            ->orderBy('order')
            ->get();

        return view('public.properties.index', compact('properties', 'categories'));
    }

    public function show(string $slug)
    {
        $property = Property::where('slug', $slug)
            ->with([
                'category',
                'primaryImage',
                'images',
                'videos',
                'leaseTerm',
                'parkingType',
                'coolingSystem',
                'heatingSystem',
                'laundry',
                'amenities',
                'utilities',
            ])
            ->firstOrFail();

        $this->viewService->track($property, auth()->user());

        return view('public.properties.show', compact('property'));
    }
}