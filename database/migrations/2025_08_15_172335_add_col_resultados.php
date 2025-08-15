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
        Schema::table('resultados', function (Blueprint $table) {
            $table->string('status')->after('periodo')->nullable();
            $table->renameColumn('pontuacao', 'pontuacao_obtida');
            $table->decimal('pontuacao_maxima', 10, 2)
                ->after('pontuacao_obtida')
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultados', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
