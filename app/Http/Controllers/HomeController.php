<?php

namespace App\Http\Controllers;

use App\Models\Property\Property;
use App\Models\Property\PropertyCategory;
use App\Services\Contracts\Product\ProductServiceInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $properties = Property::active()
            ->with(['category', 'primaryImage', 'images', 'videos'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => [
                    'id'            => $p->id,
                    'slug'          => $p->slug,
                    'title'         => $p->category?->name ?? 'Property',
                    'address'       => $p->address,
                    'city'          => $p->city,
                    'state'         => $p->state,
                    'zip_code'      => $p->zip_code,
                    'unit_type'     => $p->unit_type,
                    'description'   => $p->description,
                    'restrictions'  => $p->restrictions,
                    'pet_friendly'  => (bool) $p->pet_friendly,
                    'max_pets'      => (int) $p->max_pets,
                    'bathrooms'     => $p->bathrooms,           
                    'square_feet'   => $p->square_feet,         
                    'rent'          => $p->rent,                
                    'rent_formatted'=> $p->formatted_rent,      
                    'available_date'=> $p->available_date?->format('m/d/Y'), 
                    'lat'           => $p->latitude,
                    'lng'           => $p->longitude,
                    'full_address'  => $p->full_address,
                    'primary_image' => $p->primaryImage?->image_url,
                    'images'        => $p->images->pluck('image_url')->filter()->values()->all(),
                    'videos'        => $p->videos->pluck('video_url')->filter()->values()->all(),
                    'views_count'   => (int) $p->views_count,
                    'url'           => route('public.properties.show', $p->slug),
                ]);

        $categories = PropertyCategory::active()
            ->withCount('properties')
            ->orderBy('order')
            ->get();

        return view('welcome', compact('properties', 'categories'));  
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return redirect()->route('welcome');
        }

        $properties = Property::active()
            ->where(function ($q) use ($query) {
                $q->where('address', 'LIKE', "%{$query}%")
                  ->orWhere('city', 'LIKE', "%{$query}%")
                  ->orWhere('zip_code', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->with(['category', 'primaryImage'])
            ->paginate(12);

        return view('public.search', compact('properties', 'query'));
    }
}