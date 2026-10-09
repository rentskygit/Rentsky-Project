<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amenity_property', function (Blueprint $table) {
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_option_id')->constrained('property_options')->cascadeOnDelete();
            $table->primary(['property_id', 'property_option_id']);
        });

        Schema::create('utility_property', function (Blueprint $table) {
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_option_id')->constrained('property_options')->cascadeOnDelete();
            $table->primary(['property_id', 'property_option_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenity_property');
        Schema::dropIfExists('utility_property');
    }
};