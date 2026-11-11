<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Services\ModulePermissionService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected ModulePermissionService $permissionService
    ) {}

    public function index()
    {
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $clientRole = Role::where('id', 2)
            ->orWhere('name', 'Client')
            ->orWhere('slug', 'client')
            ->first();

        if ($clientRole) {
            $user->roles()->syncWithoutDetaching([$clientRole->id]);
        }

        $this->permissionService->invalidateUserCache($user);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(string $id)
    {
        $user = User::with(['roles', 'moduleOverrides'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::active()->orderBy('name')->get();

        $modules = Module::active()
            ->navigation()
            ->private()
            ->roots()
            ->with(['children' => fn($q) => $q->active()->navigation()->private()->orderBy('order')])
            ->orderBy('order')
            ->get();

        $user->load(['roles', 'moduleOverrides']);

        $selectedRoles = old('roles', $user->roles->pluck('id')->toArray());

        $roleModuleIds = $user->roles()
            ->with('modules:id')
            ->get()
            ->pluck('modules')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->values()
            ->toArray();

        $overridesMap = $user->moduleOverridesMap();

        $currentAccess = collect($roleModuleIds);

        foreach ($overridesMap as $moduleId => $granted) {
            if ($granted) {
                $currentAccess->push($moduleId);
            } else {
                $currentAccess = $currentAccess->reject(fn($id) => (int) $id === (int) $moduleId);
            }
        }

        $selectedModules = old('modules', $currentAccess->unique()->values()->toArray());

        return view('users.edit', compact(
            'user',
            'roles',
            'modules',
            'selectedRoles',
            'selectedModules',
            'roleModuleIds'
        ));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
            'modules' => 'nullable|array',
            'modules.*' => 'integer|exists:modules,id',
            'is_super_admin' => 'boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_super_admin' => $request->boolean('is_super_admin'),
        ]);

        $rolesChanged = false;

        if ($request->exists('roles_present')) {
            $roles = $request->input('roles', []) ?? [];
            $roles = array_values(array_unique(array_map('intval', array_filter($roles))));

            $clientRole = Role::where('id', 2)
                ->orWhere('name', 'Client')
                ->orWhere('slug', 'client')
                ->first();

            if (empty($roles) && $clientRole) {
                $roles = [$clientRole->id];
            }

            $currentRoleIds = $user->roles()
                ->pluck('roles.id')
                ->map(fn($id) => (int) $id)
                ->sort()
                ->values()
                ->toArray();

            $newRoleIds = collect($roles)->map(fn($id) => (int) $id)->sort()->values()->toArray();

            $rolesChanged = $currentRoleIds !== $newRoleIds;

            $user->roles()->sync($roles);

            if ($rolesChanged) {
                $user->moduleOverrides()->sync([]);
            }
        }

        if ($request->exists('modules_present') && !$rolesChanged) {
            $publicModuleIds = Module::where('is_public', true)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->toArray();

            $checkedModules = collect($request->input('modules', []) ?? [])
                ->map(fn($id) => (int) $id)
                ->reject(fn($id) => in_array($id, $publicModuleIds, true))
                ->values()
                ->toArray();

            $user->load('roles');

            $roleModuleIds = $user->roles()
                ->with('modules:id')
                ->get()
                ->pluck('modules')
                ->flatten()
                ->pluck('id')
                ->unique()
                ->map(fn($id) => (int) $id)
                ->reject(fn($id) => in_array($id, $publicModuleIds, true))
                ->values()
                ->toArray();

            $sync = [];

            foreach ($checkedModules as $moduleId) {
                if (!in_array($moduleId, $roleModuleIds, true)) {
                    $sync[$moduleId] = ['granted' => true];
                }
            }

            foreach ($roleModuleIds as $roleModuleId) {
                if (!in_array($roleModuleId, $checkedModules, true)) {
                    $sync[$roleModuleId] = ['granted' => false];
                }
            }

            $user->moduleOverrides()->sync($sync);
        }

        $this->permissionService->invalidateUserCache($user);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->permissionService->invalidateUserCache($user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}