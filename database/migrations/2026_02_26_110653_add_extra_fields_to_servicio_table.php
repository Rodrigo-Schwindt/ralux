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
    Schema::table('servicio', function (Blueprint $table) {
        $table->string('title_s')->nullable()->after('title');
        $table->text('description_s')->nullable()->after('description_1');
        $table->string('image_s')->nullable()->after('image');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio', function (Blueprint $table) {
            //
        });
    }
};
