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
        Schema::create('itens_ordem_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_servico_id')
                ->constrained('ordens_servico')
                ->cascadeOnDelete();
            $table->foreignId('servico_id')
                ->constrained('servicos')
                ->cascadeOnDelete();
            $table->string('posicao', 10)
                ->nullable();
            $table->string('observacao', 255)
                ->nullable();
            $table->foreignId('tecnico_manutencao_id')
                ->nullable()
                ->constrained('tecnicos_manutencao')
                ->nullOnDelete();
            $table->string('status', 20);
            $table->foreignId('created_by')
                ->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itens_ordem_servico');
    }
};
