<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PropertyOption extends Model
{
    use HasFactory;

    public const GROUP_LEASE_TERM     = 'lease_term';
    public const GROUP_PARKING_TYPE   = 'parking_type';
    public const GROUP_COOLING_SYSTEM = 'cooling_system';
    public const GROUP_HEATING_SYSTEM = 'heating_system';
    public const GROUP_LAUNDRY        = 'laundry';
    public const GROUP_AMENITY        = 'amenity';
    public const GROUP_UTILITY        = 'utility';

    protected $fillable = ['group', 'name', 'slug', 'icon', 'order', 'is_active'];
    protected $casts    = ['is_active' => 'boolean', 'order' => 'integer'];


    public function scopeGroup($q, string $group) { return $q->where('group', $group); }
    public function scopeActive($q)               { return $q->where('is_active', true); }
    public function scopeOrdered($q)              { return $q->orderBy('order')->orderBy('name'); }


    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if (empty($model->slug) && !empty($model->name)) {
                $base = Str::slug($model->name);
                $slug = $base;
                $i    = 1;
                while (
                    static::where('group', $model->group)
                          ->where('slug', $slug)
                          ->where('id', '!=', $model->id ?? 0)
                          ->exists()
                ) {
                    $slug = $base . '-' . (++$i);
                }
                $model->slug = $slug;
            }
        });
    }


    public static function forGroup(string $group)
    {
        return static::group($group)->active()->ordered()->get();
    }
}