<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->string('codigo_ralux')->unique();
            $table->text('descripcion_es')->nullable();
            $table->text('descripcion_en')->nullable();
            $table->decimal('voltaje', 10, 2)->nullable();
            $table->decimal('amperaje', 10, 2)->nullable();
            $table->decimal('terminales', 10, 2)->nullable();
            $table->boolean('soporte')->default(false);

            for ($i = 1; $i <= 10; $i++) {
                $table->string("caract_{$i}")->nullable();
                $table->string("valor_{$i}")->nullable();
            }

            $table->decimal('precio', 12, 2)->nullable();
            $table->date('periodo_desde')->nullable();
            $table->date('periodo_hasta')->nullable();
            $table->string('safe_url')->nullable();
            $table->string('pdf')->nullable();
            $table->string('sonido')->nullable();
            $table->string('video')->nullable();
            $table->boolean('estado')->default(true);
            $table->string('imagen_diagrama')->nullable();
            $table->boolean('destacado')->default(false);
            $table->boolean('visible')->default(true);
            $table->string('orden', 10)->nullable();

            $table->foreignId('producto_tipo_id')
                  ->nullable()
                  ->constrained('productos_tipo')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};