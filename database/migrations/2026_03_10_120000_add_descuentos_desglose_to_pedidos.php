<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('descuento_cliente', 12, 2)->default(0);
            $table->decimal('descuento_tipo', 12, 2)->default(0);
            $table->decimal('descuento_producto', 12, 2)->default(0);
            $table->decimal('descuento_pago', 12, 2)->default(0);

            $table->decimal('porcentaje_descuento_c1', 5, 2)->default(0);
            $table->decimal('porcentaje_descuento_c2', 5, 2)->default(0);
            $table->decimal('porcentaje_descuento_c3', 5, 2)->default(0);
            $table->decimal('porcentaje_descuento_pago', 5, 2)->default(0);
        });

        Schema::table('pedido_items', function (Blueprint $table) {
            $table->decimal('descuento_cliente', 10, 2)->default(0);
            $table->decimal('descuento_tipo', 10, 2)->default(0);
            $table->decimal('descuento_producto', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('pedido_items', function (Blueprint $table) {
            $table->dropColumn([
                'descuento_cliente',
                'descuento_tipo',
                'descuento_producto',
            ]);
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'descuento_cliente',
                'descuento_tipo',
                'descuento_producto',
                'descuento_pago',
                'porcentaje_descuento_c1',
                'porcentaje_descuento_c2',
                'porcentaje_descuento_c3',
                'porcentaje_descuento_pago',
            ]);
        });
    }
};
