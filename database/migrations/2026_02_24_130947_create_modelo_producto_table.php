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
Schema::create('modelo_producto', function (Blueprint $table) {
    $table->foreignId('modelo_id')->constrained('modelos')->cascadeOnDelete();
    $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
    $table->primary(['modelo_id', 'producto_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelo_producto');
    }
};
