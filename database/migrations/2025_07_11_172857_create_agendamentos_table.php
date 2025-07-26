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
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')
                ->constrained('veiculos');
            $table->foreignId('ordem_servico_id')
                ->nullable()
                ->constrained('ordens_servico')
                ->nullOnDelete();
            $table->date('data_agendamento')
                ->nullable();
            $table->foreignId('servico_id')
                ->constrained('servicos')
                ->cascadeOnDelete();
            $table->string('status', 20);
            $table->string('observacao', 255)
                ->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
