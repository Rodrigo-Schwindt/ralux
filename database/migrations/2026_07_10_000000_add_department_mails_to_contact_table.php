<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact', function (Blueprint $table) {
            if (!Schema::hasColumn('contact', 'mail_comercial')) {
                $table->string('mail_comercial')->nullable()->after('mail_adm');
            }
            if (!Schema::hasColumn('contact', 'mail_tecnico')) {
                $table->string('mail_tecnico')->nullable()->after('mail_comercial');
            }
            if (!Schema::hasColumn('contact', 'mail_compras')) {
                $table->string('mail_compras')->nullable()->after('mail_tecnico');
            }
            if (!Schema::hasColumn('contact', 'mail_logistica')) {
                $table->string('mail_logistica')->nullable()->after('mail_compras');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact', function (Blueprint $table) {
            $table->dropColumn(['mail_comercial', 'mail_tecnico', 'mail_compras', 'mail_logistica']);
        });
    }
};
