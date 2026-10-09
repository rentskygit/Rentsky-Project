<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PropertyCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];


    public function properties()
    {
        return $this->hasMany(Property::class);
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if (empty($model->slug) && !empty($model->name)) {
                $base = Str::slug($model->name);
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
}