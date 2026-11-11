<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ApplicationModuleSeeder extends Seeder
{
    public function run(): void
    {
        $forms = Module::where('name', 'Forms')->whereNull('parent_id')->first();

        if (!$forms) {
            $forms = Module::create([
                'name' => 'Forms',
                'icon' => 'bi-file-earmark-text',
                'route_pattern' => 'qualifications.*',
                'order' => 10,
                'is_active' => true,
                'is_dashboard' => false,
            ]);
        }

        Module::updateOrCreate(
            ['name' => 'Application', 'parent_id' => $forms->id],
            [
                'icon' => 'bi-file-earmark-person',
                'route' => 'applications.index',
                'route_pattern' => 'applications.*',
                'order' => 2,
                'is_active' => true,
                'is_dashboard' => false,
            ]
        );
    }
}