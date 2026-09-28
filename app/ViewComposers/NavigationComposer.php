<?php

namespace App\ViewComposers;

use App\Services\ModulePermissionService;
use Illuminate\View\View;

class NavigationComposer
{
    public function __construct(
        protected ModulePermissionService $permissionService
    ) {}

    public function compose(View $view): void
    {
        $user = auth()->user();

        $view->with('navigationModules', $this->permissionService->getVisibleModulesForUser($user));
    }
}