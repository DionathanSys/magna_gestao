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
        Schema::table('itens_ordem_servico', function (Blueprint $table) {
            $table->dropForeign(['tecnico_manutencao_id']);
            $table->dropColumn('tecnico_manutencao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itens_ordem_servico', function (Blueprint $table) {
            $table->foreignId('tecnico_manutencao_id')->nullable()->constrained('tecnicos_manutencao')->nullOnDelete();
        });
    }
};
