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
        Schema::table('indicadores', function (Blueprint $table) {
            $table->string('objetivo')
                ->nullable()
                ->change();
            $table->string('tipo_meta')
                ->nullable()
                ->after('objetivo');
        });

        Schema::table('resultados', function (Blueprint $table) {
            $table->decimal('objetivo', 12, 2)
                ->after('indicador_id')
                ->nullable();
            $table->decimal('resultado', 12, 2)
                ->after('objetivo')
                ->nullable();
            $table->decimal('pontuacao_obtida', 12, 4)
                ->change();
            $table->dropColumn('pontuacao_maxima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultados', function (Blueprint $table) {
            $table->dropColumn('objetivo');
            $table->dropColumn('resultado');

            $table->decimal('pontuacao_obtida', 10, 2)
                ->change();

            $table->decimal('pontuacao_maxima', 10, 2)
                ->after('pontuacao_obtida')
                ->default(0);

        });

        Schema::table('indicadores', function (Blueprint $table) {
            $table->string('objetivo')
                ->nullable(false)
                ->change();
            $table->dropColumn('tipo_meta');
        });
    }
};
