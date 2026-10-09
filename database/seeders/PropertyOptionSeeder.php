<?php

namespace Database\Seeders;

use App\Models\Property\PropertyOption;
use Illuminate\Database\Seeder;

class PropertyOptionSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'lease_term' => [
                ['Contact for details', 1],
                ['Monthly',             2],
                ['6 Months',            3],
                ['1 Year',              4],
            ],
            'parking_type' => [
                ['None',             1],
                ['Carport',          2],
                ['Garage Attached',  3],
                ['Garage Detached',  4],
                ['Off Street',       5],
                ['On Street',        6],
            ],
            'cooling_system' => [
                ['None',          1],
                ['Central Air',   2],
                ['Evaporative',   3],
                ['Geothermal',    4],
                ['Wall',          5],
                ['Solar',         6],
                ['Other',         7],
            ],
            'heating_system' => [
                ['None',        1],
                ['Baseboard',   2],
                ['Forced Air',  3],
                ['Heat Pump',   4],
                ['Radiant',     5],
                ['Stove',       6],
                ['Wall',        7],
                ['Other',       8],
            ],
            'laundry' => [
                ['None',         1],
                ['In Unit',      2],
                ['Shared',       3],
                ['Hookups',      4],
                ['Washer Only',  5],
                ['Other',        6],
            ],
            'amenity' => [
                ['Patio',      1, 'bi-tree'],
                ['Playground', 2, 'bi-brightness-high'],
                ['Pool',       3, 'bi-water'],
            ],
            'utility' => [
                ['Trash',       1, 'bi-trash'],
                ['Electricity', 2, 'bi-lightning-charge'],
                ['Cable',       3, 'bi-tv'],
                ['Water',       4, 'bi-droplet'],
                ['Gas',         5, 'bi-fire'],
                ['Internet',    6, 'bi-wifi'],
            ],
        ];

        foreach ($data as $group => $items) {
            foreach ($items as $item) {
                PropertyOption::updateOrCreate(
                    ['group' => $group, 'name' => $item[0]],
                    [
                        'icon'      => $item[2] ?? null,
                        'order'     => $item[1],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}