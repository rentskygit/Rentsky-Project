<?php
namespace Database\Seeders;

use App\Models\Property\PropertyCategory;
use Illuminate\Database\Seeder;

class PropertyCategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['name' => 'Estudio',       'icon' => 'bi-door-closed', 'order' => 1],
            ['name' => '1 Habitación',  'icon' => 'bi-house',       'order' => 2],
            ['name' => '2 Habitaciones','icon' => 'bi-house-door',  'order' => 3],
            ['name' => '3 Habitaciones','icon' => 'bi-buildings',   'order' => 4],
            ['name' => 'Penthouse',     'icon' => 'bi-building',    'order' => 5],
            ['name' => 'Suite',         'icon' => 'bi-stars',       'order' => 6],
        ];
        foreach ($cats as $c) {
            PropertyCategory::updateOrCreate(['name' => $c['name']], $c + ['is_active' => true]);
        }
    }
}