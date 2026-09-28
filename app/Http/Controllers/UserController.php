<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Services\ModulePermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function show(string $id)
    {
        $user = User::with(['roles', 'moduleOverrides'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::active()->orderBy('name')->get();
        $modules = Module::active()->orderBy('order')->get();

        $user->load(['roles', 'moduleOverrides']);

        $selectedRoles = old('roles', $user->roles->pluck('id')->toArray());
        $selectedModules = old('modules', $user->moduleOverrides()->wherePivot('granted', true)->pluck('modules.id')->toArray());

        return view('users.edit', compact('user', 'roles', 'modules', 'selectedRoles', 'selectedModules'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'array',
            'roles.*' => 'integer|exists:roles,id',
            'modules' => 'array',
            'modules.*' => 'integer|exists:modules,id',
            'is_super_admin' => 'boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_super_admin' => $request->boolean('is_super_admin'),
        ]);

        // Sincronizar roles
        $user->roles()->sync($request->input('roles', []));

        // Sincronizar overrides de módulos
        $moduleIds = $request->input('modules', []);
        $sync = [];
        foreach ($moduleIds as $id) {
            $sync[$id] = ['granted' => true];
        }
        $user->moduleOverrides()->sync($sync);

        // Invalidar caché de permisos
        $this->permissionService->invalidateUserCache($user);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        $this->permissionService->invalidateUserCache($user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}