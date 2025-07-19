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
            $table->foreignId('plano_preventivo_id')
                ->nullable()
                ->constrained('planos_preventivo')
                ->nullOnDelete()
                ->after('servico_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itens_ordem_servico', function (Blueprint $table) {
            $table->dropForeign(['plano_preventivo_id']);
            $table->dropColumn('plano_preventivo_id');
        });
    }
};
