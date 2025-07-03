<?php

use App\Models\CargaViagem;
use App\Models\HistoricoMovimentoPneu;
use App\Models\Integrado;
use App\Models\Pneu;
use App\Models\PneuPosicaoVeiculo;
use App\Models\Recapagem;
use App\Models\Viagem;
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
        Schema::table('viagens', function (Blueprint $table) {
            $table->foreignIdFor(Viagem::class, 'created_by')
                ->nullable()
                ->constrained('viagens')
                ->nullOnDelete();
            $table->foreignIdFor(Viagem::class, 'updated_by')
                ->nullable()
                ->constrained('viagens')
                ->nullOnDelete();
            $table->foreignIdFor(Viagem::class, 'checked_by')
                ->nullable()
                ->constrained('viagens')
                ->nullOnDelete();
        });

        Schema::table('cargas_viagem', function (Blueprint $table) {
            $table->foreignIdFor(CargaViagem::class, 'created_by')
                ->nullable()
                ->constrained('cargas_viagem')
                ->nullOnDelete();
            $table->foreignIdFor(CargaViagem::class, 'updated_by')
                ->nullable()
                ->constrained('cargas_viagem')
                ->nullOnDelete();
        });

        Schema::table('integrados', function (Blueprint $table) {
            $table->foreignIdFor(Integrado::class, 'created_by')
                ->nullable()
                ->constrained('integrados')
                ->nullOnDelete();
            $table->foreignIdFor(Integrado::class, 'updated_by')
                ->nullable()
                ->constrained('integrados')
                ->nullOnDelete();
        });

        Schema::table('pneus', function (Blueprint $table) {
            $table->foreignIdFor(Pneu::class, 'created_by')
                ->nullable()
                ->constrained('pneus')
                ->nullOnDelete();
            $table->foreignIdFor(Pneu::class, 'updated_by')
                ->nullable()
                ->constrained('pneus')
                ->nullOnDelete();
        });

        Schema::table('historico_movimento_pneus', function (Blueprint $table) {
            $table->foreignIdFor(HistoricoMovimentoPneu::class, 'created_by')
                ->nullable()
                ->constrained('historico_movimento_pneus')
                ->nullOnDelete();
            $table->foreignIdFor(HistoricoMovimentoPneu::class, 'updated_by')
                ->nullable()
                ->constrained('historico_movimento_pneus')
                ->nullOnDelete();
        });

        Schema::table('recapagens', function (Blueprint $table) {
            $table->foreignIdFor(Recapagem::class, 'created_by')
                ->nullable()
                ->constrained('recapagens')
                ->nullOnDelete();
            $table->foreignIdFor(Recapagem::class, 'updated_by')
                ->nullable()
                ->constrained('recapagens')
                ->nullOnDelete();
        });

        Schema::table('pneu_posicao_veiculo', function (Blueprint $table) {
            $table->foreignIdFor(PneuPosicaoVeiculo::class, 'created_by')
                ->nullable()
                ->constrained('pneu_posicao_veiculo')
                ->nullOnDelete();
            $table->foreignIdFor(Recapagem::class, 'updated_by')
                ->nullable()
                ->constrained('pneu_posicao_veiculo')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viagens', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['checked_by']);
            $table->dropColumn(['created_by', 'updated_by', 'checked_by']);
        });

        Schema::table('cargas_viagem', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });
        Schema::table('integrados', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });
        Schema::table('pneus', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });
        Schema::table('historico_movimento_pneus', function (Blueprint $table)
        {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });
        Schema::table('recapagens', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });
        Schema::table('pneu_posicao_veiculo', function (Blueprint $table)
        {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
