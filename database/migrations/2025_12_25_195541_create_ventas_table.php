<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained('clientes')
                ->nullOnDelete();

            // Facturación simulada
            $table->enum('tipo_comprobante', ['boleta', 'factura']);
            $table->string('serie', 4); // B001 o F001
            $table->unsignedInteger('numero'); // correlativo por serie

            $table->dateTime('fecha');
            $table->decimal('total', 10, 2)->default(0);

            $table->timestamps();

            // Para evitar duplicados de correlativo por serie:
            $table->unique(['serie', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
