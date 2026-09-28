<?php

namespace App\Services;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;


class ModulePermissionService
{
    public function getVisibleModulesForUser(?User $user): Collection
    {
        if (!$user) {
            return new Collection();
        }

        if ($user->isSuperAdmin()) {
            return Module::active()
                ->navigation()
                ->roots()
                ->with(['children' => fn($q) => $q->active()->navigation()])
                ->get();
        }

        $allowedIds = $this->resolveAllowedModuleIds($user);

        if (empty($allowedIds)) {
            return new Collection();
        }

        return Module::active()
            ->navigation()
            ->roots()
            ->whereIn('id', $allowedIds)
            ->with(['children' => function ($q) use ($allowedIds) {
                $q->active()->navigation()->whereIn('id', $allowedIds);
            }])
            ->get();
    }

    public function resolveAllowedModuleIds(User $user): array
    {
        return Cache::remember(
            "user.{$user->id}.modules.allowed",
            now()->addHour(),
            function () use ($user) {
                $fromRoles = $user->moduleIdsFromRoles();
                $overrides = $user->moduleOverridesMap();

                $allowed = collect($fromRoles);

                foreach ($overrides as $moduleId => $granted) {
                    if ($granted) {
                        $allowed->push($moduleId);
                    } else {
                        $allowed = $allowed->reject(fn($id) => $id == $moduleId);
                    }
                }

                return $allowed->unique()->values()->toArray();
            }
        );
    }

    public function userCanSeeModule(User $user, Module $module): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return in_array($module->id, $this->resolveAllowedModuleIds($user));
    }

    public function invalidateUserCache(User $user): void
    {
        Cache::forget("user.{$user->id}.modules.allowed");
    }
}