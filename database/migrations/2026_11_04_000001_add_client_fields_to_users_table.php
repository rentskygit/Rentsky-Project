<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('assigned_adviser_id')
                  ->nullable()
                  ->after('is_super_admin')
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('property_of_interest_id')
                  ->nullable()
                  ->after('assigned_adviser_id')
                  ->constrained('properties')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_adviser_id');
            $table->dropConstrainedForeignId('property_of_interest_id');
        });
    }
};