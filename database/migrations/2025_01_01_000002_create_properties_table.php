<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_category_id')->constrained('property_categories')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code', 5);
            $table->enum('unit_type', ['apartment','apto','suite','studio','loft','penthouse','room'])->default('apartment');
            $table->text('description')->nullable();
            $table->text('restrictions')->nullable();
            $table->boolean('pet_friendly')->default(false);
            $table->unsignedInteger('max_pets')->default(0);
            $table->enum('status', ['active','inactive'])->default('active');
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();

            $table->index(['status', 'property_category_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('properties'); }
};