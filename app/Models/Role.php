<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'role_module');
    }


    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = $value ? Str::slug($value) : Str::slug($this->attributes['name'] ?? '');
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}