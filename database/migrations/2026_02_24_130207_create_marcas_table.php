<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('marcas', function (Blueprint $table) {
    $table->id();
    $table->text('descripcion_es')->nullable();
    $table->text('descripcion_en')->nullable();
    $table->boolean('estado')->default(true);
    $table->boolean('destacado')->default(false);
    $table->boolean('visible')->default(true);
    $table->string('orden', 10)->nullable();
    $table->foreignId('vehiculo_tipo_id')
          ->nullable()
          ->constrained('vehiculo_tipo')
          ->nullOnDelete();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marcas');
    }
};
