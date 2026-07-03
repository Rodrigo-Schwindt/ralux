<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->decimal('descuento2', 5, 2)->default(0)->after('descuento');
            $table->decimal('descuento3', 5, 2)->default(0)->after('descuento2');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['descuento2', 'descuento3']);
        });
    }
};
