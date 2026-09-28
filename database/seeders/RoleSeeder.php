<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $allModules = Module::pluck('id')->toArray();

        // Rol Admin: todos los módulos
        $admin = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrador',
                'description' => 'Acceso completo a todos los módulos',
                'is_active' => true,
            ]
        );
        $admin->modules()->sync($allModules);

        // Rol Editor: solo módulos de productos
        $editor = Role::updateOrCreate(
            ['slug' => 'editor'],
            [
                'name' => 'Editor',
                'description' => 'Gestión de productos y categorías',
                'is_active' => true,
            ]
        );
        $productModules = Module::whereIn('name', ['Productos', 'Categorías'])->pluck('id')->toArray();
        $editor->modules()->sync($productModules);

        // Rol Viewer: solo lectura
        $viewer = Role::updateOrCreate(
            ['slug' => 'viewer'],
            [
                'name' => 'Visualizador',
                'description' => 'Solo puede ver el dashboard',
                'is_active' => true,
            ]
        );
        $dashboardModules = Module::where('is_dashboard', true)->pluck('id')->toArray();
        $viewer->modules()->sync($dashboardModules);
    }
}