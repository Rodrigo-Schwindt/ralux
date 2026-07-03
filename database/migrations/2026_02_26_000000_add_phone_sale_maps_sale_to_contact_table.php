<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact', function (Blueprint $table) {
            if (!Schema::hasColumn('contact', 'phone_sale')) {
                $table->string('phone_sale')->nullable()->after('phone_amd');
            }
            if (!Schema::hasColumn('contact', 'maps_sale')) {
                $table->string('maps_sale')->nullable()->after('maps_adm');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact', function (Blueprint $table) {
            $table->dropColumn(['phone_sale', 'maps_sale']);
        });
    }
};
