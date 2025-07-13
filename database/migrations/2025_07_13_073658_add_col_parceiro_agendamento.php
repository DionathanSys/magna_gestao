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
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->foreignId('parceiro_id')
                ->nullable()
                ->constrained('parceiros')
                ->nullOnDelete()
                ->after('ordem_servico_id');
            $table->string('posicao', 10)
                ->nullable()
                ->after('servico_id');
            $table->date('data_limite')
                ->nullable()
                ->after('data_agendamento');
            $table->date('data_realizado')
                ->nullable()
                ->after('data_limite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropForeign(['parceiro_id']);
            $table->dropColumn('parceiro_id');
            $table->dropColumn('posicao');
            $table->dropColumn('data_limite');
            $table->dropColumn('data_realizado');
        });
    }
};
