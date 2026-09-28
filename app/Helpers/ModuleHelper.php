<?php

namespace App\Helpers;

use App\Models\Module;
use Illuminate\Support\Facades\Schema;

class ModuleHelper
{
    public static function getNavigationModules()
    {
        try {
            if (Schema::hasTable('modules')) {
                return Module::active()->navigation()->get();
            }
            return collect([]);
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    public static function getDashboardModules()
    {
        try {
            if (Schema::hasTable('modules')) {
                return Module::active()->dashboard()->get();
            }
            return collect([]);
        } catch (\Exception $e) {
            return collect([]);
        }
    }
}