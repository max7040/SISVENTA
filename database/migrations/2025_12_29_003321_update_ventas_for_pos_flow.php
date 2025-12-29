<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {

            // Estado: abierta / cerrada
            $table->string('estado')->default('abierta')->after('cliente_id');

            // Documento ingresado al finalizar (sin SUNAT)
            $table->string('cliente_documento', 11)->nullable()->after('estado');

            // Para permitir "borrador": estos campos deben permitir NULL
            $table->string('serie')->nullable()->change();
            $table->unsignedInteger('numero')->nullable()->change();
            $table->timestamp('fecha')->nullable()->change();

            // total por defecto 0 en borrador
            $table->decimal('total', 10, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['estado', 'cliente_documento']);

            $table->string('serie')->nullable(false)->change();
            $table->unsignedInteger('numero')->nullable(false)->change();
            $table->timestamp('fecha')->nullable(false)->change();
        });
    }
};
