<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_category_id' => ['required', 'integer', 'exists:property_categories,id'],
            'address'              => ['required', 'string', 'max:255'],
            'city'                 => ['nullable', 'string', 'max:100'],
            'state'                => ['nullable', 'string', 'max:100'],
            'zip_code'             => ['required', 'string', 'regex:/^\d{5}$/'],
            'unit_type'            => ['required', 'string', 'max:100'],

            'bathrooms'            => ['required', 'string', 'regex:/^\d+(\.\d)?$/'],
            'square_feet'          => ['required', 'integer', 'min:100', 'max:100000'],
            'virtual_tour_url'     => ['nullable', 'url', 'max:500'],

            'lease_term_id'        => ['required', 'integer', 'exists:property_options,id'],
            'rent'                 => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'deposit'              => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'available_date'       => ['required', 'date_format:m/d/Y'],

            'parking_type_id'      => ['nullable', 'integer', 'exists:property_options,id'],
            'parking_spot'         => ['nullable', 'string', 'regex:/^\d+$/', 'max:20'],
            'cooling_system_id'    => ['nullable', 'integer', 'exists:property_options,id'],
            'heating_system_id'    => ['nullable', 'integer', 'exists:property_options,id'],
            'laundry_id'           => ['nullable', 'integer', 'exists:property_options,id'],

            'amenities'            => ['nullable', 'array'],
            'amenities.*'          => ['integer', 'exists:property_options,id'],
            'utilities'            => ['nullable', 'array'],
            'utilities.*'          => ['integer', 'exists:property_options,id'],

            'pet_friendly'         => ['boolean'],
            'max_pets'             => ['nullable', 'integer', 'min:0', 'max:20'],
            'allow_cats'           => ['boolean'],
            'allow_small_dogs'     => ['boolean'],
            'small_dog_max_weight' => ['nullable', 'integer', 'min:1', 'max:200', 'required_if:allow_small_dogs,1'],
            'allow_large_dogs'     => ['boolean'],

            'income_to_rent_ratio'              => ['nullable', 'integer', 'min:1', 'max:20'],
            'allow_eviction_older_than_5_years' => ['boolean'],

            'description'          => ['nullable', 'string'],
            'restrictions'         => ['nullable', 'string'],
            'internal_restrictions'=> ['nullable', 'string'],
            'status'               => ['required', Rule::in(['active', 'inactive'])],

            'images'               => ['nullable', 'array', 'max:35'],
            'images.*'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],

            'videos'               => ['nullable', 'array'],
            'videos.*'             => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:20480'],

            'delete_images'        => ['nullable', 'array'],
            'delete_images.*'      => ['exists:property_images,id'],
            'delete_videos'        => ['nullable', 'array'],
            'delete_videos.*'      => ['exists:property_videos,id'],

            'image_order'          => ['nullable', 'array'],
            'image_order.*'        => ['integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'property_category_id.required'    => 'You must select a title (category) for the property.',
            'zip_code.regex'                   => 'ZIP code must be exactly 5 digits.',
            'bathrooms.regex'                  => 'Bathrooms must be a number, optionally with one decimal (e.g. 1 or 1.5).',
            'square_feet.min'                  => 'Square feet must be at least 100.',
            'available_date.date_format'       => 'Move-in date must be in MM/DD/YYYY format.',
            'parking_spot.regex'               => 'Parking spot must contain numbers only.',
            'small_dog_max_weight.required_if' => 'Indicate the maximum weight for small dogs.',
            'max_pets.required_if'             => 'Indicate how many pets are allowed if the property is pet friendly.',
            'images.max'                       => 'A property can have at most 35 images.',
            'images.*.max'                     => 'Each image must not exceed 5MB.',
            'videos.*.max'                     => 'Each video must not exceed 20MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'pet_friendly'    => $this->boolean('pet_friendly'),
            'allow_cats'      => $this->boolean('allow_cats'),
            'allow_small_dogs'=> $this->boolean('allow_small_dogs'),
            'allow_large_dogs'=> $this->boolean('allow_large_dogs'),
            'allow_eviction_older_than_5_years' => $this->boolean('allow_eviction_older_than_5_years'),
            'max_pets'        => $this->boolean('pet_friendly') ? (int) $this->input('max_pets', 0) : 0,
            'small_dog_max_weight' => $this->boolean('allow_small_dogs')
                ? (int) $this->input('small_dog_max_weight', 0)
                : null,
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $newCount = is_array($this->file('images')) ? count($this->file('images')) : 0;
            $deletedCount = is_array($this->input('delete_images')) ? count($this->input('delete_images')) : 0;

            $existingCount = 0;
            $property = $this->route('property');
            if ($property instanceof \App\Models\Property\Property) {
                $existingCount = $property->images()->count();
            }

            $total = max(0, $existingCount - $deletedCount) + $newCount;

            if ($total > 35) {
                $v->errors()->add(
                    'images',
                    "A property can have at most 35 images in total. You would have {$total} " .
                    "(existing: {$existingCount}, deleting: {$deletedCount}, new: {$newCount})."
                );
            }
        });
    }
}