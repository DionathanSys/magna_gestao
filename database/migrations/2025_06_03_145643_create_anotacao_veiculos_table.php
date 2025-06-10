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
        Schema::create('anotacoes_veiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->constrained('veiculos')->cascadeOnDelete();
            $table->decimal('quilometragem', 10, 2)->nullable();
            $table->foreignId('servico_id')->nullable()->constrained('servicos')->nullOnDelete();
            $table->foreignId('tecnico_manutencao_id')->nullable()->constrained('tecnicos_manutencao')->nullOnDelete();
            $table->foreignId('item_ordem_servico_id')->nullable()->constrained('itens_ordem_servico')->nullOnDelete();
            $table->string('tipo', 20);
            $table->date('data_referencia');
            $table->string('descricao', 255)->nullable();
            $table->string('status', 20);
            $table->string('prioridade', 20);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anotacoes_veiculo');
    }
};
