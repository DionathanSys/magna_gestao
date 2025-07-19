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
        Schema::create('planos_manutencao_veiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plano_preventivo_id')
                ->nullable()
                ->constrained('planos_preventivo')
                ->nullOnDelete();
            $table->foreignId('veiculo_id')
                ->constrained('veiculos')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos_manutencao_veiculo');
    }
};
