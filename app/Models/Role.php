<?php

namespace App\Models;

use App\Services\ModulePermissionService;
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

    protected static function booted(): void
    {
        static::deleting(function (Role $role) {
            if ((int) $role->id === 2) {
                return false;
            }

            $clientRole = static::where('id', 2)
                ->orWhere('name', 'Client')
                ->orWhere('slug', 'client')
                ->first();

            if (!$clientRole || (int) $clientRole->id === (int) $role->id) {
                return;
            }

            $permissionService = app(ModulePermissionService::class);

            foreach ($role->users()->get() as $user) {
                $otherRoleIds = $user->roles()
                    ->where('roles.id', '!=', $role->id)
                    ->pluck('roles.id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();

                $newRoleIds = array_values(array_unique(array_merge($otherRoleIds, [$clientRole->id])));

                $user->roles()->sync($newRoleIds);
                $user->moduleOverrides()->sync([]);

                $permissionService->invalidateUserCache($user);
            }
        });
    }
}