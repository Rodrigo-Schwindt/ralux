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
Schema::create('vehiculo_tipo_producto', function (Blueprint $table) {
    $table->foreignId('vehiculo_tipo_id')->constrained('vehiculo_tipo')->cascadeOnDelete();
    $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
    $table->primary(['vehiculo_tipo_id', 'producto_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculo_tipo_producto');
    }
};
