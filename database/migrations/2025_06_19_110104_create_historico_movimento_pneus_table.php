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
        Schema::create('historico_movimento_pneus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pneu_id')
                ->constrained('pneus')
                ->cascadeOnDelete();
            $table->foreignId('veiculo_id')
                ->constrained('veiculos')
                ->cascadeOnDelete();
            $table->date('data_movimento');
            $table->string('km_inicial');
            $table->string('km_final')
                ->nullable();
            $table->string('eixo');
            $table->string('posicao');
            $table->decimal('sulco_movimento', 5, 2)
                ->default(0.00);
            $table->string('tipo_movimento');
            $table->string('motivo')->nullable();
            $table->string('observacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_movimento_pneus');
    }
};
