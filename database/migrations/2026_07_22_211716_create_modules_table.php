<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');               
            $table->string('icon')->nullable();    
            $table->string('route')->nullable();   
            $table->string('url')->nullable();     
            $table->string('route_pattern')->nullable();  
            $table->integer('order')->default(0);    
            $table->boolean('is_active')->default(true);
            $table->boolean('is_dashboard')->default(false); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('modules');
    }
};