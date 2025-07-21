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
        Schema::create('viagem_complementos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viagem_id')
                ->constrained('viagens')
                ->cascadeOnDelete();
            $table->string('numero_viagem');
            $table->string('documento_transporte')  
                ->nullable();
            $table->foreignId('integrado_id')
                ->nullable()
                ->constrained('integrados')
                ->nullOnDelete();
            $table->decimal('km_rodado', 10, 2)
                ->default(0);
            $table->decimal('km_pago', 10, 2)
                ->default(0);
            $table->decimal('km_divergencia', 10, 2)
                ->default(0);
            $table->decimal('km_cobrar', 10, 2)
                ->default(0);
            $table->string('motivo_divergencia')
                ->nullable();
            $table->string('data_competencia');
            $table->boolean('conferido')
                ->default(false);
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viagem_complementos');
    }
};
