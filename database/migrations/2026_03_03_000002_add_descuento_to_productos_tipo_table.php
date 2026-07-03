<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos_tipo', function (Blueprint $table) {
            $table->decimal('descuento', 5, 2)->default(0)->after('orden');
        });
    }

    public function down(): void
    {
        Schema::table('productos_tipo', function (Blueprint $table) {
            $table->dropColumn('descuento');
        });
    }
};
