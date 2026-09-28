<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'route',
        'url',
        'route_pattern',
        'order',
        'is_active',
        'is_dashboard',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_dashboard' => 'boolean',
        'parent_id' => 'integer',
    ];


    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Module::class, 'parent_id')->orderBy('order');
    }

    public function roles()
    {
    return $this->belongsToMany(Role::class, 'role_module');
    }

    public function users()
    {
    return $this->belongsToMany(User::class, 'module_user')
                ->withPivot('granted')
                ->withTimestamps();
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNavigation($query)
    {
        return $query->where('is_dashboard', false)->orderBy('order');
    }

    public function scopeDashboard($query)
    {
        return $query->where('is_dashboard', true)->orderBy('order');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }


    public function getUrlAttribute($value)
    {
        if ($this->route && !$value) {
            try {
                return route($this->route);
            } catch (\Exception $e) {
                return '#';
            }
        }
        return $value ?? '#';
    }


    public function isActive()
    {
        if (!$this->route_pattern) {
            return false;
        }
        return request()->routeIs($this->route_pattern);
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}