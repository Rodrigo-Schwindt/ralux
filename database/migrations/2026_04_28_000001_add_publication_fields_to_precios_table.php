<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('precios', function (Blueprint $table) {
            $table->string('tipo')->default('propia')->after('archivo');
            $table->boolean('publicado')->default(false)->after('tipo');
            $table->timestamp('generado_at')->nullable()->after('publicado');
        });

        $ultimoPrecio = DB::table('precios')->latest('created_at')->first();

        if ($ultimoPrecio) {
            DB::table('precios')
                ->where('id', $ultimoPrecio->id)
                ->update(['publicado' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('precios', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'publicado', 'generado_at']);
        });
    }
};
