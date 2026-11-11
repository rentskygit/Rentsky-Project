<?php

namespace App\Http\Controllers;

use App\Models\Property\Property;
use App\Models\Property\PropertyCategory;
use App\Services\PropertyViewService;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function __construct(
        protected PropertyViewService $viewService
    ) {}

    public function index(Request $request)
    {
        $query = Property::active()
            ->with(['category', 'primaryImage', 'amenities'])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('address', 'ILIKE', "%{$s}%")
                  ->orWhere('city', 'ILIKE', "%{$s}%")
                  ->orWhere('zip_code', 'ILIKE', "%{$s}%")
                  ->orWhere('description', 'ILIKE', "%{$s}%")
                  ->orWhere('unit_type', 'ILIKE', "%{$s}%")
                  ->orWhere('state', 'ILIKE', "%{$s}%");
                //   ->orWhere('amenities', 'ILIKE', "%{$s}%")
                //   ->orWhere('lease_term', 'ILIKE', "%{$s}%")
                //   ->orWhere('parking_type', 'ILIKE', "%{$s}%")
                //   ->orWhere('cooling_system', 'ILIKE', "%{$s}%")
                //   ->orWhere('heating_system', 'ILIKE', "%{$s}%")
                //   ->orWhere('laundry', 'ILIKE', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('property_category_id', $request->category);
        }

        if ($request->filled('min_rent')) {
            $query->where('rent', '>=', $request->min_rent);
        }

        if ($request->filled('max_rent')) {
            $query->where('rent', '<=', $request->max_rent);
        }

        $apartments = $query->paginate(12)->withQueryString();

        $categories = PropertyCategory::active()
            ->withCount('properties')
            ->orderBy('order')
            ->get();

        return view('apartments.index', compact('apartments', 'categories'));
    }

    public function show(string $slug)
    {
        $apartment = Property::where('slug', $slug)
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

        $this->viewService->track($apartment, auth()->user());

        $related = Property::active()
            ->where('property_category_id', $apartment->property_category_id)
            ->where('id', '!=', $apartment->id)
            ->with(['category', 'primaryImage'])
            ->limit(3)
            ->get();

        return view('apartments.show', compact('apartment', 'related'));
    }
}