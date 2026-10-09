<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_options', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50);          
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();  
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['group', 'slug']);
            $table->index(['group', 'is_active', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_options');
    }
};