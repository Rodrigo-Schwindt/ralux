<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('voltaje', 100)->nullable()->change();
            $table->string('amperaje', 100)->nullable()->change();
            $table->string('terminales', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('voltaje', 10, 2)->nullable()->change();
            $table->decimal('amperaje', 10, 2)->nullable()->change();
            $table->decimal('terminales', 10, 2)->nullable()->change();
        });
    }
};
