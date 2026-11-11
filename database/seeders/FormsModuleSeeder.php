<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FormsModuleSeeder extends Seeder
{
    public function run(): void
    {
        $forms = Module::updateOrCreate(
            ['name' => 'Forms'],
            [
                'icon'          => 'bi-file-earmark-text',
                'route'         => null,
                'url'           => null,
                'route_pattern' => 'qualifications.*',
                'order'         => 10,
                'is_active'     => true,
                'is_dashboard'  => false,
                'parent_id'     => null,
            ]
        );

        Module::updateOrCreate(
            ['name' => 'Qualification'],
            [
                'icon'          => 'bi-clipboard-check',
                'route'         => 'qualifications.index',
                'route_pattern' => 'qualifications.*',
                'order'         => 1,
                'is_active'     => true,
                'is_dashboard'  => false,
                'parent_id'     => $forms->id,
            ]
        );
    }
}