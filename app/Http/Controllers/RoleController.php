<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Role;
use App\Services\ModulePermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct(
        protected ModulePermissionService $permissionService
    ) {}

    public function index()
    {
        $roles = Role::withCount('users', 'modules')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $modules = Module::active()
            ->private()
            ->roots()
            ->with(['children' => fn($q) => $q->active()->private()->orderBy('order')])
            ->orderBy('order')
            ->get();

        return view('roles.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'modules' => 'nullable|array',
            'modules.*' => 'integer|exists:modules,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $publicModuleIds = Module::where('is_public', true)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $moduleIds = collect($validated['modules'] ?? [])
            ->map(fn($id) => (int) $id)
            ->reject(fn($id) => in_array($id, $publicModuleIds, true))
            ->values()
            ->toArray();

        $role->modules()->sync($moduleIds);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $modules = Module::active()
            ->private()
            ->roots()
            ->with(['children' => fn($q) => $q->active()->private()->orderBy('order')])
            ->orderBy('order')
            ->get();

        $role->load('modules');

        return view('roles.edit', compact('role', 'modules'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'modules' => 'nullable|array',
            'modules.*' => 'integer|exists:modules,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $publicModuleIds = Module::where('is_public', true)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $moduleIds = collect($validated['modules'] ?? [])
            ->map(fn($id) => (int) $id)
            ->reject(fn($id) => in_array($id, $publicModuleIds, true))
            ->values()
            ->toArray();

        $role->modules()->sync($moduleIds);

        $this->permissionService->invalidateRoleCache($role);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $clientRole = Role::where('id', 2)
            ->orWhere('name', 'Client')
            ->orWhere('slug', 'client')
            ->first();

        if ($clientRole && (int) $clientRole->id === (int) $role->id) {
            return redirect()->route('roles.index')
                ->with('error', 'The Client role cannot be deleted.');
        }

        $this->permissionService->invalidateRoleCache($role);

        if ($clientRole) {
            foreach ($role->users()->get() as $user) {
                $otherRoleIds = $user->roles()
                    ->where('roles.id', '!=', $role->id)
                    ->pluck('roles.id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();

                $newRoleIds = array_values(array_unique(array_merge($otherRoleIds, [$clientRole->id])));

                $user->roles()->sync($newRoleIds);
                $user->moduleOverrides()->sync([]);

                $this->permissionService->invalidateUserCache($user);
            }
        }

        $role->modules()->detach();
        $role->users()->detach();
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}