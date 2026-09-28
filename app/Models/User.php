<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    public function moduleOverrides()
    {
        return $this->belongsToMany(Module::class, 'module_user')
                    ->withPivot('granted')
                    ->withTimestamps();
    }


    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function moduleIdsFromRoles(): array
    {
        return $this->roles()
            ->with('modules:id')
            ->get()
            ->pluck('modules')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();
    }

    public function moduleOverridesMap(): array
    {
        return $this->moduleOverrides()
            ->pluck('granted', 'modules.id')
            ->toArray();
    }
}