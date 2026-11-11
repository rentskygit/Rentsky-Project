<?php

namespace App\Models;

use App\Models\Property\Property;
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
        'phone',
        'is_super_admin',
        'assigned_adviser_id',
        'property_of_interest_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_super_admin'    => 'boolean',
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

    public function qualification()
    {
        return $this->hasOne(Qualification::class);
    }

    public function application()
    {
        return $this->hasOne(Application::class);
    }

    public function assignedAdviser()
    {
        return $this->belongsTo(User::class, 'assigned_adviser_id');
    }

    public function assignedClients()
    {
        return $this->hasMany(User::class, 'assigned_adviser_id');
    }

    public function propertyOfInterest()
    {
        return $this->belongsTo(Property::class, 'property_of_interest_id');
    }

    public function showings()
    {
        return $this->hasMany(Showing::class)->orderByDesc('showing_date')->orderByDesc('showing_time');
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function isAdmin(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->roles()->where('roles.id', 1)->exists();
    }

    public function isAdviser(): bool
    {
        return $this->roles()->where(function ($q) {
            $q->whereIn('roles.slug', ['adviser', 'advisor'])
              ->orWhereIn('roles.name', ['Adviser', 'Advisor']);
        })->exists();
    }

    public function isManager(): bool
    {
        return $this->roles()->where(function ($q) {
            $q->whereIn('roles.slug', ['manager'])
              ->orWhereIn('roles.name', ['Manager']);
        })->exists();
    }

    public function isClient(): bool
    {
        return $this->roles()->where('roles.id', 2)->exists();
    }

    public function canManageShowings(): bool
    {
        return $this->isSuperAdmin()
            || $this->isAdmin()
            || $this->isAdviser()
            || $this->isManager();
    }

    public function canBeAssignedAsAgent(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->roles()->where(function ($q) {
            $q->where('roles.id', 1)
              ->orWhereIn('roles.slug', ['admin', 'adviser', 'advisor'])
              ->orWhereIn('roles.name', ['Admin', 'Adviser', 'Advisor']);
        })->exists();
    }

    public function getAgentRoleLabelsAttribute(): array
    {
        $labels = [];

        if ($this->isSuperAdmin()) {
            $labels[] = 'Super Admin';
        }

        foreach ($this->roles as $role) {
            $slug = strtolower($role->slug ?? $role->name);
            if (in_array($slug, ['admin', 'adviser', 'advisor'], true)) {
                $labels[] = $role->name;
            }
        }

        return array_values(array_unique($labels));
    }

    public function canSeeSidebar(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->roles()->where('roles.id', 1)->exists();
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

    public function getPhoneAttribute(): ?string
    {
        $direct = $this->attributes['phone'] ?? null;
        if (!empty($direct)) {
            return $direct;
        }

        if ($this->qualification && !empty($this->qualification->data['phone'])) {
            return $this->qualification->data['phone'];
        }

        if ($this->application && !empty($this->application->data['phone'])) {
            return $this->application->data['phone'];
        }

        return null;
    }

    public function getFormattedPhoneAttribute(): ?string
    {
        $phone = $this->phone;
        if (!$phone) {
            return null;
        }

        if (preg_match('/^\+1(\d{3})(\d{3})(\d{4})$/', $phone, $m)) {
            return "({$m[1]}) {$m[2]}-{$m[3]}";
        }

        return $phone;
    }

    public function getClientStatusAttribute(): ?string
    {
        if ($this->application) {
            return 'application';
        }

        if ($this->qualification) {
            $scheduleNow = $this->qualification->data['schedule_now'] ?? null;
            if ($scheduleNow === 'Yes' || $scheduleNow === true || $scheduleNow === 1) {
                return 'showing';
            }
            return 'qualified';
        }

        return null;
    }

    public function getClientStatusLabelAttribute(): string
    {
        return match ($this->client_status) {
            'application' => 'Application',
            'showing'     => 'Showing',
            'qualified'   => 'Qualified',
            default       => 'New',
        };
    }

    public function getClientStatusBadgeAttribute(): string
    {
        return match ($this->client_status) {
            'application' => 'info',
            'showing'     => 'warning',
            'qualified'   => 'success',
            default       => 'neutral',
        };
    }

    public function getHasQualificationAttribute(): bool
    {
        return $this->qualification !== null;
    }
}