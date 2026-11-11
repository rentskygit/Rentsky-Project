<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ApartmentModuleSeeder extends Seeder
{
    public function run(): void
    {
        Module::updateOrCreate(
            ['name' => 'Apartments'],
            [
                'icon'          => 'bi-house-door',
                'route'         => 'apartments.index',
                'route_pattern' => 'apartments.*',
                'url'           => null,
                'parent_id'     => null,
                'order'         => 1,
                'is_active'     => true,
                'is_dashboard'  => false,
                'is_public'     => true,
            ]
        );
    }
}