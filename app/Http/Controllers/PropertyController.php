<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyRequest;
use App\Models\Property\Property;
use App\Models\Property\PropertyCategory;
use App\Models\Property\PropertyImage;
use App\Models\Property\PropertyOption;
use App\Models\Property\PropertyVideo;
use App\Services\Contracts\Product\ImageStorageInterface;
use App\Services\Contracts\Property\VideoStorageInterface;
use App\Services\GeocodingService;
use App\Services\PropertyViewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    public function __construct(
        protected ImageStorageInterface $imageStorage,
        protected VideoStorageInterface $videoStorage,
        protected PropertyViewService   $viewService,
        protected GeocodingService      $geocoder,
    ) {}

    public function index(Request $request)
    {
        $query = Property::with(['category', 'primaryImage'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('address', 'LIKE', "%{$s}%")
                ->orWhere('zip_code', 'LIKE', "%{$s}%"));
        }

        if ($request->filled('category')) {
            $query->where('property_category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $properties = $query->paginate(15)->withQueryString();
        $categories = PropertyCategory::active()->orderBy('order')->get();

        return view('properties.index', compact('properties', 'categories'));
    }

    public function create()
    {
        return view('properties.create', $this->getFormOptions());
    }

    public function store(PropertyRequest $request)
    {
        try {
            DB::beginTransaction();

            $property = Property::create(
                $request->safe()->except([
                    'images', 'videos',
                    'delete_images', 'delete_videos',
                    'amenities', 'utilities',
                ])
            );

            $this->applyGeocoding($property);
            $this->handleUploads($property, $request);
            $this->syncPivots($property, $request);

            DB::commit();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Property created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creating property', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Error creating property: ' . $e->getMessage());
        }
    }

    public function show(Property $property)
    {
        $this->viewService->track($property, auth()->user());

        $property->load([
            'category', 'images', 'videos', 'primaryImage',
            'leaseTerm', 'parkingType', 'coolingSystem', 'heatingSystem', 'laundry',
            'amenities', 'utilities',
        ]);

        $stats = $this->viewService->getStats($property);

        return view('properties.show', compact('property', 'stats'));
    }

    public function edit(Property $property)
    {
        $property->load(['images', 'videos', 'amenities', 'utilities']);

        return view('properties.edit', array_merge(
            $this->getFormOptions(),
            ['property' => $property]
        ));
    }

    public function update(PropertyRequest $request, Property $property)
    {
        try {
            DB::beginTransaction();

            $property->update(
                $request->safe()->except([
                    'images', 'videos',
                    'delete_images', 'delete_videos',
                    'amenities', 'utilities',
                ])
            );

            $this->applyGeocoding($property);
            $this->deleteSelectedMedia($property, $request);
            $this->handleUploads($property, $request);
            $this->syncPivots($property, $request);

            DB::commit();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Property updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error updating property', ['id' => $property->id, 'error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Error updating property: ' . $e->getMessage());
        }
    }

    public function destroy(Property $property)
    {
        try {
            DB::beginTransaction();

            foreach ($property->images as $img) {
                $this->imageStorage->delete($img->image_path);
            }
            foreach ($property->videos as $vid) {
                $this->videoStorage->delete($vid->video_path);
            }

            $property->delete();

            DB::commit();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Property deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error deleting property', ['id' => $property->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Error deleting property: ' . $e->getMessage());
        }
    }

    protected function getFormOptions(): array
    {
        return [
            'categories'     => PropertyCategory::active()->orderBy('order')->get(),
            'leaseTerms'     => PropertyOption::forGroup('lease_term'),
            'parkingTypes'   => PropertyOption::forGroup('parking_type'),
            'coolingSystems' => PropertyOption::forGroup('cooling_system'),
            'heatingSystems' => PropertyOption::forGroup('heating_system'),
            'laundryOptions' => PropertyOption::forGroup('laundry'),
            'amenities'      => PropertyOption::forGroup('amenity'),
            'utilities'      => PropertyOption::forGroup('utility'),
        ];
    }

   
    protected function applyGeocoding(Property $property): void
    {
        $coords = $this->geocoder->geocode(
            $property->address,
            $property->city,
            $property->state,
            $property->zip_code
        );

        if ($coords) {
            $property->update([
                'latitude'  => $coords['lat'],
                'longitude' => $coords['lng'],
            ]);
        }
    }

    protected function handleUploads(Property $property, PropertyRequest $request): void
    {
        if ($request->hasFile('images')) {
            $existing = $property->images()->count();
            foreach ($request->file('images') as $i => $file) {
                $path = $this->imageStorage->store($file, 'properties/images');
                $property->images()->create([
                    'image_path' => $path,
                    'is_primary' => $existing === 0 && $i === 0,
                    'order'      => $existing + $i,
                ]);
            }
        }

        if ($request->hasFile('videos')) {
            $existing = $property->videos()->count();
            foreach ($request->file('videos') as $i => $file) {
                $path = $this->videoStorage->store($file, 'properties/videos');
                $property->videos()->create([
                    'video_path' => $path,
                    'order'      => $existing + $i,
                ]);
            }
        }
    }

    protected function deleteSelectedMedia(Property $property, PropertyRequest $request): void
    {
        if ($request->filled('delete_images')) {
            $images = PropertyImage::whereIn('id', $request->delete_images)
                ->where('property_id', $property->id)
                ->get();

            foreach ($images as $img) {
                $this->imageStorage->delete($img->image_path);
                $img->delete();
            }
        }

        if ($request->filled('delete_videos')) {
            $videos = PropertyVideo::whereIn('id', $request->delete_videos)
                ->where('property_id', $property->id)
                ->get();

            foreach ($videos as $vid) {
                $this->videoStorage->delete($vid->video_path);
                $vid->delete();
            }
        }
    }

    /**
     * Sincroniza amenities y utilities (relaciones muchos-a-muchos).
     */
    protected function syncPivots(Property $property, PropertyRequest $request): void
    {
        $property->amenities()->sync($request->input('amenities', []));
        $property->utilities()->sync($request->input('utilities', []));
    }
}