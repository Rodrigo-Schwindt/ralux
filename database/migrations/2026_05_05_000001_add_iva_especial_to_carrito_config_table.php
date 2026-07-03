<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carrito_config', function (Blueprint $table) {
            $table->decimal('iva_especial', 5, 2)->default(10.50)->after('iva');
            $table->json('iva_prefijos_especiales')->nullable()->after('iva_especial');
        });
    }

    public function down(): void
    {
        Schema::table('carrito_config', function (Blueprint $table) {
            $table->dropColumn(['iva_especial', 'iva_prefijos_especiales']);
        });
    }
};
