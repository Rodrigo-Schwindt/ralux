<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('orden')->nullable();
            $table->boolean('visible')->default(1);
            $table->string('pdf')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nombre_tabla');
    }
};