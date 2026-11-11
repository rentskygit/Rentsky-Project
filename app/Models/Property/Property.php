<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_category_id',
        'lease_term_id',
        'rent',
        'deposit',
        'available_date',
        'slug',
        'address',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longitude',
        'unit_type',
        'bathrooms',
        'square_feet',
        'virtual_tour_url',
        'description',
        'restrictions',
        'internal_restrictions',
        'pet_friendly',
        'max_pets',
        'allow_cats',
        'allow_small_dogs',
        'small_dog_max_weight',
        'allow_large_dogs',
        'income_to_rent_ratio',
        'allow_eviction_older_than_5_years',
        'parking_type_id',
        'parking_spot',
        'cooling_system_id',
        'heating_system_id',
        'laundry_id',
        'status',
        'views_count',
    ];

    protected $casts = [
        'pet_friendly'    => 'boolean',
        'allow_cats'      => 'boolean',
        'allow_small_dogs'=> 'boolean',
        'allow_large_dogs'=> 'boolean',
        'allow_eviction_older_than_5_years' => 'boolean',
        'max_pets'        => 'integer',
        'small_dog_max_weight' => 'integer',
        'income_to_rent_ratio' => 'integer',
        'square_feet'     => 'integer',
        'views_count'     => 'integer',
        'rent'            => 'decimal:2',
        'deposit'         => 'decimal:2',
        'latitude'        => 'float',
        'longitude'       => 'float',
        'available_date'  => 'date',
    ];


    public function category()     { return $this->belongsTo(PropertyCategory::class, 'property_category_id'); }
    public function leaseTerm()    { return $this->belongsTo(PropertyOption::class, 'lease_term_id'); }
    public function parkingType()  { return $this->belongsTo(PropertyOption::class, 'parking_type_id'); }
    public function coolingSystem(){ return $this->belongsTo(PropertyOption::class, 'cooling_system_id'); }
    public function heatingSystem(){ return $this->belongsTo(PropertyOption::class, 'heating_system_id'); }
    public function laundry()      { return $this->belongsTo(PropertyOption::class, 'laundry_id'); }

    public function amenities()
    {
        return $this->belongsToMany(PropertyOption::class, 'amenity_property');
    }

    public function utilities()
    {
        return $this->belongsToMany(PropertyOption::class, 'utility_property');
    }

    public function images()       { return $this->hasMany(PropertyImage::class)->orderBy('order'); }
    public function primaryImage() { return $this->hasOne(PropertyImage::class)->where('is_primary', true); }
    public function videos()       { return $this->hasMany(PropertyVideo::class)->orderBy('order'); }
    public function views()        { return $this->hasMany(PropertyView::class); }
    public function showings()     { return $this->hasMany(\App\Models\Showing::class); }


    public function scopeActive($q)      { return $q->where('status', 'active'); }
    public function scopePetFriendly($q) { return $q->where('pet_friendly', true); }


    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if (empty($model->slug) && !empty($model->address)) {
                $base = Str::slug($model->address);
                $slug = $base;
                $i    = 1;
                while (
                    static::where('slug', $slug)
                          ->where('id', '!=', $model->id ?? 0)
                          ->exists()
                ) {
                    $slug = $base . '-' . (++$i);
                }
                $model->slug = $slug;
            }
        });
    }

    // ── Helpers ──

    public function viewsByUser(int $userId): int
    {
        return $this->views()->where('user_id', $userId)->count();
    }

    public function getUnitTypeLabelAttribute(): string
    {
        return (string) $this->unit_type;
    }

    public function getFullAddressAttribute(): string
    {
        return trim(implode(', ', array_filter([
            $this->address, $this->city, $this->state, $this->zip_code,
        ])));
    }

    public function getFormattedRentAttribute(): ?string
    {
        return $this->rent !== null ? '$' . number_format((float) $this->rent, 2) : null;
    }

    public function getFormattedDepositAttribute(): ?string
    {
        return $this->deposit !== null ? '$' . number_format((float) $this->deposit, 2) : null;
    }
}