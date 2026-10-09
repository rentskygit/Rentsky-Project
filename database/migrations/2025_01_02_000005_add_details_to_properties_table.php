<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('lease_term_id')->nullable()->after('property_category_id')
                  ->constrained('property_options')->nullOnDelete();
            $table->decimal('rent', 10, 2)->nullable()->after('lease_term_id');
            $table->decimal('deposit', 10, 2)->nullable()->after('rent');
            $table->date('available_date')->nullable()->after('deposit');

            $table->string('bathrooms', 10)->nullable()->after('unit_type');
            $table->unsignedInteger('square_feet')->nullable()->after('bathrooms');
            $table->string('virtual_tour_url', 500)->nullable()->after('square_feet');

            $table->foreignId('parking_type_id')->nullable()->after('virtual_tour_url')
                  ->constrained('property_options')->nullOnDelete();
            $table->string('parking_spot', 20)->nullable()->after('parking_type_id');
            $table->foreignId('cooling_system_id')->nullable()->after('parking_spot')
                  ->constrained('property_options')->nullOnDelete();
            $table->foreignId('heating_system_id')->nullable()->after('cooling_system_id')
                  ->constrained('property_options')->nullOnDelete();
            $table->foreignId('laundry_id')->nullable()->after('heating_system_id')
                  ->constrained('property_options')->nullOnDelete();

            $table->boolean('allow_cats')->default(false)->after('max_pets');
            $table->boolean('allow_small_dogs')->default(false)->after('allow_cats');
            $table->unsignedInteger('small_dog_max_weight')->nullable()->after('allow_small_dogs');
            $table->boolean('allow_large_dogs')->default(false)->after('small_dog_max_weight');

            $table->unsignedInteger('income_to_rent_ratio')->nullable()->after('allow_large_dogs');
            $table->boolean('allow_eviction_older_than_5_years')->default(false)->after('income_to_rent_ratio');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lease_term_id');
            $table->dropConstrainedForeignId('parking_type_id');
            $table->dropConstrainedForeignId('cooling_system_id');
            $table->dropConstrainedForeignId('heating_system_id');
            $table->dropConstrainedForeignId('laundry_id');

            $table->dropColumn([
                'rent', 'deposit', 'available_date',
                'bathrooms', 'square_feet', 'virtual_tour_url', 'parking_spot',
                'allow_cats', 'allow_small_dogs', 'small_dog_max_weight', 'allow_large_dogs',
                'income_to_rent_ratio', 'allow_eviction_older_than_5_years',
            ]);
        });
    }
};