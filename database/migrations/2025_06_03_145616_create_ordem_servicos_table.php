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
        Schema::create('ordens_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->constrained('veiculos');
            $table->decimal('quilometragem', 10, 2)->nullable(); // quilometragem do veiculo no momento da abertura
            $table->string('tipo_manutencao', 50)->nullable(); // preventivo, corretivo, inspecao, etc.
            $table->date('data_abertura')->nullable();
            $table->date('data_fechamento')->nullable();
            $table->string('status', 20)->default('aberta'); // aberta, em_andamento, concluida, cancelada
            $table->string('status_sankhya', 20)->default('aberta'); // aberta, em_andamento, concluida, cancelada
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordens_servico');
    }
};
