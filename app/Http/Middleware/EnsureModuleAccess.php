<?php

namespace App\Http\Middleware;

use App\Models\Module;
use App\Services\ModulePermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleAccess
{
    public function __construct(
        protected ModulePermissionService $permissionService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $route = $request->route();
        if (!$route) {
            return $next($request);
        }

        $routeName = $route->getName();
        if (!$routeName) {
            return $next($request);
        }

        if ($this->isProfileRoute($routeName, $route, $user)) {
            return $next($request);
        }

        $module = Module::active()
            ->whereNotNull('route_pattern')
            ->where('is_dashboard', false)
            ->get()
            ->first(fn($m) => $request->routeIs($m->route_pattern));

        if (!$module) {
            return $next($request);
        }

        if ($module->is_public) {
            return $next($request);
        }

        $allowedIds = $this->permissionService->resolveAllowedModuleIds($user);

        if (in_array($module->id, $allowedIds)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'You do not have permission to access this module.',
            ], 403);
        }

        abort(403, 'You do not have permission to access this module.');
    }

    protected function isProfileRoute(string $routeName, $route, $user): bool
    {
        if (in_array($routeName, ['logout', 'dashboard', 'welcome'], true)) {
            return true;
        }

        return false;
    }
}
