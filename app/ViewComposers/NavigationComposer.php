<?php

namespace App\ViewComposers;

use App\Models\Module;
use App\Models\User;
use App\Services\ModulePermissionService;
use Illuminate\View\View;

class NavigationComposer
{
    public function __construct(
        protected ModulePermissionService $permissionService
    ) {}

    public function compose(View $view)
    {
        $publicModules = Module::active()
            ->public()
            ->navigation()
            ->roots()
            ->with(['children' => fn($q) => $q->active()->public()->navigation()->orderBy('order')])
            ->orderBy('order')
            ->get();

        $view->with('publicModules', $publicModules);

        $user = auth()->user();

        if (!$user) {
            $view->with('navigationModules', collect());
            $view->with('navbarModules', collect());
            return;
        }

        if ($user->canSeeSidebar()) {
            $modules = $this->permissionService->getVisibleModulesForUser($user);
            $view->with('navigationModules', $modules);
            $view->with('navbarModules', collect());
        } else {
            $modules = $this->getClientNavbarModules($user);
            $view->with('navigationModules', collect());
            $view->with('navbarModules', $modules);
        }
    }

    protected function getClientNavbarModules(User $user)
    {
        if ($user->isSuperAdmin()) {
            return Module::active()
                ->private()
                ->navigation()
                ->roots()
                ->with(['children' => fn($q) => $q->active()->private()->navigation()->orderBy('order')])
                ->orderBy('order')
                ->get();
        }

        $allowedIds = $this->permissionService->resolveAllowedModuleIds($user);

        if (empty($allowedIds)) {
            return collect();
        }

        return Module::active()
            ->private()
            ->navigation()
            ->roots()
            ->where(function ($q) use ($allowedIds) {
                $q->whereIn('id', $allowedIds)
                  ->orWhereHas('children', fn($c) => $c->active()->private()->navigation()->whereIn('id', $allowedIds));
            })
            ->with(['children' => function ($q) use ($allowedIds) {
                $q->active()->private()->navigation()->whereIn('id', $allowedIds)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
    }
}