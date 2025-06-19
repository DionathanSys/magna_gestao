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
        Schema::create('pneu_posicao_veiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pneu_id')
                ->nullable()
                ->constrained('pneus')
                ->cascadeOnDelete();
            $table->foreignId('veiculo_id')
                ->constrained('veiculos')
                ->cascadeOnDelete();
            $table->date('data_inicial');
            $table->string('km_inicial');
            $table->string('eixo');
            $table->string('posicao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pneu_posicao_veiculo');
    }
};
