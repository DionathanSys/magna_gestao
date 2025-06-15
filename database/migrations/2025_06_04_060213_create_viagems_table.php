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
        Schema::create('viagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->constrained('veiculos');
            $table->string('numero_viagem', 50);
            $table->string('numero_custo_frete', 50)->nullable();
            $table->string('documento_transporte', 50)->nullable();
            $table->string('tipo_viagem')->nullable(); // Simples, Composta
            $table->decimal('valor_frete', 14)->default(0);
            $table->decimal('valor_cte', 14)->default(0);
            $table->decimal('valor_nfs', 14)->default(0);
            $table->decimal('valor_icms', 14)->default(0);
            $table->decimal('km_rodado', 10, 2)->default(0);
            $table->decimal('km_pago', 10, 2)->default(0);
            $table->decimal('km_divergencia', 10, 2)->default(0);
            $table->decimal('km_cadastro', 10, 2)->default(0);
            $table->decimal('km_rota_corrigido', 10, 2)->default(0);
            $table->decimal('km_pago_excedente', 10, 2)->default(0);
            $table->decimal('km_rodado_excedente', 10, 2)->default(0);
            $table->decimal('km_cobrar', 10, 2)->default(0);
            $table->decimal('peso', 10)->default(0);
            $table->decimal('entregas', 10)->default(1);
            $table->date('data_competencia');
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim');
            $table->boolean('conferido')->default(false);
            $table->json('divergencias')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viagens');
    }
};
