<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE properties ALTER COLUMN unit_type TYPE VARCHAR(100) USING unit_type::text");

        DB::statement("ALTER TABLE properties ALTER COLUMN unit_type SET DEFAULT 'Apartamento'");

        DB::statement("ALTER TABLE properties ALTER COLUMN unit_type SET NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE properties ALTER COLUMN unit_type TYPE VARCHAR(100)");
    }
};