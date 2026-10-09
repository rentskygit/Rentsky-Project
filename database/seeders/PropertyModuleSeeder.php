<?php
namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class PropertyModuleSeeder extends Seeder
{
    public function run(): void
    {
        Module::updateOrCreate(
            ['name' => 'Propiedades'],
            [
                'icon'          => 'bi-house-door-fill',
                'route'         => 'properties.index',
                'route_pattern' => 'properties.*',
                'order'         => 5,
                'is_active'     => true,
                'is_dashboard'  => false,
            ]
        );

        Module::updateOrCreate(
            ['name' => 'Categorías de Propiedades'],
            [
                'icon'          => 'bi-tags-fill',
                'route'         => 'property-categories.index',
                'route_pattern' => 'property-categories.*',
                'order'         => 6,
                'is_active'     => true,
                'is_dashboard'  => false,
            ]
        );
    }
}