<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE properties DROP CONSTRAINT IF EXISTS properties_unit_type_check');
    }

    public function down(): void
    {
    }
};