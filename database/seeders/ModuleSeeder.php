<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            // Dashboard
            [
                'name' => 'Dashboard',
                'icon' => 'bi-speedometer2',
                'route' => 'dashboard',
                'route_pattern' => 'dashboard',
                'order' => 1,
                'is_active' => true,
                'is_dashboard' => true,
            ],
            // Productos (con hijos)
            [
                'name' => 'Productos',
                'icon' => 'bi-box-seam',
                'route' => 'products.index',
                'route_pattern' => 'products.*',
                'order' => 2,
                'is_active' => true,
                'is_dashboard' => false,
                'children' => [
                    [
                        'name' => 'Todos los productos',
                        'icon' => 'bi-list-ul',
                        'route' => 'products.index',
                        'route_pattern' => 'products.index',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Crear producto',
                        'icon' => 'bi-plus-circle',
                        'route' => 'products.create',
                        'route_pattern' => 'products.create',
                        'order' => 2,
                    ],
                ],
            ],
            // Categorías
            [
                'name' => 'Categorías',
                'icon' => 'bi-tags',
                'route' => null,
                'url' => '#',
                'route_pattern' => null,
                'order' => 3,
                'is_active' => true,
                'is_dashboard' => false,
            ],
            // Usuarios
            [
                'name' => 'Usuarios',
                'icon' => 'bi-people',
                'route' => 'users.index',
                'route_pattern' => 'users.*',
                'order' => 4,
                'is_active' => true,
                'is_dashboard' => false,
            ],
            // Roles
            [
                'name' => 'Roles',
                'icon' => 'bi-shield-lock',
                'route' => 'roles.index',
                'route_pattern' => 'roles.*',
                'order' => 5,
                'is_active' => true,
                'is_dashboard' => false,
            ],
            // Módulos
            [
                'name' => 'Módulos',
                'icon' => 'bi-grid-3x3-gap',
                'route' => 'modules.index',
                'route_pattern' => 'modules.*',
                'order' => 6,
                'is_active' => true,
                'is_dashboard' => false,
            ],
        ];

        foreach ($modules as $data) {
            $children = $data['children'] ?? [];
            unset($data['children']);

            $parent = Module::updateOrCreate(
                ['name' => $data['name']],
                $data
            );

            foreach ($children as $child) {
                $child['parent_id'] = $parent->id;
                $child['is_active'] = $child['is_active'] ?? true;
                $child['is_dashboard'] = $child['is_dashboard'] ?? false;
                $child['route'] = $child['route'] ?? null;
                $child['url'] = $child['url'] ?? null;

                Module::updateOrCreate(
                    ['name' => $child['name'], 'parent_id' => $parent->id],
                    $child
                );
            }
        }
    }
}