<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ClientModuleSeeder extends Seeder
{
    public function run(): void
    {
        $module = Module::updateOrCreate(
            ['name' => 'Clients'],
            [
                'icon'          => 'bi-people',
                'route'         => 'clients.index',
                'route_pattern' => 'clients.*',
                'parent_id'     => null,
                'order'         => 15,
                'is_active'     => true,
                'is_dashboard'  => false,
                'is_public'     => false,
            ]
        );

        $adminRole = Role::where('id', 1)->first();
        if ($adminRole) {
            $adminRole->modules()->syncWithoutDetaching([$module->id]);
        }

        $adviserRoles = Role::where(function ($q) {
            $q->whereIn('slug', ['adviser', 'advisor'])
              ->orWhereIn('name', ['Adviser', 'Advisor']);
        })->get();

        foreach ($adviserRoles as $role) {
            $role->modules()->syncWithoutDetaching([$module->id]);
        }

        $clientRole = Role::where('id', 2)->first();
        if ($clientRole) {
            $clientRole->modules()->detach([$module->id]);
        }
    }
}